// ==================== GLOBALS ====================
let editor;
let ws;
let userId;
let roomId;
let username;
let currentLanguage = 'javascript';
let lastCodeContent = '';
let codeChangeTimeout;
let autoSnapshotTimeout;
let remoteCursors = new Map();
let userPresence = new Map();

// Language map for Monaco Editor
const languageMap = {
    javascript: 'javascript',
    python: 'python',
    php: 'php',
    typescript: 'typescript',
    html: 'html',
    css: 'css',
    sql: 'sql'
};

// ==================== INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', () => {
    const joinForm = document.getElementById('joinForm');
    joinForm.addEventListener('submit', handleJoinRoom);
});

// ==================== LANDING PAGE ====================
function handleJoinRoom(e) {
    e.preventDefault();
    
    username = document.getElementById('username').value.trim();
    let room = document.getElementById('roomCode').value.trim();
    
    if (!username) {
        alert('Please enter a username');
        return;
    }
    
    if (!room) {
        room = generateRoomCode();
    }
    
    roomId = room;
    showEditor();
    connectWebSocket();
}

function generateRoomCode() {
    return 'room_' + Math.random().toString(36).substr(2, 9);
}

// ==================== PAGE TRANSITIONS ====================
function showEditor() {
    document.getElementById('landingPage').style.display = 'none';
    document.getElementById('editorPage').style.display = 'flex';
    document.getElementById('roomName').textContent = roomId;
    initializeMonaco();
}

// ==================== MONACO EDITOR ====================
function initializeMonaco() {
    require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.44.0/min/vs' } });
    
    require(['vs/editor/editor.main'], function() {
        editor = monaco.editor.create(document.getElementById('monaco-editor'), {
            value: '// Welcome to Collaborative Code Editor!\n// Start typing...',
            language: languageMap[currentLanguage],
            theme: 'vs-dark',
            fontSize: 14,
            fontFamily: 'Consolas, "Courier New", monospace',
            tabSize: 2,
            insertSpaces: true,
            wordWrap: 'on',
            automaticLayout: true,
            scrollBeyondLastLine: false,
            minimap: { enabled: true, side: 'right' },
            quickSuggestions: { other: true, comments: false, strings: false },
            wordBasedSuggestions: 'off'
        });

        // Code change event
        editor.onDidChangeModelContent(() => {
            handleCodeChange();
        });

        // Cursor position event
        editor.onDidChangeCursorPosition((e) => {
            sendCursorPosition(e.position);
        });

        setupUIEventListeners();
    });
}

function handleCodeChange() {
    const content = editor.getValue();
    
    if (content === lastCodeContent) return;
    lastCodeContent = content;

    // Debounce broadcast
    clearTimeout(codeChangeTimeout);
    codeChangeTimeout = setTimeout(() => {
        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.send(JSON.stringify({
                type: 'code-change',
                roomId,
                content,
                language: currentLanguage
            }));
        }
    }, 50);

    // Auto-snapshot every 60 seconds
    clearTimeout(autoSnapshotTimeout);
    autoSnapshotTimeout = setTimeout(() => {
        createSnapshot(true);
    }, 60000);
}

function sendCursorPosition(position) {
    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'cursor',
            roomId,
            username,
            cursorPosition: { column: position.column, lineNumber: position.lineNumber },
            line: editor.getModel().getLineContent(position.lineNumber)
        }));
    }
}

// ==================== WEBSOCKET ====================
function connectWebSocket() {
    const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
    const wsUrl = `${protocol}//${window.location.host}`;
    
    ws = new WebSocket(wsUrl);

    ws.onopen = () => {
        console.log('[WS] Connected');
        ws.send(JSON.stringify({
            type: 'join',
            roomId,
            username
        }));
    };

    ws.onmessage = (e) => {
        try {
            const message = JSON.parse(e.data);
            handleWebSocketMessage(message);
        } catch (err) {
            console.error('Error parsing message:', err);
        }
    };

    ws.onerror = (err) => {
        console.error('[WS] Error:', err);
    };

    ws.onclose = () => {
        console.log('[WS] Disconnected');
        // Reconnect after 2 seconds
        setTimeout(connectWebSocket, 2000);
    };
}

function handleWebSocketMessage(message) {
    const { type } = message;

    switch (type) {
        case 'joined':
            userId = message.userId;
            updateUserPresence(message.users);
            break;
        case 'code-change':
            if (message.userId !== userId && editor) {
                const currentPos = editor.getPosition();
                editor.setValue(message.content);
                editor.setPosition(currentPos);
                lastCodeContent = message.content;
                if (message.language) {
                    currentLanguage = message.language;
                    updateLanguageSelector();
                }
            }
            break;
        case 'cursor':
            displayRemoteCursor(message);
            break;
        case 'user-joined':
            updateUserPresence(message.users);
            addChatMessage('SYSTEM', `${message.username} joined the room`);
            break;
        case 'user-left':
            updateUserPresence(message.users);
            addChatMessage('SYSTEM', `${message.username} left the room`);
            break;
        case 'chat':
            addChatMessage(message.username, message.text);
            break;
        case 'code-state':
            if (editor) {
                editor.setValue(message.content);
                lastCodeContent = message.content;
                currentLanguage = message.language;
                updateLanguageSelector();
            }
            break;
        case 'code-restored':
            if (editor) {
                editor.setValue(message.content);
                lastCodeContent = message.content;
                currentLanguage = message.language;
                updateLanguageSelector();
                addChatMessage('SYSTEM', 'Code snapshot restored');
            }
            break;
    }
}

// ==================== USER PRESENCE ====================
function updateUserPresence(users) {
    userPresence.clear();
    users.forEach(user => {
        userPresence.set(user.userId, user);
    });

    updateUserAvatars();
    document.getElementById('usersCount').textContent = `${users.length} user${users.length !== 1 ? 's' : ''}`;
}

function updateUserAvatars() {
    const avatarsContainer = document.getElementById('userAvatars');
    avatarsContainer.innerHTML = '';

    userPresence.forEach((user, userId) => {
        const avatar = document.createElement('div');
        avatar.className = 'user-avatar';
        avatar.style.backgroundColor = user.color;
        avatar.textContent = user.username.charAt(0).toUpperCase();
        avatar.title = user.username;
        avatarsContainer.appendChild(avatar);
    });
}

// ==================== REMOTE CURSORS ====================
function displayRemoteCursor(message) {
    const { userId: remoteUserId, username: remoteName, cursorPosition, color } = message;

    if (remoteUserId === userId) return;

    // Remove old cursor
    const oldCursor = document.querySelector(`[data-user-id="${remoteUserId}"]`);
    if (oldCursor) oldCursor.remove();

    if (!cursorPosition) return;

    const cursor = document.createElement('div');
    cursor.className = 'remote-cursor';
    cursor.setAttribute('data-user-id', remoteUserId);
    cursor.style.backgroundColor = color;

    const label = document.createElement('div');
    label.className = 'remote-cursor-label';
    label.style.backgroundColor = color;
    label.textContent = remoteName;

    cursor.appendChild(label);
    document.getElementById('remoteCursors').appendChild(cursor);

    // Position cursor using editor layout
    const editorLayout = editor.getLayoutInfo();
    const line = cursorPosition.lineNumber - 1;
    const column = cursorPosition.column - 1;

    const coordinates = editor.getTopForLineNumber(cursorPosition.lineNumber);
    const charWidth = editor.getOption(monaco.editor.EditorOption.fontInfo).typicalHalfwidthCharacterWidth;
    const left = charWidth * column + editorLayout.contentLeft;

    cursor.style.top = (coordinates + editor.getOption(monaco.editor.EditorOption.lineHeight) / 2 - 10) + 'px';
    cursor.style.left = left + 'px';

    // Remove cursor after 5 seconds of inactivity
    if (cursor.timeout) clearTimeout(cursor.timeout);
    cursor.timeout = setTimeout(() => cursor.remove(), 5000);
}

// ==================== CHAT ====================
function addChatMessage(sender, text) {
    const messagesContainer = document.getElementById('chatMessages');
    const messageEl = document.createElement('div');
    messageEl.className = 'chat-message';

    const time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

    if (sender === 'SYSTEM') {
        messageEl.innerHTML = `
            <div class="chat-message-header">
                <span class="chat-username" style="color: #858585;">● System</span>
                <span class="chat-time">${time}</span>
            </div>
            <div class="chat-text">${escapeHtml(text)}</div>
        `;
    } else {
        messageEl.innerHTML = `
            <div class="chat-message-header">
                <span class="chat-username">${escapeHtml(sender)}</span>
                <span class="chat-time">${time}</span>
            </div>
            <div class="chat-text">${escapeHtml(text)}</div>
        `;
    }

    messagesContainer.appendChild(messageEl);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function handleChatSubmit(e) {
    e.preventDefault();
    const input = document.getElementById('chatInput');
    const text = input.value.trim();

    if (!text) return;

    if (ws && ws.readyState === WebSocket.OPEN) {
        ws.send(JSON.stringify({
            type: 'chat',
            roomId,
            username,
            text
        }));
    }

    input.value = '';
}

// ==================== CODE EXECUTION ====================
async function executeCode() {
    const code = editor.getValue();
    const outputContent = document.getElementById('outputContent');
    outputContent.innerHTML = '<div class="placeholder">⏳ Running...</div>';

    try {
        const response = await fetch('/api/execute', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                language: currentLanguage,
                code
            })
        });

        const result = await response.json();

        if (response.ok) {
            let output = '';
            if (result.stdout) {
                output += `<div class="output-stdout">${escapeHtml(result.stdout)}</div>`;
            }
            if (result.stderr) {
                output += `<div class="output-stderr">Error: ${escapeHtml(result.stderr)}</div>`;
            }
            output += `<div class="output-info">Status: ${result.status} | Time: ${result.time}s | Memory: ${result.memory}KB</div>`;

            outputContent.innerHTML = output || '<div class="placeholder">No output</div>';
        } else {
            outputContent.innerHTML = `<div class="output-stderr">Error: ${escapeHtml(result.error)}</div>`;
        }
    } catch (err) {
        outputContent.innerHTML = `<div class="output-stderr">Error: ${escapeHtml(err.message)}</div>`;
    }
}

// ==================== VERSION HISTORY ====================
async function createSnapshot(auto = false) {
    const code = editor.getValue();

    try {
        const response = await fetch(`/api/rooms/${roomId}/snapshots`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                content: code,
                language: currentLanguage,
                username
            })
        });

        if (response.ok) {
            if (!auto) {
                addChatMessage('SYSTEM', `💾 Snapshot saved by ${username}`);
            }
            loadSnapshots();
        }
    } catch (err) {
        console.error('Error creating snapshot:', err);
    }
}

async function loadSnapshots() {
    try {
        const response = await fetch(`/api/rooms/${roomId}/snapshots`);
        const snapshots = await response.json();

        const snapshotsList = document.getElementById('snapshotsList');
        snapshotsList.innerHTML = '';

        if (snapshots.length === 0) {
            snapshotsList.innerHTML = '<div class="placeholder">No snapshots yet</div>';
            return;
        }

        snapshots.forEach(snapshot => {
            const item = document.createElement('div');
            item.className = 'snapshot-item';

            const createdAt = new Date(snapshot.created_at).toLocaleString();

            item.innerHTML = `
                <div class="snapshot-time">${createdAt}</div>
                <div class="snapshot-author">by ${escapeHtml(snapshot.created_by)}</div>
                <div class="snapshot-language">${snapshot.language}</div>
                <div class="snapshot-actions">
                    <button class="btn btn-sm btn-info" onclick="restoreSnapshot('${snapshot.id}')">Restore</button>
                    <button class="btn btn-sm btn-secondary" onclick="previewSnapshot('${snapshot.id}')">Preview</button>
                </div>
            `;

            snapshotsList.appendChild(item);
        });
    } catch (err) {
        console.error('Error loading snapshots:', err);
    }
}

async function restoreSnapshot(snapshotId) {
    if (!confirm('Restore this snapshot? Current code will be replaced.')) return;

    try {
        const response = await fetch(`/api/rooms/${roomId}/snapshots/${snapshotId}/restore`, {
            method: 'POST'
        });

        if (response.ok) {
            addChatMessage('SYSTEM', `${username} restored a snapshot`);
            closeHistoryDrawer();
        }
    } catch (err) {
        console.error('Error restoring snapshot:', err);
    }
}

function previewSnapshot(snapshotId) {
    // This would require fetching snapshot details and showing a modal
    alert('Preview feature coming soon');
}

// ==================== UI EVENT LISTENERS ====================
function setupUIEventListeners() {
    // Language selector
    document.getElementById('languageSelector').addEventListener('change', (e) => {
        currentLanguage = e.target.value;
        const monacoLanguage = languageMap[currentLanguage];
        monaco.editor.setModelLanguage(editor.getModel(), monacoLanguage);
    });

    // Run button
    document.getElementById('runBtn').addEventListener('click', executeCode);

    // Save snapshot button
    document.getElementById('saveSnapshotBtn').addEventListener('click', () => {
        createSnapshot(false);
    });

    // History button
    document.getElementById('historyBtn').addEventListener('click', () => {
        openHistoryDrawer();
        loadSnapshots();
    });

    // Chat form
    document.getElementById('chatForm').addEventListener('submit', handleChatSubmit);

    // Toggle output panel
    document.getElementById('toggleOutput').addEventListener('click', () => {
        document.querySelector('.output-panel').classList.toggle('collapsed');
    });

    // Toggle chat sidebar
    document.getElementById('toggleChat').addEventListener('click', () => {
        document.querySelector('.chat-sidebar').style.display = 
            document.querySelector('.chat-sidebar').style.display === 'none' ? 'flex' : 'none';
    });

    // History drawer controls
    document.getElementById('closeHistory').addEventListener('click', closeHistoryDrawer);
    document.getElementById('historyOverlay').addEventListener('click', closeHistoryDrawer);
}

function openHistoryDrawer() {
    document.getElementById('historyDrawer').classList.add('open');
    document.getElementById('historyOverlay').classList.add('show');
}

function closeHistoryDrawer() {
    document.getElementById('historyDrawer').classList.remove('open');
    document.getElementById('historyOverlay').classList.remove('show');
}

function updateLanguageSelector() {
    document.getElementById('languageSelector').value = currentLanguage;
}

// ==================== UTILITIES ====================
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load chat messages when page loads
window.addEventListener('load', async () => {
    try {
        const response = await fetch(`/api/rooms/${roomId}/messages`);
        const messages = await response.json();
        messages.forEach(msg => {
            addChatMessage(msg.username, msg.message);
        });
    } catch (err) {
        console.error('Error loading messages:', err);
    }
});
