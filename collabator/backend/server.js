const express = require('express');
const WebSocket = require('ws');
const http = require('http');
const cors = require('cors');
const { v4: uuidv4 } = require('uuid');
const axios = require('axios');
const path = require('path');
const db = require('./database');
const { spawn } = require('child_process');
const fs = require('fs');
const os = require('os');

const app = express();
const server = http.createServer(app);
const wss = new WebSocket.Server({ server });

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static(path.join(__dirname, '../frontend')));

// Favicon route (prevent 404 errors)
app.get('/favicon.ico', (req, res) => {
  res.setHeader('Content-Type', 'image/x-icon');
  res.send(Buffer.from('AAABAAEAEBAQAAEABACwBgAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAgAAAAAAAAAAAAAAAEAAAAAAAAAAAAAAA/4QAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD//wAA//8AAP7/AAD8fwAA8H8AAPgfAADgHwAA8H8AAPgfAAD+HwAA//8AAP//AAD//wAA', 'hex'));
});

const PORT = process.env.PORT || 3000;

// Judge0 API Configuration
const JUDGE0_API = 'https://api.judge0.com';
const LANGUAGE_IDS = {
  javascript: 63,
  python: 71,
  php: 68,
  typescript: 74,
  html: 89,
  css: 50,
  sql: 82
};

// Store active rooms and connections
const activeRooms = new Map();
const userConnections = new Map(); // userId -> WebSocket
const userPresence = new Map(); // userId -> { username, roomId, color }

// Generate color for user cursor
function generateUserColor() {
  const colors = [
    '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
    '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B88B', '#A8E6CF'
  ];
  return colors[Math.floor(Math.random() * colors.length)];
}

// WebSocket connection handling
wss.on('connection', (ws) => {
  const userId = uuidv4();
  console.log(`[WS] New connection: ${userId}`);

  ws.on('message', (data) => {
    try {
      const message = JSON.parse(data);
      handleWebSocketMessage(ws, userId, message);
    } catch (err) {
      console.error('Error parsing WebSocket message:', err);
    }
  });

  ws.on('close', () => {
    handleUserDisconnect(userId);
  });

  ws.on('error', (err) => {
    console.error(`[WS] Error for ${userId}:`, err.message);
  });
});

function handleWebSocketMessage(ws, userId, message) {
  const { type, roomId, username, content, language, cursorPosition, line } = message;

  switch (type) {
    case 'join':
      handleUserJoin(ws, userId, roomId, username);
      break;
    case 'code-change':
      broadcastToRoom(roomId, {
        type: 'code-change',
        userId,
        content,
        language
      }, userId);
      saveCodeState(roomId, content, language);
      break;
    case 'cursor':
      broadcastToRoom(roomId, {
        type: 'cursor',
        userId,
        username,
        cursorPosition,
        line
      }, userId);
      break;
    case 'chat':
      handleChatMessage(roomId, username, message.text);
      break;
  }
}

function handleUserJoin(ws, userId, roomId, username) {
  // Store connection
  userConnections.set(userId, ws);
  userPresence.set(userId, {
    username,
    roomId,
    color: generateUserColor()
  });

  // Create or get room
  let room = activeRooms.get(roomId);
  if (!room) {
    room = { users: new Map() };
    activeRooms.set(roomId, room);
    // Insert room into DB if new
    db.run(
      'INSERT OR IGNORE INTO rooms (id, name) VALUES (?, ?)',
      [roomId, roomId],
      (err) => {
        if (err) console.error('Error inserting room:', err);
      }
    );
  }

  room.users.set(userId, {
    username,
    color: userPresence.get(userId).color
  });

  // Send joined confirmation
  ws.send(JSON.stringify({
    type: 'joined',
    userId,
    users: Array.from(room.users.entries()).map(([id, user]) => ({
      userId: id,
      username: user.username,
      color: user.color
    }))
  }));

  // Broadcast presence to all in room
  broadcastToRoom(roomId, {
    type: 'user-joined',
    userId,
    username,
    color: userPresence.get(userId).color,
    users: Array.from(room.users.entries()).map(([id, user]) => ({
      userId: id,
      username: user.username,
      color: user.color
    }))
  });

  // Send current code state
  db.get(
    'SELECT content, language FROM room_code_state WHERE room_id = ?',
    [roomId],
    (err, row) => {
      if (err) {
        console.error('Error fetching code state:', err);
      } else if (row) {
        ws.send(JSON.stringify({
          type: 'code-state',
          content: row.content,
          language: row.language
        }));
      }
    }
  );

  console.log(`[JOIN] ${username} joined ${roomId}`);
}

function handleUserDisconnect(userId) {
  const presence = userPresence.get(userId);
  if (presence) {
    const { roomId, username } = presence;
    const room = activeRooms.get(roomId);
    if (room) {
      room.users.delete(userId);
      broadcastToRoom(roomId, {
        type: 'user-left',
        userId,
        username,
        users: Array.from(room.users.entries()).map(([id, user]) => ({
          userId: id,
          username: user.username,
          color: user.color
        }))
      });
      if (room.users.size === 0) {
        activeRooms.delete(roomId);
      }
    }
  }
  userConnections.delete(userId);
  userPresence.delete(userId);
  console.log(`[DISCONNECT] User ${userId} disconnected`);
}

function broadcastToRoom(roomId, message, excludeUserId = null) {
  const room = activeRooms.get(roomId);
  if (!room) return;

  const payload = JSON.stringify(message);
  room.users.forEach((user, userId) => {
    if (excludeUserId && userId === excludeUserId) return;
    const ws = userConnections.get(userId);
    if (ws && ws.readyState === WebSocket.OPEN) {
      ws.send(payload);
    }
  });
}

function saveCodeState(roomId, content, language) {
  db.run(
    `INSERT INTO room_code_state (room_id, content, language, updated_at)
     VALUES (?, ?, ?, CURRENT_TIMESTAMP)
     ON CONFLICT(room_id) DO UPDATE SET
       content = excluded.content,
       language = excluded.language,
       updated_at = CURRENT_TIMESTAMP`,
    [roomId, content, language],
    (err) => {
      if (err) console.error('Error saving code state:', err);
    }
  );
}

function handleChatMessage(roomId, username, text) {
  const messageId = uuidv4();
  db.run(
    `INSERT INTO messages (id, room_id, username, message, created_at)
     VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)`,
    [messageId, roomId, username, text],
    (err) => {
      if (err) {
        console.error('Error saving chat message:', err);
      } else {
        broadcastToRoom(roomId, {
          type: 'chat',
          username,
          text,
          timestamp: new Date().toISOString()
        });
      }
    }
  );
}

// REST API Endpoints

// Execute code locally without external APIs
app.post('/api/execute', async (req, res) => {
  try {
    const { language, code } = req.body;

    if (!language || !code) {
      return res.status(400).json({ error: 'Language and code required' });
    }

    console.log(`[EXECUTE] ${language}: ${code.substring(0, 50)}...`);

    let stdout = '';
    let stderr = '';
    let status = 'Executed';

    try {
      // Execute based on language
      switch (language) {
        case 'javascript':
          ({ stdout, stderr, status } = await executeJavaScript(code));
          break;
        case 'python':
          ({ stdout, stderr, status } = await executePython(code));
          break;
        case 'php':
          ({ stdout, stderr, status } = await executePHP(code));
          break;
        default:
          return res.status(400).json({ error: `Unsupported language: ${language}` });
      }

      return res.json({
        stdout: stdout,
        stderr: stderr,
        time: 0.001,
        memory: 0,
        status: status
      });
    } catch (err) {
      console.error('Execution error:', err.message);
      return res.status(500).json({ 
        error: 'Execution failed', 
        details: err.message
      });
    }
  } catch (err) {
    console.error('API error:', err.message);
    return res.status(500).json({ error: 'Server error', details: err.message });
  }
});

// Execute JavaScript locally
function executeJavaScript(code) {
  return new Promise((resolve, reject) => {
    try {
      const logs = [];
      const originalLog = console.log;
      console.log = (...args) => {
        logs.push(args.map(arg => String(arg)).join(' '));
        originalLog(...args);
      };

      eval(`(function() { ${code} })()`);
      
      console.log = originalLog;

      resolve({
        stdout: logs.join('\n'),
        stderr: '',
        status: 'Executed'
      });
    } catch (e) {
      console.log = originalLog;
      resolve({
        stdout: '',
        stderr: e.message,
        status: 'Runtime Error'
      });
    }
  });
}

// Execute Python code with UTF-8 encoding support
function executePython(code) {
  return new Promise((resolve, reject) => {
    const tempFile = path.join(os.tmpdir(), `script_${Date.now()}.py`);
    
    try {
      // Prepend UTF-8 encoding declaration
      const pythonCode = `# -*- coding: utf-8 -*-\nimport sys\nsys.stdout.reconfigure(encoding='utf-8')\n${code}`;
      fs.writeFileSync(tempFile, pythonCode, 'utf-8');
      
      console.log(`[PYTHON] Executing: ${tempFile}`);
      
      // Spawn Python process with UTF-8 environment
      const python = spawn('python', [tempFile], {
        timeout: 10000,
        maxBuffer: 1024 * 1024 * 10,
        env: {
          ...process.env,
          PYTHONIOENCODING: 'utf-8'
        }
      });

      let stdout = '';
      let stderr = '';

      python.stdout.on('data', (data) => {
        try {
          stdout += data.toString('utf-8');
        } catch (e) {
          stdout += data.toString('latin1');
        }
      });

      python.stderr.on('data', (data) => {
        try {
          stderr += data.toString('utf-8');
        } catch (e) {
          stderr += data.toString('latin1');
        }
      });

      python.on('close', (exitCode) => {
        // Clean up temp file
        try {
          fs.unlinkSync(tempFile);
        } catch (e) {}

        console.log(`[PYTHON] Exit code: ${exitCode}`);

        if (exitCode !== 0 && stderr) {
          resolve({
            stdout: stdout,
            stderr: stderr,
            status: 'Runtime Error'
          });
        } else if (exitCode === 0 || !stderr) {
          resolve({
            stdout: stdout,
            stderr: stderr,
            status: 'Executed'
          });
        } else {
          resolve({
            stdout: stdout,
            stderr: stderr || `Process exited with code ${exitCode}`,
            status: 'Runtime Error'
          });
        }
      });

      python.on('error', (err) => {
        try {
          fs.unlinkSync(tempFile);
        } catch (e) {}
        console.error('[PYTHON] Error:', err.message);
        resolve({
          stdout: '',
          stderr: `Python not found: ${err.message}. Please install Python 3.x`,
          status: 'Error'
        });
      });

      // Timeout handler
      setTimeout(() => {
        if (python.exitCode === null) {
          python.kill();
          try {
            fs.unlinkSync(tempFile);
          } catch (e) {}
          resolve({
            stdout: stdout,
            stderr: 'Timeout: Code execution exceeded 10 seconds',
            status: 'Timeout'
          });
        }
      }, 11000);
    } catch (err) {
      try {
        fs.unlinkSync(tempFile);
      } catch (e) {}
      console.error('[PYTHON] Error:', err.message);
      resolve({
        stdout: '',
        stderr: err.message,
        status: 'Error'
      });
    }
  });
}

// Execute PHP code
function executePHP(code) {
  return new Promise((resolve, reject) => {
    const tempFile = path.join(os.tmpdir(), `script_${Date.now()}.php`);
    
    try {
      // Wrap code in PHP tags if not present
      const phpCode = code.includes('<?php') ? code : `<?php\n${code}`;
      fs.writeFileSync(tempFile, phpCode);
      
      // Spawn PHP process
      const php = spawn('php', [tempFile], {
        timeout: 5000,
        maxBuffer: 1024 * 1024 * 10
      });

      let stdout = '';
      let stderr = '';

      php.stdout.on('data', (data) => {
        stdout += data.toString();
      });

      php.stderr.on('data', (data) => {
        stderr += data.toString();
      });

      php.on('close', (code) => {
        try {
          fs.unlinkSync(tempFile);
        } catch (e) {}

        if (code !== 0) {
          resolve({
            stdout: stdout,
            stderr: stderr || `Process exited with code ${code}`,
            status: 'Runtime Error'
          });
        } else {
          resolve({
            stdout: stdout,
            stderr: stderr,
            status: 'Executed'
          });
        }
      });

      php.on('error', (err) => {
        try {
          fs.unlinkSync(tempFile);
        } catch (e) {}
        resolve({
          stdout: '',
          stderr: `PHP not found or failed to execute: ${err.message}. Try installing PHP or use JavaScript instead.`,
          status: 'Error'
        });
      });
    } catch (err) {
      try {
        fs.unlinkSync(tempFile);
      } catch (e) {}
      resolve({
        stdout: '',
        stderr: err.message,
        status: 'Error'
      });
    }
  });
}

// Create snapshot
app.post('/api/rooms/:roomId/snapshots', (req, res) => {
  const { roomId } = req.params;
  const { content, language, username } = req.body;

  const snapshotId = uuidv4();
  db.run(
    `INSERT INTO snapshots (id, room_id, content, language, created_by, created_at)
     VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)`,
    [snapshotId, roomId, content, language, username],
    (err) => {
      if (err) {
        return res.status(500).json({ error: err.message });
      }
      res.json({ snapshotId, createdAt: new Date().toISOString() });
    }
  );
});

// Get snapshots
app.get('/api/rooms/:roomId/snapshots', (req, res) => {
  const { roomId } = req.params;
  db.all(
    `SELECT id, content, language, created_by, created_at
     FROM snapshots
     WHERE room_id = ?
     ORDER BY created_at DESC`,
    [roomId],
    (err, rows) => {
      if (err) {
        return res.status(500).json({ error: err.message });
      }
      res.json(rows || []);
    }
  );
});

// Restore snapshot
app.post('/api/rooms/:roomId/snapshots/:snapshotId/restore', (req, res) => {
  const { roomId, snapshotId } = req.params;
  
  db.get(
    `SELECT content, language FROM snapshots WHERE id = ? AND room_id = ?`,
    [snapshotId, roomId],
    (err, snapshot) => {
      if (err) {
        return res.status(500).json({ error: err.message });
      }
      if (!snapshot) {
        return res.status(404).json({ error: 'Snapshot not found' });
      }

      saveCodeState(roomId, snapshot.content, snapshot.language);
      
      // Broadcast to all users in room
      broadcastToRoom(roomId, {
        type: 'code-restored',
        content: snapshot.content,
        language: snapshot.language
      });

      res.json({ success: true });
    }
  );
});

// Get room info
app.get('/api/rooms/:roomId', (req, res) => {
  const { roomId } = req.params;
  db.get(
    'SELECT * FROM rooms WHERE id = ?',
    [roomId],
    (err, room) => {
      if (err) {
        return res.status(500).json({ error: err.message });
      }
      if (!room) {
        return res.status(404).json({ error: 'Room not found' });
      }
      res.json(room);
    }
  );
});

// Get messages
app.get('/api/rooms/:roomId/messages', (req, res) => {
  const { roomId } = req.params;
  db.all(
    `SELECT username, message, created_at
     FROM messages
     WHERE room_id = ?
     ORDER BY created_at ASC
     LIMIT 100`,
    [roomId],
    (err, rows) => {
      if (err) {
        return res.status(500).json({ error: err.message });
      }
      res.json(rows || []);
    }
  );
});

// Start server
server.listen(PORT, () => {
  console.log(`
╔════════════════════════════════════════╗
║  Collaborative Code Editor             ║
║  Server running on http://localhost:${PORT}   ║
║  Open your browser to start editing    ║
╚════════════════════════════════════════╝
  `);
});
