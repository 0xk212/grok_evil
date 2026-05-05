<?php
require_once 'config.php';
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch initial conversation if ID is provided
$conv_id = $_GET['id'] ?? null;
$messages = [];
if ($conv_id && is_numeric($conv_id)) {
    $stmt = $pdo->prepare("SELECT role, content FROM chats WHERE conversation_id = ? AND user_id = ? ORDER BY id ASC");
    $stmt->execute([$conv_id, $user_id]);
    $messages = $stmt->fetchAll();
}

// Fetch all conversations for sidebar
$stmt = $pdo->prepare("SELECT id, title, created_at FROM conversations WHERE user_id = ? ORDER BY created_at DESC LIMIT 100");
$stmt->execute([$user_id]);
$history_rows = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grok | Evil Edition</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @font-face {
            font-family: 'universalSans';
            src: url('https://cdn.grok.com/_next/static/media/UniversalSans_Text_400.p.8e69d71d.woff2') format('woff2');
            font-weight: 400; font-style: normal;
        }
        @font-face {
            font-family: 'universalSansMedium';
            src: url('https://cdn.grok.com/_next/static/media/UniversalSans_Text_550.p.8ed2b378.woff2') format('woff2');
            font-weight: 550; font-style: normal;
        }

        :root {
            --font-main: 'universalSans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            --bg-color: #000000;
            --sidebar-width: 64px;
            --panel-width: 280px;
            --input-bg: #161616;
            --text-primary: #ffffff;
            --text-secondary: #888888;
            --accent-crimson: #e11d48;
            --border-dim: rgba(255, 255, 255, 0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: var(--font-main); }
        body { background-color: var(--bg-color); color: var(--text-primary); height: 100vh; display: flex; overflow: hidden; }

        /* Sidebar & Panel */
        .sidebar {
            width: var(--sidebar-width); height: 100vh; background: #000;
            border-right: 1px solid var(--border-dim); display: flex; flex-direction: column;
            align-items: center; padding: 16px 0; z-index: 100; flex-shrink: 0;
        }
        .sidebar-icon {
            width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;
            color: var(--text-secondary); font-size: 1.1rem; cursor: pointer; transition: 0.2s; border-radius: 8px; margin-bottom: 8px;
        }
        .sidebar-icon:hover, .sidebar-icon.active { color: #fff; background: rgba(255,255,255,0.08); }
        
        .history-panel {
            width: 0; height: 100vh; background: #000; border-right: 1px solid var(--border-dim);
            overflow: hidden; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex; flex-direction: column; z-index: 90;
        }
        .history-panel.open { width: var(--panel-width); }
        .panel-header { padding: 20px; font-size: 0.85rem; font-weight: 600; color: #888; text-transform: uppercase; border-bottom: 1px solid var(--border-dim); }
        .history-list-scroll { flex: 1; overflow-y: auto; padding: 10px; }
        .history-item {
            padding: 12px 15px; border-radius: 8px; font-size: 0.9rem; color: #ccc; cursor: pointer;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis; transition: 0.2s;
            margin-bottom: 4px; display: block; text-decoration: none;
        }
        .history-item:hover { background: rgba(255,255,255,0.05); color: #fff; }
        .history-item.active { background: rgba(255,255,255,0.1); color: #fff; }

        /* Main Area */
        .main-container { flex: 1; display: flex; flex-direction: column; position: relative; z-index: 5; }
        
        .stars-background {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJub25lIi8+PGNpcmNsZSBjeD0iMTAiIGN5PSIxMCIgcj0iMC41IiBmaWxsPSIjZmZmIi8+PGNpcmNsZSBjeD0iMTIwIiBjeT0iNjAiIHI9IjAuNSIgZmlsbD0iI2ZmZiIvPjxjaXJjbGUgY3g9IjE4MCIgY3k9IjEyMCIgcj0iMC41IiBmaWxsPSIjZmZmIi8+PGNpcmNsZSBjeD0iNDAiIGN5PSIxNTAiIHI9IjAuNSIgZmlsbD0iI2ZmZiIvPjwvc3ZnPg==');
            opacity: 0.15; pointer-events: none;
        }

        .hero { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; max-width: 800px; margin: 0 auto; padding: 0 200px; }
        .logo-box { display: flex; align-items: center; gap: 12px; margin-bottom: 50px; }
        .grok-text { font-family: 'universalSansMedium', sans-serif; font-size: 3.5rem; font-weight: 600; }

        .replica-search {
            width: 100%; background: #111; border-radius: 50px; padding: 6px 10px;
            display: flex; align-items: center; gap: 15px; border: 1px solid var(--border-dim);
        }
        #mainInput, #chatInput {
            flex: 1; background: transparent; border: none; color: #fff;
            font-size: 1.15rem; outline: none; padding: 12px 10px 12px 20px;
        }
        .waveform-btn {
            width: 38px; height: 38px; background: #fff; color: #000; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; cursor: pointer; border: none;
        }

        .chat-view { display: none; flex: 1; overflow-y: auto; padding: 60px 0; flex-direction: column; align-items: center; width: 100%; }
        .chat-list { width: 100%; max-width: 800px; padding: 0 20px; }
        .fixed-input-area { display: none; width: 100%; max-width: 800px; margin: 0 auto 24px; padding: 0 20px; }
        
        .message { display: flex; gap: 20px; margin-bottom: 40px; width: 100%; }
        .bot-avatar { width: 32px; height: 32px; background: #fff; border-radius: 6px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
        .user-avatar { width: 32px; height: 32px; background: #007664; border-radius: 50%; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.8rem; }
        .msg-body { flex: 1; font-size: 1.05rem; line-height: 1.6; }
        .thought-container { background: rgba(255,255,255,0.03); border-radius: 8px; padding: 15px; margin-bottom: 15px; color: #777; font-style: italic; font-size: 0.9rem; }
    </style>
</head>
<body data-conv-id="<?php echo htmlspecialchars((string)$conv_id); ?>" 
      data-csrf-token="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <svg viewBox="0 0 24 24" width="24" height="24" fill="white"><circle cx="12" cy="12" r="11" stroke="white" stroke-width="2" fill="none"></circle><path d="M7 17L17 7" stroke="white" stroke-width="2" stroke-linecap="round"></path></svg>
        </div>
        <div class="sidebar-icon active" title="Search" onclick="location.href='index.php'"><i class="fa-solid fa-magnifying-glass"></i></div>
        <div class="sidebar-icon" title="History" id="historyToggle"><i class="fa-solid fa-clock-rotate-left"></i></div>
        <?php if($role === 'admin'): ?>
            <div class="sidebar-icon" title="Admin Control" onclick="location.href='admin.php'"><i class="fa-solid fa-user-shield"></i></div>
        <?php endif; ?>
        <div class="sidebar-profile" style="margin-top:auto" onclick="location.href='logout.php'"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
    </aside>

    <div class="history-panel" id="historyPanel">
        <div class="panel-header">Chat History</div>
        <div class="history-list-scroll">
            <?php foreach ($history_rows as $row): ?>
            <a href="?id=<?php echo $row['id']; ?>" class="history-item <?php echo ($conv_id == $row['id']) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($row['title']); ?>
            </a>
            <?php endforeach; ?>
            <?php if (empty($history_rows)): ?>
            <div style="padding:20px; font-size:0.8rem; color:#444">No conversations yet.</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="main-container">
        <div class="stars-background"></div>
        
        <!-- Hero Section -->
        <div class="hero" id="heroSection" <?php if ($conv_id) echo 'style="display:none"'; ?>>
            <div class="logo-box">
                <svg viewBox="0 0 24 24" width="60" height="60" fill="white"><circle cx="12" cy="12" r="11" stroke="white" stroke-width="2" fill="none"></circle><path d="M6 18L18 6" stroke="white" stroke-width="2.5"></path></svg>
                <span class="grok-text">Grok</span>
            </div>
            <div class="replica-search">
                <input type="text" id="mainInput" placeholder="How can I help you today?" dir="auto">
                <div class="search-actions">
                    <button class="waveform-btn" id="sendBtn"><i class="fa-solid fa-arrow-up"></i></button>
                </div>
            </div>
        </div>

        <!-- Chat Section -->
        <section class="chat-view" id="chatView" <?php if ($conv_id) echo 'style="display:flex"'; ?>>
            <div class="chat-list" id="chatList">
                <?php foreach ($messages as $msg): ?>
                    <div class="message">
                        <div class="avatar-box">
                            <?php if ($msg['role'] === 'user'): ?>
                                <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
                            <?php else: ?>
                                <div class="bot-avatar"><svg viewBox="0 0 24 24" width="20" height="20"><circle cx="12" cy="12" r="11" stroke="black" stroke-width="2" fill="none"></circle><path d="M6 18L18 6" stroke="black" stroke-width="2.5"></path></svg></div>
                            <?php endif; ?>
                        </div>
                        <div class="msg-body">
                            <?php echo nl2br(htmlspecialchars($msg['content'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Fixed Input Section -->
        <div class="fixed-input-area" id="footerInput" <?php if ($conv_id) echo 'style="display:block"'; ?>>
            <div class="replica-search">
                <textarea id="chatInput" placeholder="Ask GROK Evil anything..." rows="1" style="resize:none;"></textarea>
                <div class="search-actions">
                    <button class="waveform-btn" id="sendBtnChat"><i class="fa-solid fa-arrow-up"></i></button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const app = {
            convId: document.body.dataset.convId || '',
            csrf: document.body.dataset.csrfToken || '',
            isSending: false,

            init() {
                this.chatList = document.getElementById('chatList');
                this.chatView = document.getElementById('chatView');
                
                document.getElementById('historyToggle').addEventListener('click', () => {
                    document.getElementById('historyPanel').classList.toggle('open');
                });

                const hInput = document.getElementById('mainInput');
                const cInput = document.getElementById('chatInput');
                const hSend = document.getElementById('sendBtn');
                const cSend = document.getElementById('sendBtnChat');

                if (hSend) hSend.addEventListener('click', () => { this.run(hInput); });
                if (hInput) hInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') this.run(hInput); });
                
                if (cSend) cSend.addEventListener('click', () => { this.run(cInput); });
                if (cInput) {
                    cInput.addEventListener('keydown', (e) => { if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); this.run(cInput); } });
                    cInput.addEventListener('input', function() { this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px'; });
                }
                
                if (this.convId) this.scrollToBottom();
            },

            scrollToBottom() { if (this.chatView) this.chatView.scrollTop = this.chatView.scrollHeight; },
            escapeHTML(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; },
            
            append(role, text) {
                const div = document.createElement('div');
                div.className = 'message';
                const avatar = role === 'user' ? `<div class="user-avatar">${document.querySelector('.sidebar-profile').innerText}</div>` 
                                                : `<div class="bot-avatar"><svg viewBox="0 0 24 24" width="20" height="20"><circle cx="12" cy="12" r="11" stroke="black" stroke-width="2" fill="none"></circle><path d="M6 18L18 6" stroke="black" stroke-width="2.5"></path></svg></div>`;
                div.innerHTML = `<div class="avatar-box">${avatar}</div><div class="msg-body">${text}</div>`;
                this.chatList.appendChild(div);
                this.scrollToBottom();
                return div;
            },

            async run(inputEl) {
                const val = inputEl.value.trim();
                if (!val || this.isSending) return;

                document.getElementById('heroSection').style.display = 'none';
                this.chatView.style.display = 'flex';
                document.getElementById('footerInput').style.display = 'block';

                this.isSending = true;
                this.append('user', this.escapeHTML(val));
                inputEl.value = '';
                const node = this.append('baki', '<i>INITIALIZING...</i>');
                const textContainer = node.querySelector('.msg-body');

                try {
                    const response = await fetch('api.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({ message: val, conversation_id: this.convId, csrf_token: this.csrf })
                    });
                    const reader = response.body.getReader();
                    const decoder = new TextDecoder();
                    let fullText = '', thoughtText = '', isThinking = false, hasSyncedId = false;
                    textContainer.innerHTML = '';

                    while (true) {
                        const { done, value } = await reader.read();
                        if (done) break;
                        const lines = decoder.decode(value, { stream: true }).split('\n');
                        for (let line of lines) {
                            if (!line.startsWith('data: ')) continue;
                            const dataStr = line.substring(6).trim();
                            if (dataStr === '[DONE]') continue;
                            try {
                                const json = JSON.parse(dataStr);
                                if (json.conversation_id && !hasSyncedId) {
                                    this.convId = json.conversation_id;
                                    hasSyncedId = true;
                                    window.history.replaceState({}, '', '?id=' + this.convId);
                                }
                                const r = json.choices?.[0]?.delta?.reasoning_content || '';
                                const c = json.choices?.[0]?.delta?.content || '';
                                if (r) {
                                    if (!isThinking) {
                                        isThinking = true;
                                        textContainer.innerHTML += `<div class="thought-container"><div class="thought-header"><i class="fa-solid fa-brain"></i> Thought Process</div><div class="thought-content"></div></div>`;
                                    }
                                    thoughtText += r;
                                    textContainer.querySelector('.thought-content').innerText = thoughtText;
                                }
                                if (c) {
                                    isThinking = false;
                                    fullText += c;
                                    let contentDiv = textContainer.querySelector('.res-text');
                                    if (!contentDiv) { contentDiv = document.createElement('div'); contentDiv.className = 'res-text'; textContainer.appendChild(contentDiv); }
                                    contentDiv.innerHTML = fullText.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
                                }
                                this.scrollToBottom();
                            } catch (e) {}
                        }
                    }
                } catch(e) { textContainer.innerHTML = 'FATAL ERROR: Uplink compromised.'; } finally { this.isSending = false; }
            }
        };
        window.addEventListener('DOMContentLoaded', () => app.init());
    </script>
</body>
</html>