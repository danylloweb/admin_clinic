@extends('layouts.header')
@section('content')
<div class="container py-4">
  <div class="row g-3">
    <!-- Aside / Contact card (sem "All Contacts") -->
    <aside class="col-12 col-md-4">
      <div class="card h-100 text-white" style="background-color: #161717;">
        <div class="card-body p-4 text-center">
          <div class="mb-3">
            <img id="patient-avatar" onclick="openPhotoModalWithSrc('{{ $photo }}')" src="{{ $photo ?? ('https://ui-avatars.com/api/?name='.urlencode($patient->name)) }}" alt="{{ $patient->name }}" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;cursor:pointer;" data-src="{{ $patient->avatar ?? ('https://ui-avatars.com/api/?name='.urlencode($patient->name)) }}">
          </div>
          <h4 class="card-title mb-1">{{ $patient->name }}</h4>
          <p class="text-muted small mb-2">{{ $patient->phone }}</p>

          <div class="d-flex justify-content-center gap-2 mb-3">
            <a href="tel:{{ $patient->phone }}" class="btn btn-sm btn-light">Ligar</a>
            <a href="javascript:void(0)" class="btn btn-sm btn-outline-light" id="start-chat-btn">Enviar mensagem</a>
          </div>

          <hr style="border-color: rgba(255,255,255,0.08);">

          <div class="text-start small">
            <p class="mb-1"><strong>Id do chat:</strong> <span class="text-white-50">{{ $patient->chat_id ?? '-' }}</span></p>
            <p class="mb-1"><strong>Data de nascimento:</strong> <span class="text-white-50">{{ \Carbon\Carbon::create($patient->birth_date)->format("d/m/Y") }}</span></p>
            <p class="mb-0"><strong>Sexo:</strong> <span class="text-white-50">{{ $patient->sex ?? '-' }}</span></p>
          </div>
        </div>
        <div class="card-footer bg-transparent border-top-0 text-center small">
          <a href="{{ route('panel.patient.index') }}" class="btn btn-sm btn-outline-light">Voltar</a>
        </div>
      </div>
    </aside>

    <!-- Main / Messages area -->
    <main class="col-12 col-md-8">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between align-items-center">
          <div>
            <strong>{{ $patient->name }}</strong>
            <div class="text-muted small">{{ $patient->phone }}</div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary">Info</button>
            <button class="btn btn-sm btn-outline-danger">Arquivar</button>
          </div>
        </div>

        <div class="card-body d-flex flex-column" style="min-height:420px;">
          <!-- mensagens (exemplo estático, o JS pode popular aqui) -->
          <div id="messages-list" class="flex-grow-1 overflow-auto mb-3">
            <div class="d-flex flex-column gap-3">
              {{-- Exemplo de mensagem recebida --}}
              <div class="d-flex">
                <div class="me-2">
                  <img src="{{ $photo ?? ('https://ui-avatars.com/api/?name='.urlencode($patient->name)) }}" alt="avatar" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;"/>
                </div>
                <div>
                  <div class="bg-light p-2 rounded" style="max-width:70%;">
                    <div class="mb-1">Olá! Como podemos ajudar?</div>
                    <div class="text-muted small">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
                  </div>
                </div>
              </div>

              {{-- Exemplo de mensagem enviada (alinhada à direita) --}}
              <div class="d-flex justify-content-end">
                <div class="text-end">
                  <div class="bg-primary text-white p-2 rounded" style="max-width:70%;">
                    <div>Perfeito, obrigado!</div>
                    <div class="meta small text-white-50">{{ \Carbon\Carbon::now()->format('H:i') }}</div>
                  </div>
                </div>
              </div>

            </div>
          </div>

          <!-- input area -->
          <div>
            <form id="message-form" class="d-flex gap-2" onsubmit="return false;">
              <input id="message-input" class="form-control" placeholder="Escreva uma mensagem..." />
              <button id="send-message-btn" class="btn btn-primary">Enviar</button>
            </form>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<!-- Modal para visualizar avatar em tamanho maior -->
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0 text-center position-relative">
        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Fechar"></button>
        <img id="photoModalImg" src="{{ $photo }}" alt="Foto" style="max-width:100%; height:auto; display:block; margin:0 auto; border-radius:6px;"/>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
<style>
  /* Ajustes locais para ficar parecido com o exemplo: */
  #messages-list .meta { font-size: 0.75rem; }
  /* meta branca e alinhada a direita quando mensagem enviada */
  .d-flex.justify-content-end .meta { color: #ffffff; text-align: right; }
  /* assegura que aside tenha o background solicitado */
  aside { background-color: #161717; }
</style>
@endpush

@push('scripts')
<script>
  // Helpers para remover backdrops remanescentes e restaurar estado do body
  function _removeBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.paddingRight = '';
    document.body.style.overflow = '';
  }

  function closePhotoModal() {
    const modalEl = document.getElementById('photoModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.hide();
    const img = document.getElementById('photoModalImg');
    if (img) img.src = '';
    setTimeout(_removeBackdrops, 200);
  }

  function openPhotoModalWithSrc(src) {
    const modalEl = document.getElementById('photoModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;
    _removeBackdrops();
    document.getElementById('photoModalImg').src = src;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  }

  document.addEventListener('DOMContentLoaded', function () {
    // torna o avatar clicável
    // const avatar = document.getElementById('patient-avatar');
    // if (avatar) {
    //   avatar.addEventListener('click', function () {
    //     const src = this.dataset.src || this.src;
    //     openPhotoModalWithSrc(src);
    //   });
    // }

    // fecha o modal ao clicar na imagem, no fundo ou no backdrop
    const modalEl = document.getElementById('photoModal');
    if (modalEl) {
      modalEl.addEventListener('click', function (e) {
        if (e.target === modalEl || e.target.id === 'photoModalImg') {
          closePhotoModal();
        }
      });
      modalEl.addEventListener('hidden.bs.modal', function () {
        const img = document.getElementById('photoModalImg');
        if (img) img.src = '';
        _removeBackdrops();
      });
    }

    document.addEventListener('click', function (e) {
      if (e.target && e.target.classList && e.target.classList.contains('modal-backdrop')) {
        closePhotoModal();
      }
    });

    // botão de exemplo para iniciar chat (pode ser ligado a sua lógica)
    const startChatBtn = document.getElementById('start-chat-btn');
    if (startChatBtn) {
      startChatBtn.addEventListener('click', function () {
        // exemplo: redirecionar para a route de chat usando chat_id
        const chatId = '{{ $patient->chat_id ?? '' }}';
        if (!chatId) return alert('Chat não disponível');
        window.location.href = '{{ url("/chats/view") }}' + '?chatId=' + encodeURIComponent(chatId);
      });
    }

    // envio de mensagem demo (não envia ao servidor por padrão)
    const sendBtn = document.getElementById('send-message-btn');
    const msgInput = document.getElementById('message-input');
    const messagesList = document.getElementById('messages-list');
    if (sendBtn && msgInput && messagesList) {
      sendBtn.addEventListener('click', function (e) {
        e.preventDefault();
        const text = msgInput.value.trim();
        if (!text) return;
        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex justify-content-end mb-2';
        wrapper.innerHTML = `
          <div class="text-end">
            <div class="bg-primary text-white p-2 rounded" style="max-width:70%;">
              <div>${text.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</div>
              <div class="meta small text-white-50">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
            </div>
          </div>
        `;
        messagesList.querySelector('.d-flex.flex-column')?.appendChild(wrapper);
        msgInput.value = '';
        // scroll to bottom
        messagesList.scrollTop = messagesList.scrollHeight;
      });
    }
  });
</script>
@endpush
