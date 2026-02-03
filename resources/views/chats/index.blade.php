@extends('layouts.chat_base')

@section('title', 'Chat')

@section('content')
    <div class="app">

        <style>
            /* Layout de mensagens: garante coluna e espaçamento */
            .messages {
                display: flex;
                flex-direction: column;
                gap: 8px;
                padding: 12px;
            }

            /* Cada mensagem é um container flexível; as enviadas ficam alinhadas à direita */
            .message {
                display: flex;
                align-items: flex-end;
                margin: 6px 12px;
            }

            /* Mensagens enviadas alinhadas à direita */
            .message.sent {
                justify-content: flex-end;
            }

            /* Garante que o bubble da mensagem enviada fique colado à direita */
            .message.sent .bubble {
                margin-left: auto;
            }

            /* Meta (horário) menor, branca e alinhada à direita */
            .messages .message .meta {
                font-size: 0.75rem; /* cerca de 12px */
                color: rgba(255,255,255,0.9);
                margin-top: 6px;
                text-align: right;
            }
        </style>

        {{-- Sidebar --}}
        <aside class="sidebar" style="background-color: #161717;">
            <header class="sidebar-header">
                <span><svg viewBox="0 0 104 28" height="28" width="104" preserveAspectRatio="xMidYMid meet" class="" fill="none"><title>wa-wordmark-refreshed</title><path d="m13.07 21.343-2.681-10.767h-.045L7.708 21.343H4.186L0 5.523h2.981L5.84 17.621h.05L8.973 5.523h2.828l2.997 12.098h.019L17.86 5.523h2.915l-4.252 15.82h-3.456zm21.602-9.771q.486-.732 1.24-1.173a5.4 5.4 0 0 1 1.696-.632c.626-.125 1.079-.188 1.713-.188q.863 0 1.749.122c.59.081.965.24 1.453.476q.729.356 1.194.987.466.63.466 1.672v5.96q0 .778.09 1.484.086.71.31 1.063H41.56a4 4 0 0 1-.144-.543 5 5 0 0 1-.078-.565 4.1 4.1 0 0 1-1.773 1.088 7.1 7.1 0 0 1-2.08.309c-.547 0-.891-.066-1.364-.2a3.5 3.5 0 0 1-1.24-.622 2.9 2.9 0 0 1-.83-1.064q-.3-.642-.3-1.529 0-.975.342-1.606.344-.632.886-1.01.544-.377 1.241-.563c.465-.126.769-.225 1.241-.3q.71-.108 1.395-.178c.458-.043 1.03-.109 1.384-.198q.532-.132.84-.389.31-.254.288-.742 0-.507-.167-.808-.165-.3-.443-.466c-.185-.11-.565-.183-.808-.221a5 5 0 0 0-.786-.055c-.622 0-1.246.132-1.6.398q-.532.4-.622 1.33h-2.827q.066-1.107.553-1.839zm6.034 4.442a5 5 0 0 1-.643.167q-.342.065-.72.111t-.752.11c-.236.045-.635.105-.863.178a2.1 2.1 0 0 0-.598.299q-.256.19-.41.476-.154.287-.155.732c0 .296.053.515.155.707a1.2 1.2 0 0 0 .422.455q.264.166.62.23.355.069.73.069c.621 0 1.43-.104 1.77-.311q.508-.31.753-.742.244-.43.3-.876.056-.44.056-.709v-1.173c-.134.119-.465.21-.663.276zm32.818-.899L71.26 8.523h-.076l-2.337 6.587 4.679.005zm-.816-9.592 5.913 15.82h-2.955l-1.43-4.215h-6.098l-1.482 4.215h-2.909l5.98-15.82zM86.179 18.97q.522-.308.842-.807.32-.498.455-1.164.13-.665.13-1.35.001-.686-.143-1.353a3.6 3.6 0 0 0-.476-1.185 2.64 2.64 0 0 0-.853-.841q-.52-.323-1.275-.323c-.502 0-.948.11-1.293.323q-.523.32-.843.83a3.5 3.5 0 0 0-.455 1.174q-.133.664-.132 1.374c0 .474.046.907.144 1.35q.144.664.464 1.163.323.5.853.808.532.31 1.284.311c.502 0 .949-.104 1.294-.31zm-4.074-9.082v1.463h.044q.575-.93 1.461-1.352c.59-.281 1.075-.42 1.784-.42.9 0 1.508.169 2.16.509q.975.509 1.616 1.352t.953 1.96q.309 1.12.31 2.338-.001 1.152-.31 2.217a5.7 5.7 0 0 1-.942 1.882 4.65 4.65 0 0 1-1.573 1.307c-.628.324-1.197.488-2.038.488-.709 0-1.198-.146-1.794-.433a3.7 3.7 0 0 1-1.475-1.273h-.043v5.43h-2.827V9.89h2.67zm16.278 9.082a2.5 2.5 0 0 0 .843-.807q.32-.498.454-1.164.131-.665.131-1.35a6.3 6.3 0 0 0-.144-1.353 3.6 3.6 0 0 0-.475-1.185 2.64 2.64 0 0 0-.853-.841q-.52-.323-1.275-.323c-.502 0-.948.11-1.293.323q-.524.32-.844.83a3.5 3.5 0 0 0-.454 1.174q-.133.664-.132 1.374c0 .474.046.907.144 1.35q.144.664.464 1.163.323.5.853.808.531.31 1.284.311c.502 0 .948-.104 1.294-.31zM94.31 9.889v1.463h.044q.575-.93 1.461-1.352c.59-.281 1.074-.42 1.783-.42.901 0 1.509.169 2.16.509q.976.509 1.616 1.352.642.843.954 1.96.308 1.12.309 2.338 0 1.152-.309 2.217a5.7 5.7 0 0 1-.942 1.882 4.64 4.64 0 0 1-1.573 1.307c-.628.324-1.197.488-2.038.488-.709 0-1.198-.146-1.795-.433a3.7 3.7 0 0 1-1.474-1.273h-.043v5.43h-2.828V9.89h2.671zm-38.355 8.705q.21.367.544.598.33.233.765.344c.287.074.874.11 1.185.11.221 0 .495-.026.74-.077.243-.051.587-.132.788-.243q.3-.165.498-.443c.133-.185.198-.444.198-.725 0-.428-.436-.898-1.064-1.134q-.94-.356-2.626-.709a15 15 0 0 1-1.339-.367 4.5 4.5 0 0 1-1.163-.553 2.7 2.7 0 0 1-.819-.865q-.31-.52-.31-1.274 0-1.107.433-1.816a3.2 3.2 0 0 1 1.142-1.119 5 5 0 0 1 1.595-.577 8.2 8.2 0 0 1 1.65-.165c.622 0 1.055.058 1.639.177a4.8 4.8 0 0 1 1.561.598q.687.421 1.14 1.117.456.699.542 1.762h-2.659c-.043-.605-.272-1.08-.686-1.292-.414-.215-1.046-.344-1.608-.344-.176 0-.664.04-.862.068s-.43.086-.6.158q-.255.112-.433.322c-.119.141-.177.392-.177.629q-.001.42.31.685.309.268.807.433.499.166 1.142.3c.428.089 1.167.295 1.608.4.458.104.739.227 1.175.375q.652.223 1.164.588.509.367.82.909.309.541.309 1.34 0 1.13-.453 1.896-.455.765-1.185 1.23c-.488.31-.88.39-1.508.515a10 10 0 0 1-1.917.188 8 8 0 0 1-1.781-.2 5.2 5.2 0 0 1-1.696-.664q-.742-.465-1.218-1.23c-.319-.509-.49-1.148-.522-1.917h2.66c0 .34.07.73.21.974zm-3.751-8.698v1.939H49.9v6.002q0 .799.264 1.064.266.266 1.064.267.266-.001.51-.023t.465-.067v2.273a7 7 0 0 1-.885.089q-.488.02-.954.021c-.486 0-.95-.033-1.383-.099a2.07 2.07 0 0 1-.998-.396 1.9 1.9 0 0 1-.635-.82 3.5 3.5 0 0 1-.274-1.396v-6.923H45.17V9.888h1.904V6.454l2.828.008v3.434zM24.65 5.523v5.902h.065c.398-.664 1.03-1.17 1.65-1.472.621-.305 1.27-.376 1.86-.376.841 0 1.202.114 1.74.342q.81.344 1.273.954.466.61.654 1.484.189.877.189 1.939v7.045h-2.815v-6.47q.001-1.419-.442-2.116c-.296-.466-.822-.81-1.572-.864-.967-.07-1.643.324-2.026.833-.385.51-.577 1.444-.577 2.611v6.004h-2.828V5.523z" fill="currentColor" class="xd25hor"></path></svg></span>
            </header>

            <div class="chat-list" id="chat-list">
                <div class="loading">Carregando conversas...</div>
            </div>
        </aside>

        {{-- Chat --}}
        <main class="chat">
            <header class="chat-header">
                <img src="#" alt="">
                <span></span>
            </header>

            <div class="messages">
                <div class="message received"></div>
                <div class="message sent"></div>
            </div>

            <footer class="chat-input">

                <button class="icon-btn emoji-btn" type="button" aria-haspopup="true" aria-expanded="false">
                    😊
                </button>

                <div id="editor-container" tabindex="0" class="is-empty" contenteditable="true" data-placeholder="Digite uma mensagem"></div>

                <div id="emoji-picker" class="emoji-picker" aria-hidden="true" role="dialog">
                    <!-- Pequena paleta de emojis embutida -->
                    <button type="button" data-emoji="😀">😀</button>
                    <button type="button" data-emoji="😂">😂</button>
                    <button type="button" data-emoji="😍">😍</button>
                    <button type="button" data-emoji="😅">😅</button>
                    <button type="button" data-emoji="😢">😢</button>
                    <button type="button" data-emoji="👍">👍</button>
                    <button type="button" data-emoji="🙏">🙏</button>
                </div>

                <button class="icon-btn send-btn" type="button" id="sendMessage">
                    ➤
                </button>

            </footer>

        </main>

    </div>
@endsection
@push('scripts')
    <script>
        // Editor baseado em contenteditable (substitui Quill)
        document.addEventListener('DOMContentLoaded', function () {
            const editor = document.getElementById('editor-container');
            const sendBtn = document.getElementById('sendMessage');
            const emojiBtn = document.querySelector('.emoji-btn');
            const emojiPicker = document.getElementById('emoji-picker');
            const chatList = document.getElementById('chat-list');

            // Função para buscar chats via API e popular a lista
            async function fetchChats() {
                if (!chatList) return;
                chatList.innerHTML = '<div class="loading">Carregando conversas...</div>';
                try {
                    const res = await fetch('/all-chats', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    });

                    if (res.status === 401) {
                        chatList.innerHTML = '<div class="loading">Não autorizado. Faça login.</div>';
                        return;
                    }

                    if (!res.ok) {
                        chatList.innerHTML = '<div class="loading">Erro ao carregar conversas.</div>';
                        console.error('Erro fetch /api/all-chats', res.statusText);
                        return;
                    }

                    const payload = await res.json();

                    // Normaliza payload para array de chats
                    let chats = [];
                    if (Array.isArray(payload)) chats = payload;
                    else if (Array.isArray(payload.data)) chats = payload.data;
                    else if (Array.isArray(payload.chats)) chats = payload.chats;
                    else if (payload && typeof payload === 'object') {
                        // tenta inferir
                        chats = payload.items || payload.results || [];
                    }

                    if (!chats.length) {
                        chatList.innerHTML = '<div class="loading">Nenhuma conversa encontrada.</div>';
                        return;
                    }

                    // Popula a lista
                    chatList.innerHTML = '';
                    chats.forEach(function (c) {
                        const item = document.createElement('div');
                        item.className = 'chat-item';

                        const avatar = c.avatar;
                        const name = c.name || c.title || c.contactName || c.phone || 'Sem nome';
                        const preview = c.last_message || c.preview || c.lastMessage || '';

                        // garante que o src nunca fique vazio (evita requests para a página atual)
                        const placeholderAvatar = 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%2240%22 height=%2240%22 fill=%22%231b1f23%22/></svg>';
                        const avatarSrcLocal = (avatar && avatar !== 'undefined' && avatar !== '#') ? avatar : placeholderAvatar;
                        item.innerHTML = `
                            <img src="${avatarSrcLocal}" alt="avatar">
                            <div>
                                <strong>${escapeHtml(name)}</strong>
                                <p>${escapeHtml(preview)}</p>
                            </div>
                        `;
                        // armazena o avatar também no dataset para leitura confiável ao selecionar
                        item.dataset.avatar = avatarSrcLocal;

                        // clique para abrir conversa (pode ser implementado depois)
                        item.addEventListener('click', function () {
                            // Ao clicar, carrega as mensagens do chat
                            loadChat(c, item);
                        });

                        chatList.appendChild(item);
                    });
                } catch (err) {
                    console.error('Erro ao buscar chats', err);
                    if (chatList) chatList.innerHTML = '<div class="loading">Erro ao carregar conversas.</div>';
                }
            }

            function escapeHtml(str) {
                if (!str && str !== 0) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            // Inicializa busca de chats assim que a tela carregar
            fetchChats();

            if (!editor) {
                console.error('Editor container não encontrado.');
                return;
            }

            // Garantir comportamento básico
            editor.setAttribute('contenteditable', 'true');
            editor.setAttribute('data-placeholder', editor.getAttribute('data-placeholder') || 'Digite uma mensagem');
            editor.setAttribute('aria-multiline', 'true');
            editor.style.whiteSpace = 'pre-wrap';

            function cleanEmptyLines() {
                // Remove nós estranhos que alguns navegadores inserem (ex.: <div><br></div>) quando vazio
                if (editor.innerHTML === '<br>' || editor.innerHTML === '<div><br></div>') {
                    editor.innerHTML = '';
                }
            }

            function updatePlaceholderState() {
                // Considera vazio quando texto sem espaços
                const txt = editor.textContent.replace(/\u00A0/g, ' ').trim();
                if (!txt) {
                    editor.classList.add('is-empty');
                } else {
                    editor.classList.remove('is-empty');
                }
            }

            function insertEmojiAtCaret(emoji) {
                editor.focus();
                // Tenta usar execCommand (compatível com a maioria dos navegadores) para inserir texto
                try {
                    if (document.queryCommandSupported && document.queryCommandSupported('insertText')) {
                        document.execCommand('insertText', false, emoji);
                        updatePlaceholderState();
                        return;
                    }
                } catch (e) {
                    // fallback abaixo
                }

                const sel = window.getSelection();
                if (!sel || !sel.rangeCount) {
                    editor.appendChild(document.createTextNode(emoji));
                    updatePlaceholderState();
                    return;
                }
                const range = sel.getRangeAt(0);
                range.deleteContents();
                const node = document.createTextNode(emoji);
                range.insertNode(node);
                // Move o cursor para depois do emoji inserido
                range.setStartAfter(node);
                range.setEndAfter(node);
                sel.removeAllRanges();
                sel.addRange(range);
                updatePlaceholderState();
            }

            // Toggle emoji picker
            function toggleEmojiPicker() {
                const isOpen = emojiPicker.classList.toggle('show');
                emojiBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                emojiPicker.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            }

            emojiBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation(); // evita que o document click feche imediatamente
                toggleEmojiPicker();
            });

            // Inserir emoji via palette
            emojiPicker.addEventListener('click', function (e) {
                e.stopPropagation(); // evita que o document click feche o picker antes de processar
                const btn = e.target.closest('button[data-emoji]');
                if (!btn) return;
                const emoji = btn.dataset.emoji;
                insertEmojiAtCaret(emoji);
                // Fecha picker e mantém o foco no editor
                emojiPicker.classList.remove('show');
                emojiPicker.setAttribute('aria-hidden', 'true');
                emojiBtn.setAttribute('aria-expanded', 'false');
                editor.focus();
            });

            // Fecha picker ao clicar fora
            document.addEventListener('click', function (e) {
                if (!emojiPicker.contains(e.target) && e.target !== emojiBtn) {
                    emojiPicker.classList.remove('show');
                    emojiPicker.setAttribute('aria-hidden', 'true');
                    emojiBtn.setAttribute('aria-expanded', 'false');
                }
            });

            // Envio com Enter, nova linha com Shift+Enter
            editor.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });

            // Atualiza placeholder ao digitar/colar
            editor.addEventListener('input', function () {
                updatePlaceholderState();
                cleanEmptyLines();
            });
            editor.addEventListener('keyup', updatePlaceholderState);
            editor.addEventListener('paste', function (e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                // Insere texto plano na posição atual do cursor
                if (document.queryCommandSupported && document.queryCommandSupported('insertText')) {
                    document.execCommand('insertText', false, text);
                } else {
                    const sel = window.getSelection();
                    if (sel && sel.rangeCount) {
                        const range = sel.getRangeAt(0);
                        range.deleteContents();
                        range.insertNode(document.createTextNode(text));
                        range.collapse(false);
                    } else {
                        editor.appendChild(document.createTextNode(text));
                    }
                }
                updatePlaceholderState();
            });

            function sendMessage() {
                cleanEmptyLines();
                const text = editor.innerText.replace(/\u00A0/g, ' ').trim(); // remove nbsp
                if (!text) return;

                // Aqui você enviaria via AJAX/WebSocket — por enquanto logamos no console
                console.log('Mensagem enviada:', text);

                // Limpa editor
                editor.innerHTML = '';
                updatePlaceholderState();
                editor.focus();
            }

            sendBtn.addEventListener('click', function () {
                sendMessage();
            });

            // Inicializa estado do placeholder
            updatePlaceholderState();

            // Pequeno helper para debugging
            window.__editorInstance = editor;

            // Função que seleciona um chat e carrega suas mensagens
            async function loadChat(chatObj, itemElement) {
                // marca visualmente como ativo
                document.querySelectorAll('.chat-item.active').forEach(function (el) {
                    el.classList.remove('active');
                });
                if (itemElement) itemElement.classList.add('active');

                // DEBUG: log para ajudar a identificar problemas com avatars
                try {
                    console.debug('loadChat called', {
                        id: chatObj && chatObj.id,
                        name: chatObj && chatObj.name,
                        datasetAvatar: itemElement && itemElement.dataset && itemElement.dataset.avatar
                    });
                    const listImgDebug = itemElement ? itemElement.querySelector('img') : null;
                    console.debug('listImg src:', listImgDebug ? listImgDebug.getAttribute('src') : null, 'chatObj.avatar:', chatObj && chatObj.avatar);
                } catch (e) {
                    console.debug('loadChat debug error', e);
                }

                // atualiza header com avatar/nome
                const headerImg = document.querySelector('.chat-header img');
                const headerName = document.querySelector('.chat-header span');

                // Pega diretamente o src do <img> dentro do item (garante mesma imagem/tamanho)
                let avatarSrc = 'data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2240%22 height=%2240%22><rect width=%2240%22 height=%2240%22 fill=%22%231b1f23%22/></svg>';
                if (itemElement) {
                    const listImg = itemElement.querySelector('img');
                    if (listImg && listImg.getAttribute('src')) avatarSrc = listImg.getAttribute('src');
                }
                if ((!avatarSrc || avatarSrc === 'undefined' || avatarSrc === '#') && chatObj && chatObj.avatar) {
                    avatarSrc = chatObj.avatar;
                }

                // Normaliza URL: se for relativa, transforma em absoluta usando origin
                try {
                    if (avatarSrc && !avatarSrc.startsWith('data:') && !/^https?:\/\//i.test(avatarSrc)) {
                        avatarSrc = new URL(avatarSrc, window.location.origin).toString();
                    }
                } catch (e) {
                    // ignore URL normalization errors
                }

                if (headerImg) {
                    try {
                        // atualiza src e garante mesmos estilos do avatar da lista
                        headerImg.src = avatarSrc;
                        headerImg.alt = chatObj.name || '';
                        headerImg.style.width = '40px';
                        headerImg.style.height = '40px';
                        headerImg.style.objectFit = 'cover';
                        headerImg.style.borderRadius = '50%';
                        headerImg.style.display = 'inline-block';
                    } catch (e) {
                        headerImg.setAttribute('src', avatarSrc);
                        headerImg.setAttribute('alt', chatObj.name || '');
                    }
                }
                if (headerName) headerName.textContent = chatObj.name || chatObj.title || chatObj.phone || '';

                // busca mensagens
                await fetchChatMessages(chatObj.id);
            }

            // Formata timestamp UNIX (segundos) para HH:MM
            function formatTime(timestamp) {
                try {
                    const ts = parseInt(timestamp, 10);
                    if (isNaN(ts)) return '';
                    const d = new Date(ts * 1000);
                    const hh = String(d.getHours()).padStart(2, '0');
                    const mm = String(d.getMinutes()).padStart(2, '0');
                    return `${hh}:${mm}`;
                } catch (e) {
                    return '';
                }
            }

            // Busca mensagens do chat via rota get-chat?chatId=...
            async function fetchChatMessages(chatId) {
                const messagesContainer = document.querySelector('.messages');
                if (!messagesContainer) return;

                // Limpa e mostra loading + progressbar
                messagesContainer.innerHTML = '\n                    <div class="loading">Carregando mensagens...</div>\n                    <div class="messages-progress" aria-hidden="true" style="margin:6px 12px;height:6px;background:#eee;border-radius:3px;overflow:hidden;display:none">\n                      <div class="messages-progress-bar" style="width:0;height:100%;background:#161717;"></div>\n                    </div>';

                const progressWrap = messagesContainer.querySelector('.messages-progress');
                const progressBar = messagesContainer.querySelector('.messages-progress-bar');
                if (progressWrap) progressWrap.style.display = 'block';

                try {
                    const res = await fetch('/get-chat?chatId=' + encodeURIComponent(chatId), {
                        method: 'GET',
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin'
                    });

                    if (!res.ok) {
                        messagesContainer.innerHTML = '<div class="loading">Erro ao carregar mensagens.</div>';
                        console.error('Erro fetch /get-chat', res.statusText);
                        return;
                    }

                    const payload = await res.json();
                    let messages = [];
                    if (Array.isArray(payload)) messages = payload;
                    else if (Array.isArray(payload.data)) messages = payload.data;

                    if (!messages.length) {
                        messagesContainer.innerHTML = '<div class="loading">Sem mensagens nesta conversa.</div>';
                        return;
                    }

                    // Ordena por timestamp ascendente (cronológico)
                    messages.sort(function (a, b) {
                        const ta = (a && a.timestamp) ? parseInt(a.timestamp, 10) : 0;
                        const tb = (b && b.timestamp) ? parseInt(b.timestamp, 10) : 0;
                        return ta - tb;
                    });

                    // Prepara área de mensagens limpa
                    messagesContainer.innerHTML = '';

                    // Renderiza mensagens com progressbar atualizado
                    for (let i = 0; i < messages.length; i++) {
                        const m = messages[i];

                        // Atualiza progress
                        if (progressBar) {
                            const pct = Math.round(((i + 1) / messages.length) * 100);
                            progressBar.style.width = pct + '%';
                        }

                        const isFromMe = !!m.fromMe;
                        const type = m.type || 'chat';

                        const msgEl = document.createElement('div');
                        msgEl.className = 'message ' + (isFromMe ? 'sent' : 'received');

                        // bubble
                        const bubble = document.createElement('div');
                        bubble.className = 'bubble';

                        // Conteúdo: se for imagem, mostra imagem (media) e depois o texto
                        if (type === 'image' && m.media) {
                            const img = document.createElement('img');
                            img.src = m.media;
                            img.alt = m.body ? m.body.substring(0, 60) : 'image';
                            img.style.maxWidth = '220px';
                            img.style.display = 'block';
                            img.style.marginBottom = '6px';
                            bubble.appendChild(img);
                        }

                        if (m.body && String(m.body).trim().length) {
                            const p = document.createElement('div');
                            p.className = 'text';
                            // Mantém quebras de linha
                            p.textContent = m.body;
                            p.style.whiteSpace = 'pre-wrap';
                            bubble.appendChild(p);
                        }

                        // Rodapé da mensagem com horário
                        const footer = document.createElement('div');
                        footer.className = 'meta';
                        footer.textContent = formatTime(m.timestamp) || '';
                        bubble.appendChild(footer);

                        msgEl.appendChild(bubble);
                        messagesContainer.appendChild(msgEl);
                    }

                    // Depois que carregou, garante scroll para o final
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;

                } catch (err) {
                    console.error('Erro ao buscar chat', err);
                    messagesContainer.innerHTML = '<div class="loading">Erro ao carregar mensagens.</div>';
                } finally {
                    if (progressWrap) progressWrap.style.display = 'none';
                }
            }
        });
    </script>
@endpush
