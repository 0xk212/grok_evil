const app = {
    chatList: null,
    scrollContainer: null,
    mainInput: null,
    sendBtn: null,
    inputContainer: null,
    newChatLink: null,
    convId: '',
    csrfToken: '',
    isSending: false,
    resizeObserver: null,
    allowedTags: new Set(['B', 'BR', 'BUTTON', 'CODE', 'DIV', 'H1', 'H2', 'H3', 'I', 'LI', 'PRE', 'SPAN']),
    allowedAttrs: {
        DIV: new Set(['class', 'style', 'dir', 'data-content']),
        SPAN: new Set(['class', 'style', 'dir']),
        I: new Set(['class']),
        BUTTON: new Set(['class', 'type', 'title', 'data-action']),
        PRE: new Set(['spellcheck', 'style']),
        CODE: new Set(['style']),
        H1: new Set(['style', 'dir']),
        H2: new Set(['style', 'dir']),
        H3: new Set(['style', 'dir']),
        LI: new Set(['style', 'dir']),
        BR: new Set([]),
        B: new Set(['style'])
    },

    init() {
        this.chatList = document.getElementById('chatList');
        this.scrollContainer = document.getElementById('scrollContainer');
        this.inputContainer = document.querySelector('.input-fixed-container');
        
        // Handle both Hero and Chat inputs
        const heroInput = document.getElementById('mainInput');
        const chatInput = document.getElementById('chatInput');
        this.mainInput = heroInput || chatInput;

        const heroSend = document.getElementById('sendBtn');
        const chatSend = document.getElementById('sendBtnChat');
        this.sendBtn = heroSend || chatSend;

        // Listener for the home-mode logic
        if (heroSend && heroInput) {
            heroSend.addEventListener('click', () => {
                this.mainInput = heroInput;
                this.run();
            });
            heroInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    this.mainInput = heroInput;
                    this.sendBtn = heroSend;
                    this.run();
                }
            });
        }

        // Listener for the chat-mode logic
        if (chatSend && chatInput) {
            chatSend.addEventListener('click', () => {
                this.mainInput = chatInput;
                this.run();
            });
            chatInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.mainInput = chatInput;
                    this.sendBtn = chatSend;
                    this.run();
                }
            });
            chatInput.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }

        const dismissBtn = document.getElementById('btnDismissModal');
        if (dismissBtn) {
            dismissBtn.addEventListener('click', () => {
                document.getElementById('upgradeModal').style.display = 'none';
            });
        }

        const langModal = document.getElementById('langModal');
        let userLang = localStorage.getItem('ds_user_lang');
        if (!userLang && langModal) {
            langModal.style.display = 'flex';
            langModal.querySelectorAll('button[data-lang]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    userLang = e.target.dataset.lang;
                    localStorage.setItem('ds_user_lang', userLang);
                    langModal.style.display = 'none';
                });
            });
        }

        // Initialize existing messages if any
        // IMPORTANT: always use data-content attribute (decoded by browser from htmlspecialchars)
        // Never use innerText here as it re-reads already-escaped content
        document.querySelectorAll('.msg-text-node[data-content]').forEach(msg => {
            const raw = msg.getAttribute('data-content').trim();
            if (raw) {
                msg.innerHTML = this.sanitizeHTML(this.parseContent(raw));
            }
        });
        this.scrollToBottom();
        console.log("SYSTEM_READY: Uplink established.");
    },

    escapeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    parseContent(t) {
        if (!t) return "";

        // Phase 1: Extract special containers to preserve them from escaping
        let thoughts = [];
        t = t.replace(/<thought>([\s\S]*?)<\/thought>/g, (match, content) => {
            const id = `__THOUGHT_${thoughts.length}__`;
            thoughts.push(`<div class="thought-container">
                        <div class="thought-header"><i class="fa-solid fa-brain"></i> DeepThink Process</div>
                        <div class="thought-content">${this.escapeHTML(content.trim())}</div>
                    </div>`);
            return id;
        });

        let codeBlocks = [];
        // Fix 1+2+3+4: robust regex, skip empty blocks, LTR direction, spellcheck=false
        t = t.replace(/```([\w+-]*)[^\n]*\n([\s\S]*?)```/g, (match, lang, code) => {
            const trimmedCode = code.trim();
            if (!trimmedCode) return ''; 
            const id = `__CODE_${codeBlocks.length}__`;
            const language = lang ? lang.toLowerCase() : 'text'; 
            codeBlocks.push(`
                <div class="code-frame">
                    <div class="code-frame-header">
                        <div class="code-lang-label">${language}</div>
                        <div class="code-actions">
                            <button class="copy-tiny-btn" type="button" data-action="copy-snippet">Copy</button>
                        </div>
                    </div>
                    <pre spellcheck="false"><code>${this.escapeHTML(trimmedCode)}</code></pre>
                </div>`);
            return id;
        });

        // Phase 2: Escape remaining content and apply markdown
        let processed = this.escapeHTML(t)
            .replace(/^### (.*$)/gm, '<h3 style="margin:15px 0 10px; color:var(--ds-accent-evil)">$1</h3>')
            .replace(/^## (.*$)/gm, '<h2 style="margin:20px 0 10px; border-bottom:1px solid #333; padding-bottom:5px">$1</h2>')
            .replace(/^# (.*$)/gm, '<h1 style="margin:25px 0 15px; color:var(--ds-accent-evil)">$1</h1>')
            .replace(/\*\*(.*?)\*\*/g, '<b style="color:#fff">$1</b>')
            .replace(/^\- (.*$)/gm, '<li style="margin-left:20px; color:#ccc">$1</li>')
            .replace(/^\* (.*$)/gm, '<li style="margin-left:20px; color:#ccc">$1</li>')
            .replace(/`(.*?)`/g, '<code style="background:rgba(255,255,255,0.1); padding:2px 4px; border-radius:4px">$1</code>')
            .replace(/\n/g, '<br>');

        // Phase 3: Restore special containers
        thoughts.forEach((html, i) => {
            processed = processed.replace(`__THOUGHT_${i}__`, html);
        });
        codeBlocks.forEach((html, i) => {
            processed = processed.replace(`__CODE_${i}__`, html);
        });

        return processed;
    },

    sanitizeHTML(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(`<div>${html}</div>`, 'text/html');
        const root = doc.body.firstElementChild;

        const cleanNode = (node) => {
            if (node.nodeType === Node.TEXT_NODE) {
                return document.createTextNode(node.textContent);
            }

            if (node.nodeType !== Node.ELEMENT_NODE) {
                return document.createTextNode('');
            }

            const tagName = node.tagName.toUpperCase();
            if (!this.allowedTags.has(tagName)) {
                const fragment = document.createDocumentFragment();
                Array.from(node.childNodes).forEach((child) => {
                    fragment.appendChild(cleanNode(child));
                });
                return fragment;
            }

            const el = document.createElement(tagName.toLowerCase());
            const allowedAttrs = this.allowedAttrs[tagName] || new Set();
            Array.from(node.attributes).forEach((attr) => {
                const attrName = attr.name.toLowerCase();
                const attrValue = attr.value;

                if (attrName.startsWith('on')) {
                    return;
                }

                if (!allowedAttrs.has(attr.name)) {
                    return;
                }

                if (attrName === 'class') {
                    el.className = attrValue.replace(/[^\w\s-]/g, ' ').trim();
                    return;
                }

                if (attrName === 'type' && attrValue === 'button') {
                    el.setAttribute('type', 'button');
                    return;
                }

                if (attrName === 'title' || attrName === 'data-action' || attrName === 'spellcheck') {
                    el.setAttribute(attr.name, attrValue);
                }
            });

            Array.from(node.childNodes).forEach((child) => {
                el.appendChild(cleanNode(child));
            });

            return el;
        };

        const wrapper = document.createElement('div');
        Array.from(root.childNodes).forEach((child) => {
            wrapper.appendChild(cleanNode(child));
        });

        return wrapper.innerHTML;
    },

    renderContent(target, html) {
        target.innerHTML = this.sanitizeHTML(html);
    },

    handleDocumentClick(e) {
        const copyBtn = e.target.closest('[data-action="copy-snippet"]');
        if (copyBtn) {
            this.copySnippet(copyBtn);
            return;
        }

        const deleteBtn = e.target.closest('[data-confirm-delete="true"]');
        if (deleteBtn) {
            if (!window.confirm('Erase this agent?')) {
                e.preventDefault();
            }
        }
    },

    copySnippet(btn) {
        // If it's a code block copy button
        const codeFrame = btn.closest('.code-frame');
        if (codeFrame) {
            const code = codeFrame.querySelector('code').textContent;
            navigator.clipboard.writeText(code);
            btn.textContent = 'Copied';
            setTimeout(() => {
                btn.textContent = 'Copy';
            }, 2000);
            return;
        }

        // If it's a message copy button
        const msgNode = btn.closest('.msg-text-node-wrapper');
        if (msgNode) {
            const text = msgNode.querySelector('.msg-text-node .final-content') 
                       ? msgNode.querySelector('.msg-text-node .final-content').innerText 
                       : msgNode.querySelector('.msg-text-node').innerText;
            navigator.clipboard.writeText(text);
            const originalHtml = btn.innerHTML;
            btn.textContent = 'Copied';
            setTimeout(() => btn.innerHTML = originalHtml, 2000);
        }
    },

    updateSidebar(id, title) {
        if (!id) return;
        const historyList = document.querySelector('.history-scroll');
        if (!historyList) return;

        // Convert to string for reliable comparison
        const idStr = String(id);

        // Check if this conversation already exists in the sidebar
        const existing = Array.from(historyList.querySelectorAll('.history-item')).find(a => {
            const url = new URL(a.href, window.location.origin);
            return String(url.searchParams.get('id')) === idStr;
        });

        if (existing) {
            // Already exists — just mark it active, don't add a duplicate
            historyList.querySelectorAll('.history-item').forEach(item => item.classList.remove('active'));
            existing.classList.add('active');
            return;
        }

        // Remove "No history found" placeholder if present
        const emptyMsg = historyList.querySelector('.no-history-msg, [data-empty]');
        if (emptyMsg) emptyMsg.remove();

        historyList.querySelectorAll('.history-item').forEach(item => item.classList.remove('active'));

        const a = document.createElement('a');
        a.href = `?id=${idStr}`;
        a.dataset.convId = idStr; // store for easy lookup
        a.className = 'history-item active';
        a.style.textDecoration = 'none';
        a.innerHTML = this.sanitizeHTML(`<i class="fa-regular fa-message"></i><span>${this.escapeHTML(title.substring(0, 30))}</span>`);

        const label = historyList.querySelector('.history-label');
        if (label && label.nextSibling) {
            historyList.insertBefore(a, label.nextSibling);
        } else {
            historyList.appendChild(a);
        }
    },

    scrollToBottom() {
        if (!this.scrollContainer) return;

        const anchor = document.getElementById('chatAnchor');
        const forceScroll = () => {
            this.scrollContainer.scrollTo({
                top: this.scrollContainer.scrollHeight,
                behavior: 'smooth'
            });
        };

        forceScroll();
        setTimeout(forceScroll, 50); 
        setTimeout(forceScroll, 250); 
    },

    syncLayout() {
        if (!this.inputContainer) return;

        const composerHeight = Math.ceil(this.inputContainer.getBoundingClientRect().height);
        document.documentElement.style.setProperty('--composer-height', `${composerHeight}px`);
    },

    resetConversationState() {
        this.convId = '';
        document.body.dataset.convId = '';
        sessionStorage.removeItem('bakigpt_conv_id');
    },

    setSendingState(isSending) {
        this.isSending = isSending;

        if (this.sendBtn) {
            this.sendBtn.disabled = isSending;
            this.sendBtn.style.opacity = isSending ? '0.6' : '';
            this.sendBtn.style.pointerEvents = isSending ? 'none' : '';
        }

        if (this.mainInput) {
            this.mainInput.disabled = isSending;
        }
    },

    append(role, text, id = '') {
        const div = document.createElement('div');
        div.className = 'message-node ' + (role === 'user' ? 'user' : 'baki');
        if (id) div.id = id;

        const icon = role === 'user' 
            ? '<div class="avatar-circle-user">' + (document.querySelector('.avatar-circle')?.innerText || 'U') + '</div>' 
            : `<div class="avatar-ds-evil">
                <svg width="24" height="24" viewBox="0 0 35 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 6L18 12L12 18" stroke="black" stroke-width="4" stroke-linecap="round"></path>
                </svg>
               </div>`;

        div.innerHTML = `
            <div class="msg-avatar-node">${icon}</div>
            <div class="msg-text-node-wrapper">
                <div class="msg-text-node" dir="auto"></div>
            </div>
        `;
        this.chatList.appendChild(div);
        const textNode = div.querySelector('.msg-text-node');
        if (text.includes('</span>') || text.includes('code-frame')) {
            this.renderContent(textNode, text);
        } else {
            this.renderContent(textNode, this.parseContent(text));
        }
        return div;
    },

    async run() {
        const val = this.mainInput.value.trim();
        if (!val || this.isSending) return;

        // Transition from Hero Home to Chat View
        const hero = document.querySelector('.hero');
        const bottomInput = document.getElementById('bottomInput');
        const mainContent = document.querySelector('.main-content');
        if (hero) {
            hero.style.display = 'none';
            if (this.chatList) this.chatList.style.display = 'block';
            if (bottomInput) bottomInput.style.display = 'block';
            if (this.scrollContainer) {
                this.scrollContainer.style.display = 'flex';
                this.scrollContainer.style.height = '100%';
                this.scrollContainer.style.width = '100%';
            }
            mainContent.style.justifyContent = 'flex-start';
            mainContent.style.padding = '0';
        }

        this.setSendingState(true);

        this.append('user', val);
        this.mainInput.value = '';
        this.mainInput.style.height = 'auto';
        this.scrollToBottom();

        const loadingId = 'loading-' + Date.now();
        const node = this.append('baki', '<span class="loading-dots glitch-text">INITIALIZING QUANTUM TRUTH...</span>', loadingId);
        this.syncLayout();
        this.scrollToBottom();

        try {
            const userLangPref = localStorage.getItem('ds_user_lang') || 'Arabic';
            const response = await fetch('api.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ message: val, conversation_id: this.convId, csrf_token: this.csrfToken, language: userLangPref })
            });

            if (!response.ok) {
                const error = await response.json().catch(() => ({message: 'Connection Severed'}));
                node.querySelector('.msg-text-node').textContent = `SYSTEM_ERROR: ${error.message}`;
                return;
            }

            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let fullText = '';
            let thoughtText = '';
            const textContainer = node.querySelector('.msg-text-node');
            textContainer.innerHTML = ''; 

            let isThinking = false;
            let buffer = ""; // Cumulative buffer for fragmented SSE data

            while (true) {
                const { done, value } = await reader.read();
                if (done) break;

                const chunk = decoder.decode(value, { stream: true });
                buffer += chunk;

                let lines = buffer.split('\n');
                buffer = lines.pop(); // Keep the last incomplete fragment in the buffer
                
                for (let line of lines) {
                    let trimLine = line.trim();
                    if (!trimLine.startsWith('data: ')) continue;
                    
                    let dataStr = trimLine.substring(6).trim();
                    if (dataStr === '[DONE]') continue;
                    
                    try {
                        const json = JSON.parse(dataStr);
                        
                        // Sync conversation context
                        if (json.conversation_id) {
                            const newId = String(json.conversation_id);
                            const currentId = String(this.convId || '');

                            if (currentId !== newId) {
                                // New conversation was created — sync state once
                                console.log("SYNC_SESSION_ID: " + newId);
                                this.convId = newId;
                                document.body.dataset.convId = newId;
                                // Persist across popstate / back navigation
                                sessionStorage.setItem('bakigpt_conv_id', newId);

                                const url = new URL(window.location);
                                url.searchParams.set('id', newId);
                                window.history.replaceState({ path: url.toString() }, '', url.toString());

                                // Add to sidebar (updateSidebar guards against duplicates)
                                this.updateSidebar(newId, val);
                            }
                        }

                        if (json.error) {
                            textContainer.innerHTML = `<div style="color: red; font-weight: bold; padding: 10px; border: 1px solid red; background: rgba(255,0,0,0.1); border-radius: 5px;">${json.error.message || 'API Error'}</div>`;
                            break;
                        }

                        const reasoning = json.choices?.[0]?.delta?.reasoning_content || '';
                        const content = json.choices?.[0]?.delta?.content || '';

                        if (reasoning) {
                            if (!isThinking) {
                                isThinking = true;
                                let thDiv = document.createElement('div');
                                thDiv.className = 'thought-container';
                                this.renderContent(thDiv, `<div class="thought-header"><i class="fa-solid fa-brain"></i> DeepThink Process</div><div class="thought-content"></div>`);
                                textContainer.appendChild(thDiv);
                            }
                            thoughtText += reasoning;
                            textContainer.querySelector('.thought-content').innerText = thoughtText;
                        }

                        if (content) {
                            isThinking = false; 
                            fullText += content;
                            let contentDiv = textContainer.querySelector('.final-content');
                            if (!contentDiv) {
                                contentDiv = document.createElement('div');
                                contentDiv.className = 'final-content';
                                textContainer.appendChild(contentDiv);
                            }
                            this.renderContent(contentDiv, this.parseContent(fullText));
                        }
                        this.scrollToBottom();
                    } catch (innerE) {
                        // Suppress parse errors safely as TCP fragmentation is now buffered.
                    }
                }
            }
            // Stream complete - Add action buttons
            const wrapper = node.querySelector('.msg-text-node-wrapper');
            if (wrapper && !wrapper.querySelector('.msg-actions')) {
                 wrapper.insertAdjacentHTML('beforeend', `
                 <div class="msg-actions">
                     <button class="action-btn" type="button" title="Copy" data-action="copy-snippet">Copy</button>
                     <button class="action-btn"><i class="fa-regular fa-thumbs-up"></i></button>
                     <button class="action-btn"><i class="fa-regular fa-thumbs-down"></i></button>
                     <button class="action-btn"><i class="fa-solid fa-share-nodes"></i></button>
                 </div>`);
            }
        } catch (e) {
            console.error("UPLINK_CRITICAL:", e);
            node.querySelector('.msg-text-node').textContent = 'FATAL_ERROR: Uplink Lost.';
        } finally {
            this.setSendingState(false);
            this.mainInput.focus();
        }
    }
};

window.addEventListener('DOMContentLoaded', () => {
    try {
        app.init();
    } catch (e) {
        console.error("BOOT_ERROR:", e);
    }
});
