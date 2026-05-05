const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const fs = require('fs');

const dbPath = path.join(__dirname, '../data/collaborative.db');

// Ensure data directory exists
const dataDir = path.join(__dirname, '../data');
if (!fs.existsSync(dataDir)) {
  fs.mkdirSync(dataDir, { recursive: true });
}

const db = new sqlite3.Database(dbPath, (err) => {
  if (err) {
    console.error('Error opening database:', err);
  } else {
    console.log('✓ Database connected');
    initializeDatabase();
  }
});

// Enable foreign keys
db.run('PRAGMA foreign_keys = ON');

// Initialize database tables
function initializeDatabase() {
  // Rooms table
  db.run(`
    CREATE TABLE IF NOT EXISTS rooms (
      id TEXT PRIMARY KEY,
      name TEXT NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      language TEXT DEFAULT 'javascript'
    )
  `);

  // Users table (for presence tracking)
  db.run(`
    CREATE TABLE IF NOT EXISTS users (
      id TEXT PRIMARY KEY,
      room_id TEXT NOT NULL,
      username TEXT NOT NULL,
      color TEXT,
      joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (room_id) REFERENCES rooms(id)
    )
  `);

  // Chat messages table
  db.run(`
    CREATE TABLE IF NOT EXISTS messages (
      id TEXT PRIMARY KEY,
      room_id TEXT NOT NULL,
      username TEXT NOT NULL,
      message TEXT NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (room_id) REFERENCES rooms(id)
    )
  `);

  // Code snapshots table
  db.run(`
    CREATE TABLE IF NOT EXISTS snapshots (
      id TEXT PRIMARY KEY,
      room_id TEXT NOT NULL,
      content TEXT NOT NULL,
      language TEXT,
      created_by TEXT NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (room_id) REFERENCES rooms(id)
    )
  `);

  // Current code state (latest version per room)
  db.run(`
    CREATE TABLE IF NOT EXISTS room_code_state (
      room_id TEXT PRIMARY KEY,
      content TEXT NOT NULL,
      language TEXT DEFAULT 'javascript',
      updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (room_id) REFERENCES rooms(id)
    )
  `, (err) => {
    if (err) {
      console.error('Error creating tables:', err);
    } else {
      console.log('✓ Database tables initialized');
    }
  });
}

module.exports = db;
