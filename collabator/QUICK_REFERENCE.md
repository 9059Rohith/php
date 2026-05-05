# ⚡ Quick Reference Card

## 🚀 Start Using (Right Now)

```bash
# Server is running at:
http://localhost:3000

# Stop server: Ctrl+C in terminal
# Restart: npm start
```

---

## 🎯 Basic Workflow

```
1. Open http://localhost:3000
   ↓
2. Enter username (e.g., "Alice")
   ↓
3. Enter room code (e.g., "room123")
   ↓
4. Click "Join or Create Room"
   ↓
5. See editor with Monaco Editor
   ↓
6. Start typing or select language
   ↓
7. Press ▶ to run code or 💾 to save
```

---

## 💻 Terminal Commands

```bash
# Start server
npm start

# Install dependencies (already done)
npm install

# Stop server
Ctrl+C

# View server logs
# (Check terminal where npm start runs)

# Delete database and start fresh
rm data/collaborative.db
npm start
```

---

## 🎮 Browser Shortcuts

| Action | Shortcut |
|--------|----------|
| Open DevTools | F12 |
| Refresh page | Ctrl+R or Cmd+R |
| Full screen | F11 |
| Zoom in | Ctrl++ |
| Zoom out | Ctrl+- |
| Clear cache | Ctrl+Shift+Del |

---

## 🎨 Monaco Editor Keys

```
Ctrl+/       Comment/uncomment
Ctrl+D       Select word
Ctrl+H       Find & replace
Ctrl+M       Toggle minimap
Alt+Up       Move line up
Alt+Down     Move line down
Ctrl+B       Toggle sidebar (in VS Code)
```

---

## 📊 File Sizes

| File | Size | Type |
|------|------|------|
| server.js | ~12 KB | Backend |
| database.js | ~2 KB | Backend |
| client.js | ~18 KB | Frontend |
| index.html | ~4 KB | Frontend |
| styles.css | ~15 KB | Frontend |
| collaborative.db | ~50+ KB | Database |

---

## 🔗 Important URLs

```
Main Application:
http://localhost:3000

API Endpoints:
POST   http://localhost:3000/api/execute
GET    http://localhost:3000/api/rooms/:roomId
POST   http://localhost:3000/api/rooms/:roomId/snapshots
GET    http://localhost:3000/api/rooms/:roomId/messages

WebSocket:
ws://localhost:3000  (same as http)
```

---

## 🗣️ WebSocket Event Types

**Client sends:**
- `join` - Enter room
- `code-change` - Edit code
- `cursor` - Move cursor
- `chat` - Send message

**Server sends:**
- `joined` - Confirm join
- `code-change` - Code update
- `cursor` - Remote cursor
- `user-joined` / `user-left` - User presence
- `chat` - New message
- `code-state` - Load code
- `code-restored` - Snapshot restored

---

## 💬 Chat Commands

```
Regular message:
[Type text] + Enter → Broadcasts to room

System messages (auto-generated):
- "User joined room"
- "User left room"
- "Snapshot saved"
- "Snapshot restored"
```

---

## 🎯 Supported Languages

| Language | ID | Example |
|----------|----|----|
| JavaScript | 63 | `console.log("hi")` |
| Python | 71 | `print("hi")` |
| PHP | 68 | `<?php echo "hi"; ?>` |
| TypeScript | 74 | `console.log("hi")` |
| HTML | 89 | `<h1>Hello</h1>` |
| CSS | 50 | `body { color: red; }` |
| SQL | 82 | `SELECT * FROM table;` |

---

## 🐛 Troubleshooting Checklist

```
❌ Server won't start?
✅ Check if another app uses port 3000
✅ Try different port: PORT=3001 npm start

❌ Can't connect to server?
✅ Verify server is running (terminal shows "running on...")
✅ Try http://localhost:3000 (not 127.0.0.1)

❌ WebSocket not connecting?
✅ Open DevTools → Network tab → look for WS
✅ Check browser console for errors

❌ Code execution fails?
✅ Check syntax (try simple code first)
✅ Check language selected matches code
✅ Max 10 second timeout

❌ Database errors?
✅ Delete data/collaborative.db and restart
✅ Verify data/ folder exists

❌ Chat not working?
✅ Refresh page
✅ Check WebSocket connection

❌ Multiple users don't sync?
✅ Make sure using SAME room code
✅ Refresh both pages
✅ Check WebSocket is connected
```

---

## 🔑 Key Concepts

**Room** = Shared space for collaboration
- Each room has unique code
- Users in same room see each other's edits
- Separate code, chat, and history per room

**WebSocket** = Real-time two-way communication
- Instant updates (no polling)
- All users get updates simultaneously
- Connection stays open

**Debounce** = Wait 50ms before sending
- Reduces network traffic
- Makes collaboration smooth
- Waits for user to pause typing

**Judge0** = Sandboxed code execution
- Runs code safely
- Supports 7 languages
- 10 second timeout
- Returns stdout/stderr

**Snapshot** = Version of code at specific time
- Auto-saved every 60 seconds
- Manual save anytime
- Can restore to any snapshot
- Shows author and timestamp

---

## 🎨 Color Reference

```
Background:  #1e1e1e (dark)
Accent:      #007acc (blue)
Success:     #4ec9b0 (green)
Error:       #f48771 (red)
Text:        #d4d4d4 (light gray)
Muted:       #858585 (medium gray)
```

---

## 📱 Supported Browsers

| Browser | Version | Support |
|---------|---------|---------|
| Chrome | 80+ | ✅ Full |
| Firefox | 75+ | ✅ Full |
| Safari | 13+ | ✅ Full |
| Edge | 80+ | ✅ Full |
| IE 11 | - | ❌ No |

---

## 🎯 Feature Quick Access

| Feature | Location | Shortcut |
|---------|----------|----------|
| Language Select | Top bar center | Dropdown |
| Run Code | Top bar right | Button ▶ |
| Save Snapshot | Top bar right | Button 💾 |
| History | Top bar right | Button 📜 |
| Chat | Right sidebar | Always visible |
| Output | Bottom panel | Collapsible |
| User List | Top bar left | Shows count |

---

## 🚀 Deployment Quick Links

| Platform | Time | Difficulty |
|----------|------|-----------|
| Heroku | 15 min | ⭐ Easy |
| Docker | 30 min | ⭐⭐ Medium |
| VPS | 1 hour | ⭐⭐⭐ Hard |
| Kubernetes | 1 day | ⭐⭐⭐⭐ Very Hard |

**Start with:** Heroku (free, easiest)

---

## 📚 Documentation Files

```
INDEX.md          ← Start here
QUICKSTART.md     ← How to use
README.md         ← Full reference
DEPLOYMENT.md     ← Deploy & scale
UI_LAYOUT.md      ← Design details
PROJECT_SUMMARY   ← Overview
```

---

## 💡 Pro Tips

✨ **Multiple browsers** = Multiple users
✨ **Same room code** = Same conversation
✨ **Incognito window** = No cache issues
✨ **Ctrl+Shift+Del** = Clear browser cache
✨ **F12** = Open DevTools for debugging

---

## 🎓 Learn More

- WebSocket docs: https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
- Express.js: https://expressjs.com/
- Monaco Editor: https://microsoft.github.io/monaco-editor/
- Judge0 API: https://rapidapi.com/judge0-official/api/judge0-ce

---

## ⚡ Emergency Commands

```bash
# Restart everything
npm start

# Check if running
curl http://localhost:3000

# Kill process on port 3000 (if stuck)
# Mac/Linux:
lsof -i :3000 | grep LISTEN | awk '{print $2}' | xargs kill -9

# Windows:
netstat -ano | findstr :3000
taskkill /PID [PID] /F

# Fresh start (delete database)
rm data/collaborative.db && npm start
```

---

**Quick Help?** → Read [QUICKSTART.md](./QUICKSTART.md)

**Need Details?** → Check [README.md](./README.md)

**Ready to Deploy?** → Follow [DEPLOYMENT.md](./DEPLOYMENT.md)

---

**Server:** Running ✅  
**Database:** Ready ✅  
**Editor:** Live at http://localhost:3000 ✅

**Happy Coding!** 🚀

