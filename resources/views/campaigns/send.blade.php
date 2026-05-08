@extends('layouts.header')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Disparo da Campanha</h5>
            <small class="text-muted">{{ $campaign['name'] }}</small>
        </div>
        <a href="{{ route('panel-campaign-show', ['id' => $campaign['id']]) }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3">O envio acontece paginado com 3 segundos de intervalo entre cada lote para não sobrecarregar o WhatsApp.</p>

        <div class="mb-3">
            <label class="form-label">Tamanho do lote (mensagens por envio)</label>
            <input type="number" id="messages-per-batch" class="form-control" value="5" min="1" max="100">
        </div>

        <div class="mb-3">
            <label class="form-label">Progresso do envio</label>
            <div class="progress" style="height: 24px;">
                <div id="campaign-send-progressbar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;">0%</div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-3"><div class="border rounded p-2"><small class="text-muted">Total</small><div id="stat-total" class="fw-semibold">0</div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><small class="text-muted">Enviados</small><div id="stat-sent" class="fw-semibold text-success">0</div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><small class="text-muted">Falhas</small><div id="stat-failed" class="fw-semibold text-danger">0</div></div></div>
            <div class="col-md-3"><div class="border rounded p-2"><small class="text-muted">Processados</small><div id="stat-processed" class="fw-semibold">0</div></div></div>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <button id="btn-start-send" class="btn btn-primary">Iniciar envio</button>
            <button id="btn-pause-send" class="btn btn-warning">Pausar</button>
            <button id="btn-resume-send" class="btn btn-info">Retomar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        const campaignId = {{ (int) $campaign['id'] }};
        const progressBar = document.getElementById('campaign-send-progressbar');
        const totalEl = document.getElementById('stat-total');
        const sentEl = document.getElementById('stat-sent');
        const failedEl = document.getElementById('stat-failed');
        const processedEl = document.getElementById('stat-processed');
        const startBtn = document.getElementById('btn-start-send');
        const pauseBtn = document.getElementById('btn-pause-send');
        const resumeBtn = document.getElementById('btn-resume-send');
        const messagesPerBatchInput = document.getElementById('messages-per-batch');

        const startRoute = `{{ route('panel.campaign.send.start', ['id' => '__ID__']) }}`.replace('__ID__', String(campaignId));
        const processRoute = `{{ route('panel.campaign.send.process', ['id' => '__ID__']) }}`.replace('__ID__', String(campaignId));
        const progressRoute = `{{ route('panel.campaign.send.progress', ['id' => '__ID__']) }}`.replace('__ID__', String(campaignId));
        const csrfToken = '{{ csrf_token() }}';

        let timer = null;
        let isPaused = false;
        let isStarting = false;
        let currentPage = 1;
        let state = {
            total: 0,
            sent: 0,
            failed: 0,
            processed: 0,
            finished: false,
            running: false,
        };

        function renderProgress(newState) {
            state = newState;
            const total = Number(state.total || 0);
            const processed = Number(state.processed || 0);
            const sent = Number(state.sent || 0);
            const failed = Number(state.failed || 0);
            const percent = total > 0 ? Math.min(100, Math.round((processed / total) * 100)) : 0;

            totalEl.innerText = total;
            sentEl.innerText = sent;
            failedEl.innerText = failed;
            processedEl.innerText = processed;
            progressBar.style.width = `${percent}%`;
            progressBar.innerText = `${percent}%`;

            if (state.finished) {
                progressBar.classList.remove('progress-bar-animated', 'progress-bar-striped');
                showToast(`Envio finalizado. Sucesso: ${sent} | Falhas: ${failed}`, 'success');
                stopAutoProcessing();
                finishDispatch();
            }
        }

        async function getProgress() {
            const res = await fetch(progressRoute, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error('Não foi possível obter progresso.');
            const data = await res.json();
            renderProgress(data);
            return data;
        }

        async function startDispatch() {
            const res = await fetch(startRoute, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
            if (!res.ok) throw new Error('Não foi possível iniciar envio.');
            const data = await res.json();
            renderProgress(data);
            showToast('Disparo iniciado com sucesso.', 'success');
            currentPage = 1;
            isStarting = false;
            startAutoProcessing();
        }

        async function processBatch() {
            const messagesPerBatch = parseInt(messagesPerBatchInput.value, 10) || 5;

            const res = await fetch(processRoute, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    page: currentPage,
                    per_page: messagesPerBatch
                }),
            });
            if (!res.ok) throw new Error('Falha ao processar lote.');
            const data = await res.json();
            renderProgress(data);
            currentPage++;
            return data;
        }

        function startAutoProcessing() {
            stopAutoProcessing();
            updateButtonStates();

            timer = setInterval(async () => {
                if (isPaused) return;

                try {
                    const batchState = await processBatch();
                    if (batchState.finished) {
                        stopAutoProcessing();
                    }
                } catch (e) {
                    stopAutoProcessing();
                    showToast(e.message || 'Erro durante o envio.', 'danger');
                }
            }, 3000); // 3 segundos entre cada envio
        }

        function stopAutoProcessing() {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
            updateButtonStates();
        }

        function pauseDispatch() {
            isPaused = true;
            updateButtonStates();
            showToast('Envio pausado. Clique em Retomar para continuar.', 'info');
        }

        function resumeDispatch() {
            isPaused = false;
            updateButtonStates();
            showToast('Envio retomado.', 'info');
        }

        function finishDispatch() {
            isStarting = false;
            isPaused = false;
            startBtn.disabled = false;
            pauseBtn.disabled = true;
            resumeBtn.disabled = true;
            messagesPerBatchInput.disabled = false;
        }

        function updateButtonStates() {
            const hasActiveDispatch = isStarting || timer !== null || !!state.running;

            startBtn.disabled = hasActiveDispatch;
            pauseBtn.disabled = !hasActiveDispatch || isPaused;
            resumeBtn.disabled = !hasActiveDispatch || !isPaused;
            messagesPerBatchInput.disabled = hasActiveDispatch;
        }

        startBtn.addEventListener('click', async function () {
            isStarting = true;
            isPaused = false;
            updateButtonStates();

            try {
                await startDispatch();
            } catch (e) {
                isStarting = false;
                showToast(e.message || 'Erro ao iniciar.', 'danger');
                finishDispatch();
            }
        });

        pauseBtn.addEventListener('click', function () {
            pauseDispatch();
        });

        resumeBtn.addEventListener('click', function () {
            resumeDispatch();
        });
        updateButtonStates();
        getProgress().catch(() => {});
    })();
</script>
@endpush

