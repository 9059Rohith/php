# UI Layout & Component Guide

## 🎨 Landing Page

```
┌─────────────────────────────────────────────────────────┐
│                                                         │
│                                                         │
│        ┌─────────────────────────────────────┐         │
│        │  🚀 Collaborative Code Editor       │         │
│        │     Code together in real-time      │         │
│        │                                      │         │
│        │  ┌──────────────────────────────┐   │         │
│        │  │ Username                     │   │         │
│        │  │ [Enter your name...........]  │   │         │
│        │  └──────────────────────────────┘   │         │
│        │                                      │         │
│        │  ┌──────────────────────────────┐   │         │
│        │  │ Room Code                    │   │         │
│        │  │ [optional or auto-generated.]│   │         │
│        │  └──────────────────────────────┘   │         │
│        │                                      │         │
│        │      ┌──────────────────────┐       │         │
│        │      │ Join or Create Room  │       │         │
│        │      └──────────────────────┘       │         │
│        │                                      │         │
│        └─────────────────────────────────────┘         │
│                                                         │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 🎨 Editor Page - Overall Layout

```
┌─────────────────────────────────────────────────────────────────────┐
│                        TOP BAR (40px)                                │
├─────────────────────────────────────────────────────────────────────┤
│  room_123 | 2 users │ [Language v] │ ▶ | 💾 | 📜 │ 👤👥           │
├──────────────────────────────────────────┬──────────────────────────┤
│                                          │                          │
│                                          │  ┌──────────────────────┐│
│                                          │  │ 💬 Chat              ││
│       MONACO EDITOR                      │  ├──────────────────────┤│
│       (75% width)                        │  │ Alice: Hi!          ││
│       ┌────────────────────────┐         │  │ 09:15                ││
│       │                        │         │  │                      ││
│       │ console.log('hello')   │         │  │ Bob: Hey there!     ││
│       │ for (let i=0; i<3) {   │         │  │ 09:16                ││
│       │   console.log(i);      │         │  ├──────────────────────┤│
│       │ }                      │         │  │ [Type message...]    ││
│       │                        │         │  │ [Send]               ││
│       │ ➤ (Alice's cursor)    │         │  └──────────────────────┘│
│       │   ← (Bob's cursor)     │         │                          │
│       │                        │         │                          │
│       └────────────────────────┘         │                          │
├────────────────────────────────────────────┼──────────────────────────┤
│        OUTPUT PANEL (200px / collapsible)                            │
│ ┌─────────────────────────────────────────────────────────────────┐ │
│ │ 📤 Output                                              [−]      │ │
│ ├─────────────────────────────────────────────────────────────────┤ │
│ │ hello                                                           │ │
│ │ 0                                                               │ │
│ │ 1                                                               │ │
│ │ 2                                                               │ │
│ │ Status: Accepted | Time: 0.12s | Memory: 45KB                │ │
│ └─────────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 📐 Detailed Top Bar

```
┌──────────────────────────────────────────────────────────────────────┐
│                          TOP BAR BREAKDOWN                            │
├──────────────────────┬─────────────────┬──────────────┬──────────────┤
│    LEFT SECTION      │  CENTER SECTION │  RIGHT SECTION             │
├──────────────────────┼─────────────────┼──────────────┼──────────────┤
│                      │                 │              │              │
│ room_123             │ Language        │ ▶ Run Code   │ 👤👥👨👩   │
│ 2 users              │ [ JavaScript v] │ 💾 Save      │ (avatars)   │
│                      │                 │ 📜 History   │              │
└──────────────────────┴─────────────────┴──────────────┴──────────────┘

Legend:
- Room name (blue text)
- User count (gray text)
- Language selector (dropdown)
- Buttons with emojis
- User avatars (colored circles)
```

---

## 💬 Chat Sidebar Detail

```
CHAT SIDEBAR (25% width, collapsible)

┌────────────────────────────┐
│ 💬 Chat              [−]   │
├────────────────────────────┤
│                            │
│ ┌─ Message ──────────────┐ │
│ │ Alice        09:15      │ │
│ │ Hey! Let's debug       │ │
│ └────────────────────────┘ │
│                            │
│ ┌─ Message ──────────────┐ │
│ │ Bob          09:16      │ │
│ │ Sure, I see the error  │ │
│ └────────────────────────┘ │
│                            │
│ ┌─ System ────────────────┐│
│ │ ● System     09:17      ││
│ │ Charlie joined room    ││
│ └────────────────────────┘│
│                            │
│ ──────────────────────────│
│ [Type message here......]  │
│ [Send]                     │
└────────────────────────────┘

Colors:
- Regular user: Blue name
- System message: Gray name with dot
- Message background: Dark gray
- Border left: Accent color
```

---

## 🎮 Remote Cursors

```
CODE EDITOR WITH CURSORS

┌─────────────────────────────────────┐
│ 1  console.log('Hello, World!')     │
│ 2                                   │
│ 3  for (let i = 0; i < 5; i++) {   │ ← Alice's cursor (line 3)
│    ║ (red cursor + "Alice" label)   │   (red label above)
│ 4    console.log(i);                │
│ 5                                   │
│ 6    ▌                              │ ← Bob's cursor (line 6, col 5)
│      │ (blue cursor + "Bob" label)  │   (blue label)
│ 7  }                                │
│ 8                                   │
└─────────────────────────────────────┘

Cursor Styling:
- Width: 2px
- Height: 20px
- Color: User's assigned color
- Label: Username above cursor
- Animation: Blinking effect
- Disappears after 5s inactivity
```

---

## 🗂️ History Drawer

```
HISTORY DRAWER (slides in from right)

┌────────────────────────────────┐
│ 📜 Version History         [×] │
├────────────────────────────────┤
│                                │
│ ┌──────────────────────────┐   │
│ │ 09:45:12 PM, May 5       │   │
│ │ by Alice                 │   │
│ │ javascript               │   │
│ │ [Restore] [Preview]      │   │
│ └──────────────────────────┘   │
│                                │
│ ┌──────────────────────────┐   │
│ │ 09:15:33 PM, May 5       │   │
│ │ by Bob                   │   │
│ │ python                   │   │
│ │ [Restore] [Preview]      │   │
│ └──────────────────────────┘   │
│                                │
│ ┌──────────────────────────┐   │
│ │ 08:30:45 PM, May 5       │   │
│ │ by Alice                 │   │
│ │ javascript               │   │
│ │ [Restore] [Preview]      │   │
│ └──────────────────────────┘   │
│                                │
│ (scroll if more snapshots)     │
│                                │
└────────────────────────────────┘

Overlay: Semi-transparent dark background
Animation: Slides in from right (0.3s)
```

---

## 🎨 Color Scheme

```
VS CODE DARK THEME PALETTE

Background Colors:
  - Primary BG:    #1e1e1e (dark editor bg)
  - Secondary BG:  #252526 (panels)
  - Tertiary BG:   #2d2d30 (inputs, buttons)
  - Border:        #3e3e42 (dividers)

Text Colors:
  - Primary text:  #d4d4d4 (normal text)
  - Secondary txt: #858585 (muted text)
  - Info color:    #9cdcfe (blue, like Monaco)

Accent Colors:
  - Primary:       #007acc (VS Code blue)
  - Success:       #4ec9b0 (green)
  - Warning:       #ce9178 (orange/brown)
  - Error:         #f48771 (red)

Shadow:
  - Small: 0 2px 8px rgba(0,0,0,0.3)
  - Large: 0 10px 40px rgba(0,0,0,0.5)
```

---

## 📱 Responsive Breakpoints

```
SCREEN SIZES:

1. Desktop (1920px) - Default layout
   ┌─────────────────┬──────────────┐
   │   Editor 75%    │  Chat 25%    │
   │                 │              │
   ├─────────────────┴──────────────┤
   │   Output Panel 200px           │
   └─────────────────┴──────────────┘

2. Laptop (1280px) - Still good
   ┌─────────────────┬──────────────┐
   │   Editor 75%    │  Chat 20%    │
   │                 │              │
   ├─────────────────┴──────────────┤
   │   Output Panel 150px           │
   └─────────────────┴──────────────┘

3. Tablet (768px) - Stacked
   ┌────────────────────────────┐
   │      Editor (full)         │
   ├────────────────────────────┤
   │   Chat (collapsible, 100px)│
   ├────────────────────────────┤
   │  Output (collapsible)      │
   └────────────────────────────┘

Mobile not officially supported (requires 1080p+)
```

---

## 🎯 UI Component States

### Button States
```
DEFAULT:        HOVER:          ACTIVE:
┌───────┐       ┌───────┐       ┌───────┐
│Button │       │Button │       │Button │
└───────┘       └───────┘       └───────┘
bg: color       +shadow         -shadow
                +scale 1.01     scale 1.0
```

### Input States
```
NORMAL:
┌──────────────────┐
│ [Type here...]   │
└──────────────────┘
border: gray

FOCUSED:
┌──────────────────┐
│ [Type here...]   │ ← Blue border
└──────────────────┘
border: blue
shadow: blue glow
```

### Panel States
```
EXPANDED:               COLLAPSED:
┌─────────────┐        ┌─────────────┐
│ Title  [−]  │        │ Title  [+]  │
├─────────────┤        │             │
│ Content...  │        │             │
│ (visible)   │        │             │
└─────────────┘        └─────────────┘

max-height: auto       max-height: 40px
```

---

## 🎨 Animation Effects

```
1. FADE IN (messages)
   0%:   opacity: 0
   100%: opacity: 1
   Time: 0.2s ease-out

2. SLIDE IN (history drawer)
   0%:   transform: translateX(100%)
   100%: transform: translateX(0)
   Time: 0.3s ease

3. BLINK (remote cursors)
   0%, 49%: opacity: 1
   50%:     opacity: 0.3
   Time: 1s infinite

4. SCALE (avatar hover)
   Normal:  scale(1.0)
   Hover:   scale(1.1)
   Time:    0.2s
```

---

## 🔤 Typography

```
Font Stack:
  - Primary: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
  - Monospace: 'Consolas', 'Courier New', monospace

Font Sizes:
  - Page title:    28px (bold)
  - Section title: 13px (bold)
  - Body text:     13px (regular)
  - Small text:    11px (regular)
  - Code:          14px (Monaco default)

Line Height:
  - Body:  1.5
  - Code:  auto (Monaco handles)
```

---

## 📊 Layout Grid

```
All spacing uses 4px base unit:

4px   (1 unit)  - Tight spacing
8px   (2 unit)  - Normal padding
12px  (3 unit)  - Section padding
16px  (4 unit)  - Major padding
20px  (5 unit)  - Large spacing
24px  (6 unit)  - Extra large

Example button: 
  Padding: 8px 12px (8px vertical, 12px horizontal)
```

---

## 🖱️ Interactive Elements

```
BUTTONS:
  - Size: 30-36px height
  - Padding: 8px 12px
  - Border-radius: 4px
  - Transition: 0.2s all

INPUTS:
  - Size: 32px height
  - Padding: 8px 12px
  - Border-radius: 4px
  - Border: 1px solid #3e3e42
  - Focus border: #007acc

DROPDOWNS:
  - Size: 32px height
  - Cursor: pointer
  - Styled like inputs
  - Arrow pointer: ▼

SCROLLBARS:
  - Width: 12px
  - Track bg: #1e1e1e
  - Thumb bg: #2d2d30
  - Thumb hover: #3e3e42
  - Border-radius: 6px
```

---

## 🎯 Information Hierarchy

```
1st Level (Most Important):
   - Room name (large, blue)
   - User count (moderate, gray)

2nd Level (Important):
   - Language selector
   - Code content
   - Recent messages

3rd Level (Supporting):
   - Timestamps
   - Author names
   - UI chrome (borders, lines)

4th Level (Background):
   - Shadows
   - Borders
   - Empty space
```

---

This layout is **fully responsive** for 1080p+ screens and follows the **VS Code design language** for familiarity to developers.

