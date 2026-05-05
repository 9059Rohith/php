# 🏗️ Architecture & Deployment Guide

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     INTERNET / WAN                          │
└────────────┬────────────────────────────────┬───────────────┘
             │                                │
      ┌──────▼──────┐                  ┌──────▼──────┐
      │   Browser 1 │                  │   Browser 2 │
      │   (User A)  │                  │   (User B)  │
      └──────┬──────┘                  └──────┬──────┘
             │ HTTP/WebSocket                 │ HTTP/WebSocket
             │                                │
             └────────────────┬───────────────┘
                              │
                    ┌─────────▼────────────┐
                    │  Node.js Server      │
                    │  (Express + WS)      │
                    │  - HTTP API          │
                    │  - WebSocket Hub     │
                    │  - Broadcasting      │
                    └─────────┬────────────┘
                              │
                    ┌─────────▼────────────┐
                    │  SQLite Database     │
                    │  ✓ Rooms             │
                    │  ✓ Messages          │
                    │  ✓ Snapshots         │
                    │  ✓ Code State        │
                    └──────────────────────┘
                              │
                    ┌─────────▼────────────┐
                    │  Judge0 API          │
                    │  (Code Execution)    │
                    │  Sandboxed VMs       │
                    └──────────────────────┘
```

---

## Real-Time Data Flow

### **User A Types Code:**
```
User A Editor
    │
    ├─ (onChange event)
    │
    ├─ Debounce 50ms
    │
    ├─ WebSocket: {type: 'code-change', content: '...'}
    │
    ├─ Server receives
    │
    ├─ Save to SQLite
    │
    ├─ Broadcast to all in room (except sender)
    │
    └─ User B sees update instantly
       (preserves their cursor position)
```

### **User Moves Cursor:**
```
User A Cursor Move
    │
    ├─ (onCursorPosition event)
    │
    ├─ WebSocket: {type: 'cursor', position: {line, column}}
    │
    ├─ Server broadcasts
    │
    └─ User B sees colored cursor + label in real-time
```

---

## Database Schema (SQLite)

```sql
-- Rooms table
CREATE TABLE rooms (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  created_at DATETIME,
  language TEXT DEFAULT 'javascript'
);

-- Users (for presence tracking)
CREATE TABLE users (
  id TEXT PRIMARY KEY,
  room_id TEXT NOT NULL,
  username TEXT NOT NULL,
  color TEXT,
  joined_at DATETIME,
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Chat messages
CREATE TABLE messages (
  id TEXT PRIMARY KEY,
  room_id TEXT NOT NULL,
  username TEXT NOT NULL,
  message TEXT NOT NULL,
  created_at DATETIME,
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Code snapshots (version history)
CREATE TABLE snapshots (
  id TEXT PRIMARY KEY,
  room_id TEXT NOT NULL,
  content TEXT NOT NULL,        -- Full code
  language TEXT,                -- javascript, python, etc.
  created_by TEXT NOT NULL,     -- Username
  created_at DATETIME,
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);

-- Current code state per room
CREATE TABLE room_code_state (
  room_id TEXT PRIMARY KEY,
  content TEXT NOT NULL,
  language TEXT DEFAULT 'javascript',
  updated_at DATETIME,
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);
```

---

## Deployment Strategies

### 1️⃣ **Local Development (Current)**

**Running:**
```bash
npm start
# http://localhost:3000
```

**Storage:** `data/collaborative.db`

**Perfect for:** Testing, development, learning

**Limitations:**
- Single machine only
- Data lost on restart (with SQLite)

---

### 2️⃣ **Single VPS / Server**

**Example: DigitalOcean, AWS EC2, Linode**

#### Step 1: Connect to VPS
```bash
ssh root@your-vps-ip
```

#### Step 2: Install Node.js
```bash
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

#### Step 3: Clone/Upload Project
```bash
git clone <your-repo> /opt/editor
cd /opt/editor
npm install
```

#### Step 4: Setup PM2 (Process Manager)
```bash
sudo npm install -g pm2
pm2 start backend/server.js --name "editor"
pm2 startup
pm2 save
```

#### Step 5: Setup Nginx (Reverse Proxy)
```bash
sudo apt-get install -y nginx
```

Create `/etc/nginx/sites-available/editor`:
```nginx
server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://localhost:3000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/editor /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### Step 6: SSL/TLS (HTTPS)
```bash
sudo apt-get install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

**Cost:** ~$5-20/month for small VPS

**Users:** 10-50 concurrent users

---

### 3️⃣ **Docker Deployment**

Create `Dockerfile`:
```dockerfile
FROM node:18-alpine

WORKDIR /app

COPY package*.json ./
RUN npm ci --only=production

COPY . .

EXPOSE 3000

CMD ["npm", "start"]
```

Create `docker-compose.yml`:
```yaml
version: '3.8'

services:
  editor:
    build: .
    ports:
      - "3000:3000"
    volumes:
      - ./data:/app/data
    environment:
      - NODE_ENV=production
      - PORT=3000
    restart: unless-stopped
```

**Run:**
```bash
docker-compose up -d
```

**Deploy to Docker Hub:**
```bash
docker build -t username/editor:latest .
docker push username/editor:latest
```

**Deploy to AWS ECS / Google Cloud Run / Azure Container Instances**

---

### 4️⃣ **Serverless Deployment (Heroku)**

#### Option A: Deploy via Git

1. **Create Heroku app:**
```bash
heroku login
heroku create your-app-name
```

2. **Deploy:**
```bash
git push heroku main
```

3. **Set environment:**
```bash
heroku config:set NODE_ENV=production
```

4. **View logs:**
```bash
heroku logs --tail
```

5. **Access:** `https://your-app-name.herokuapp.com`

**Cost:** Free tier available (limited), $7+/month for production

**Limitations:** 
- Ephemeral filesystem (data lost on restart)
- 30-second request timeout
- Need PostgreSQL add-on for persistence

#### Database Migration for Heroku
Use PostgreSQL instead of SQLite:

```bash
heroku addons:create heroku-postgresql:hobby-dev
```

Update database.js to use PostgreSQL (pg library)

---

### 5️⃣ **Horizontal Scaling (Multiple Servers)**

For large deployments (100+ concurrent users):

```
┌─────────────────┐
│  Load Balancer  │ (Nginx/HAProxy)
└────────┬────────┘
         │
    ┌────┴────┬────────┬────────┐
    │          │        │        │
┌───▼──┐  ┌───▼──┐ ┌───▼──┐ ┌──▼────┐
│ App1 │  │ App2 │ │ App3 │ │ App4  │
└───┬──┘  └───┬──┘ └───┬──┘ └───┬───┘
    │         │       │        │
    └─────────┴───┬───┴────────┘
                  │
            ┌─────▼──────┐
            │ PostgreSQL  │
            │ (Shared DB) │
            └─────┬──────┘
                  │
            ┌─────▼──────┐
            │   Redis    │
            │ (WebSocket │
            │   Session  │
            │  Store)    │
            └────────────┘
```

**Technologies:**
- **Load Balancer:** Nginx, HAProxy, AWS ALB
- **Database:** PostgreSQL (replaces SQLite)
- **Session Store:** Redis (for WebSocket affinity)
- **Caching:** Redis
- **Message Queue:** RabbitMQ / Kafka (for scaling)

---

## Performance Optimization

### **Frontend Optimizations**
```javascript
// ✓ Debounce code changes (50ms)
clearTimeout(codeChangeTimeout);
codeChangeTimeout = setTimeout(() => {
    // Send update
}, 50);

// ✓ Throttle cursor updates
// (Monaco provides cursor efficiently)

// ✓ Lazy load chat messages
// (Load on demand, not all at once)
```

### **Backend Optimizations**
```javascript
// ✓ Async database operations (non-blocking)
db.run(sql, params, callback);

// ✓ WebSocket broadcasting (efficient)
broadcastToRoom(roomId, message);

// ✓ Connection pooling
// (handled by driver)

// ✓ Message compression
// (consider msgpack or protobuf for large rooms)
```

### **Database Optimizations**
```sql
-- Add indexes for fast queries
CREATE INDEX idx_messages_room ON messages(room_id, created_at);
CREATE INDEX idx_snapshots_room ON snapshots(room_id, created_at);
CREATE INDEX idx_users_room ON users(room_id);

-- Partition large tables by date (if >1M rows)
-- Archive old messages regularly
-- Vacuum database monthly: VACUUM;
```

---

## Security Checklist

### **Pre-Production** ✓
- [ ] Authentication (OAuth2 / JWT)
- [ ] Authorization (room access control)
- [ ] Rate limiting (prevent abuse)
- [ ] Input sanitization (XSS prevention)
- [ ] SQL injection prevention (use parameterized queries) ✓
- [ ] CSRF protection
- [ ] HTTPS/WSS (SSL/TLS certificates)
- [ ] Environment variables (.env file) ✓
- [ ] No credentials in code ✓
- [ ] Security headers (CORS, CSP)

### **Database Security**
```javascript
// ✓ Always use parameterized queries
db.run('INSERT INTO messages (id, room_id, username, message) VALUES (?, ?, ?, ?)',
       [id, roomId, username, message]);

// ✗ NEVER do this:
db.run(`INSERT INTO messages VALUES ('${id}', '${roomId}', '${username}', '${message}')`);
```

### **Code Execution Safety**
- Judge0 API runs in **isolated sandboxes**
- 10-second timeout prevents infinite loops
- Memory/CPU limits enforced by Judge0
- **Already secure** for demo use

### **WebSocket Security**
```javascript
// Validate all incoming messages
if (!message.roomId || !message.username) {
    ws.close(1008, 'Invalid message');
}

// Rate limit per user
// Verify user is in room before broadcasting
```

---

## Monitoring & Logging

### **Production Logging**
```bash
# View real-time logs
npm install -g pm2
pm2 logs editor

# Or use systemd
journalctl -u editor -f
```

### **Error Tracking (optional)
```bash
npm install sentry-node
```

Usage:
```javascript
const Sentry = require("@sentry/node");
Sentry.init({dsn: "YOUR_DSN"});
app.use(Sentry.Handlers.errorHandler());
```

### **Metrics to Monitor**
- WebSocket connection count
- Database query time
- API response time
- Code execution requests
- Room creation rate
- Chat message throughput

---

## Scaling Timeline

| Users | Strategy | Setup Time |
|-------|----------|-----------|
| 1-10 | Local dev | 5 min |
| 10-50 | Single VPS | 30 min |
| 50-500 | VPS + PostgreSQL | 2 hours |
| 500-5k | Multi-VPS + LB | 1 day |
| 5k+ | Kubernetes + CDN | 1 week |

---

## Backup Strategy

### **Daily Backups**
```bash
#!/bin/bash
# backup.sh
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
sqlite3 data/collaborative.db ".backup data/backup_$TIMESTAMP.db"
tar -czf backup_$TIMESTAMP.tar.gz data/
aws s3 cp backup_$TIMESTAMP.tar.gz s3://my-backups/
```

### **Restore from Backup**
```bash
cp data/backup_20250505_000000.db data/collaborative.db
npm start
```

---

## Cost Estimates

| Platform | Tier | Cost/Month | Users |
|----------|------|-----------|-------|
| Local Dev | - | $0 | 1 |
| Heroku | Free | $0 | 10 |
| Heroku | Basic | $7 | 50 |
| VPS (DigitalOcean) | Standard | $12 | 100 |
| VPS (AWS EC2) | t3.medium | $30 | 500 |
| Kubernetes | Managed | $100+ | 5000+ |

---

## Migration Path

```
Local Dev
    ↓
    ├─→ VPS (if <100 users)
    │
    └─→ Heroku (if quick launch needed)
         ↓
         ├─→ PostgreSQL add-on ($50+/mo)
         │
         └─→ Custom VPS (more control, cost)
              ↓
              ├─→ Docker containers
              │
              └─→ Kubernetes (if scaling needed)
                  ↓
                  └─→ Multi-region CDN
```

---

## Quick Deploy Commands

### **Heroku (Fastest)**
```bash
heroku create
git push heroku main
heroku open
```

### **Docker (Local)**
```bash
docker-compose up -d
# http://localhost:3000
```

### **VPS (Production)**
```bash
ssh root@ip
git clone <repo> /opt/editor
cd /opt/editor && npm install
pm2 start backend/server.js
# Setup Nginx + SSL
```

---

**Choose your deployment based on scale and budget!** 🚀

