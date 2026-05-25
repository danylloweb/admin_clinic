@extends('layouts.header')
@section('content')
@php
    $isEdit = !empty($attendance) && !empty($attendance->id);
    $sessions = $attendance->evolution_sessions ?? [];
    $resultOptions = ['Excelente', 'Bom', 'Regular', 'Insatisfatorio'];
    $saveUrl = $isEdit
        ? route('panel.attendances.update', ['id' => $attendance->id])
        : route('panel.attendances.store');
    $saveMethod = $isEdit ? 'PUT' : 'POST';
    $attendanceId = (int) ($attendance->id ?? 0);
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Atendimento</h4>
            <div class="d-flex gap-2">
                @if($isEdit)
                    <a href="{{ route('panel.attendances.print', ['id' => $attendance->id]) }}" class="btn btn-light btn-sm" target="_blank">Imprimir</a>
                    <a href="{{ route('panel.attendances.export-pdf', ['id' => $attendance->id]) }}" class="btn btn-light btn-sm">PDF</a>
                @endif
                <a href="{{ route('panel.schedules.index') }}" class="btn btn-light btn-sm">Fechar</a>
            </div>
        </div>
        <div class="card-body">
            <div id="attendance-config"
                 data-url="{{ $saveUrl }}"
                 data-method="{{ $saveMethod }}"
                 data-existing-id="{{ $attendanceId }}"
                 data-csrf="{{ csrf_token() }}"></div>
            <form id="attendance-form" action="{{ $isEdit ? route('panel.attendances.update', ['id' => $attendance->id]) : route('panel.attendances.store') }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <input type="hidden" id="patient_id" value="{{ (int) $patient->id }}">
                <input type="hidden" id="schedule_id" value="{{ (int)($attendance->schedule_id ?? $schedule->id ?? 0) }}">
                <input type="hidden" id="professional_id" value="{{ (int)($attendance->professional_id ?? $professional_id ?? auth()->id()) }}">
                <input type="hidden" id="evolution_sessions">
                <input type="hidden" id="photo_before_hidden" value="{{ $attendance->photo_before ?? '' }}">
                <input type="hidden" id="photo_after_hidden" value="{{ $attendance->photo_after ?? '' }}">
                <input type="hidden" id="patient_signature_hidden" value="{{ $attendance->patient_signature ?? '' }}">
                <input type="hidden" id="professional_signature_hidden" value="{{ $attendance->professional_signature ?? '' }}">

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Paciente</label>
                        <input class="form-control" readonly value="{{ $patient->social_name ?: $patient->name }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Procedimento</label>
                        <input id="procedure_name" class="form-control" value="{{ $attendance->procedure_name ?? ($schedule->procedure->name ?? '') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Data de inicio</label>
                        <input id="start_date" type="date" class="form-control" value="{{ optional(optional($attendance)->start_date)->format('Y-m-d') ?: ($schedule->date ?? '') }}">
                    </div>
                </div>

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header">Evolucao por Sessao</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="sessions-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Data</th>
                                    <th>Procedimento realizado</th>
                                    <th>Equipamento</th>
                                    <th>Parametros</th>
                                    <th>Produtos</th>
                                    <th>Reacao</th>
                                    <th>Observacoes</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="add-session">Adicionar sessao</button>
                        <small class="text-muted ms-2">Preencha livremente os campos de cada atendimento.</small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Foto antes</label>
                        <input id="photo_before_input" type="file" class="form-control js-photo-input" data-target="before" accept="image/*">
                        <button type="button" class="btn btn-sm btn-info mt-2 js-open-camera" data-target="before">Usar camera</button>
                        <small id="status-before" class="text-muted d-block mt-1"></small>
                        <img id="photo_before_preview" class="img-fluid rounded mt-2 border" style="max-height:220px;" src="{{ $attendance->photo_before ?? '' }}" alt="Foto antes">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto depois</label>
                        <input id="photo_after_input" type="file" class="form-control js-photo-input" data-target="after" accept="image/*">
                        <button type="button" class="btn btn-sm btn-info mt-2 js-open-camera" data-target="after">Usar camera</button>
                        <small id="status-after" class="text-muted d-block mt-1"></small>
                        <img id="photo_after_preview" class="img-fluid rounded mt-2 border" style="max-height:220px;" src="{{ $attendance->photo_after ?? '' }}" alt="Foto depois">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Avaliacao de Resultado</label>
                    <select id="result_evaluation" class="form-select">
                        <option value="">Selecione</option>
                        @foreach($resultOptions as $option)
                            <option value="{{ $option }}" {{ ($attendance->result_evaluation ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Assinatura do Paciente</label>
                        <canvas id="patient-signature-canvas" class="w-100 border rounded" height="180"></canvas>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-patient-signature">Limpar</button>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Assinatura do Profissional</label>
                        <canvas id="professional-signature-canvas" class="w-100 border rounded" height="180"></canvas>
                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="clear-professional-signature">Limpar</button>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" id="save-attendance">Salvar Atendimento</button>
                    @if($isEdit)
                        <a href="{{ route('panel.attendances.show', ['id' => $attendance->id]) }}" class="btn btn-outline-secondary">Visualizar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Capturar foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center" style="background:#000;">
                <video id="cameraVideo" autoplay playsinline style="width:95vw; height:78vh; max-width:100%; object-fit:contain;"></video>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="capturePhotoBtn" class="btn btn-primary">Capturar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const initialSessions = @json($sessions);
        const uploadEndpoint = '{{ route("panel.uploads.store") }}';
        const csrf = document.getElementById('attendance-config').dataset.csrf;
        const saveBtn = document.getElementById('save-attendance');
        const pendingUploads = { count: 0 };

        let activeCameraTarget = null;
        let cameraStream = null;
        const cameraModalEl = document.getElementById('cameraModal');
        const cameraModal = (window.bootstrap && cameraModalEl) ? new bootstrap.Modal(cameraModalEl) : null;

        const photoConfig = {
            before: {
                previewId: 'photo_before_preview',
                hiddenId: 'photo_before_hidden',
                statusId: 'status-before',
            },
            after: {
                previewId: 'photo_after_preview',
                hiddenId: 'photo_after_hidden',
                statusId: 'status-after',
            },
        };

        function notify(message, type) {
            if (typeof showToast === 'function') {
                showToast(message, type || 'info');
            } else {
                alert(message);
            }
        }

        function addSessionRow(session = {}) {
            const tbody = document.querySelector('#sessions-table tbody');
            const index = tbody.querySelectorAll('tr').length + 1;
            const tr = document.createElement('tr');
            tr.innerHTML = '' +
                '<td><input class="form-control form-control-sm js-session-number" type="number" min="1" value="' + (session.session_number || index) + '"></td>' +
                '<td><input class="form-control form-control-sm js-session-date" type="date" value="' + (session.date || '') + '"></td>' +
                '<td><input class="form-control form-control-sm js-session-procedure" value="' + (session.procedure_performed || '') + '"></td>' +
                '<td><input class="form-control form-control-sm js-session-equipment" value="' + (session.equipment_used || '') + '"></td>' +
                '<td><input class="form-control form-control-sm js-session-parameters" value="' + (session.parameters_used || '') + '"></td>' +
                '<td><input class="form-control form-control-sm js-session-products" value="' + (session.products_used || '') + '"></td>' +
                '<td><input class="form-control form-control-sm js-session-reaction" value="' + (session.patient_reaction || '') + '"></td>' +
                '<td><input class="form-control form-control-sm js-session-observations" value="' + (session.observations || '') + '"></td>' +
                '<td><button type="button" class="btn btn-sm btn-outline-danger js-remove-session">X</button></td>';
            tbody.appendChild(tr);
        }

        function collectSessions() {
            return Array.from(document.querySelectorAll('#sessions-table tbody tr')).map((row) => ({
                session_number: row.querySelector('.js-session-number').value || null,
                date: row.querySelector('.js-session-date').value || null,
                procedure_performed: row.querySelector('.js-session-procedure').value || null,
                equipment_used: row.querySelector('.js-session-equipment').value || null,
                parameters_used: row.querySelector('.js-session-parameters').value || null,
                products_used: row.querySelector('.js-session-products').value || null,
                patient_reaction: row.querySelector('.js-session-reaction').value || null,
                observations: row.querySelector('.js-session-observations').value || null
            }));
        }

        function setPhotoStatus(target, text, type) {
            const config = photoConfig[target];
            if (!config) return;
            const statusEl = document.getElementById(config.statusId);
            if (!statusEl) return;
            statusEl.className = 'd-block mt-1 text-' + (type || 'muted');
            statusEl.textContent = text || '';
        }

        function setPhotoPreview(target, src) {
            const config = photoConfig[target];
            if (!config) return;
            const preview = document.getElementById(config.previewId);
            if (!preview) return;
            preview.src = src || '';
        }

        function setPhotoUrl(target, url) {
            const config = photoConfig[target];
            if (!config) return;
            const hidden = document.getElementById(config.hiddenId);
            if (!hidden) return;
            hidden.value = url || '';
        }

        function toggleSaveState() {
            const blocked = pendingUploads.count > 0;
            saveBtn.disabled = blocked;
            saveBtn.innerHTML = blocked
                ? '<i class="fas fa-spinner fa-spin me-1"></i>Enviando fotos...'
                : 'Salvar Atendimento';
        }

        async function uploadPhoto(file, target) {
            if (!file || !photoConfig[target]) return;

            pendingUploads.count += 1;
            toggleSaveState();
            setPhotoStatus(target, 'Enviando...', 'warning');

            try {
                const data = new FormData();
                data.append('file', file);
                data.append('folder', 'aesthetic-procedure-evolutions');
                data.append('prefix', String(Date.now()) + '-' + target);

                const response = await fetch(uploadEndpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: data,
                    credentials: 'same-origin'
                });

                const json = await response.json().catch(() => ({}));
                if (!response.ok || json.error || !json.url) {
                    throw new Error(json.message || 'Falha no upload da imagem.');
                }

                setPhotoUrl(target, json.url);
                setPhotoPreview(target, json.url);
                setPhotoStatus(target, 'Upload concluido', 'success');
            } catch (error) {
                setPhotoStatus(target, error.message || 'Erro no upload', 'danger');
                notify(error.message || 'Erro no upload da imagem.', 'danger');
            } finally {
                pendingUploads.count -= 1;
                toggleSaveState();
            }
        }

        function normalizeCameraModalLayering() {
            if (!cameraModalEl) return;
            if (cameraModalEl.parentElement !== document.body) {
                document.body.appendChild(cameraModalEl);
            }

            cameraModalEl.style.zIndex = '1060';
            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                backdrop.style.zIndex = '1050';
            });
        }

        function cleanupModalArtifacts() {
            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }

        async function openCamera(target) {
            activeCameraTarget = target;

            if (!cameraModal) {
                notify('Nao foi possivel inicializar o modal da camera.', 'danger');
                return;
            }

            cleanupModalArtifacts();
            normalizeCameraModalLayering();
            cameraModal.show();
            normalizeCameraModalLayering();

            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: { ideal: 'environment' } },
                    audio: false
                });

                const video = document.getElementById('cameraVideo');
                video.srcObject = cameraStream;
                video.play();
            } catch (error) {
                if (cameraModal) cameraModal.hide();
                notify('Nao foi possivel abrir a camera.', 'danger');
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach((track) => track.stop());
                cameraStream = null;
            }
        }

        function attachSignaturePad(canvasId, clearButtonId, hiddenFieldId) {
            const canvas = document.getElementById(canvasId);
            const ctx = canvas.getContext('2d');
            let drawing = false;

            function resizeCanvas() {
                const rect = canvas.getBoundingClientRect();
                canvas.width = rect.width;
                canvas.height = 180;
                const saved = document.getElementById(hiddenFieldId).value;
                if (saved) {
                    const img = new Image();
                    img.onload = () => ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    img.src = saved;
                }
            }

            function getPos(evt) {
                const rect = canvas.getBoundingClientRect();
                const source = evt.touches ? evt.touches[0] : evt;
                return { x: source.clientX - rect.left, y: source.clientY - rect.top };
            }

            function start(evt) {
                drawing = true;
                const p = getPos(evt);
                ctx.beginPath();
                ctx.moveTo(p.x, p.y);
            }

            function move(evt) {
                if (!drawing) return;
                evt.preventDefault();
                const p = getPos(evt);
                ctx.lineTo(p.x, p.y);
                ctx.strokeStyle = '#111';
                ctx.lineWidth = 2;
                ctx.stroke();
                document.getElementById(hiddenFieldId).value = canvas.toDataURL('image/png');
            }

            function stop() {
                drawing = false;
                document.getElementById(hiddenFieldId).value = canvas.toDataURL('image/png');
            }

            canvas.addEventListener('mousedown', start);
            canvas.addEventListener('mousemove', move);
            window.addEventListener('mouseup', stop);
            canvas.addEventListener('touchstart', start, { passive: true });
            canvas.addEventListener('touchmove', move, { passive: false });
            canvas.addEventListener('touchend', stop);

            document.getElementById(clearButtonId).addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                document.getElementById(hiddenFieldId).value = '';
            });

            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);
        }

        async function saveAttendance() {
            if (pendingUploads.count > 0) {
                notify('Aguarde o termino do upload das fotos antes de salvar.', 'warning');
                return;
            }

            const sessions = collectSessions();
            document.getElementById('evolution_sessions').value = JSON.stringify(sessions);

            const payload = {
                schedule_id: document.getElementById('schedule_id').value || null,
                patient_id: document.getElementById('patient_id').value,
                professional_id: document.getElementById('professional_id').value,
                procedure_name: document.getElementById('procedure_name').value,
                start_date: document.getElementById('start_date').value || null,
                evolution_sessions: sessions,
                photo_before: document.getElementById('photo_before_hidden').value || null,
                photo_after: document.getElementById('photo_after_hidden').value || null,
                result_evaluation: document.getElementById('result_evaluation').value || null,
                patient_signature: document.getElementById('patient_signature_hidden').value || null,
                professional_signature: document.getElementById('professional_signature_hidden').value || null,
                signed_at: new Date().toISOString().slice(0, 19).replace('T', ' ')
            };

            const config = document.getElementById('attendance-config').dataset;
            const url = config.url;
            const method = config.method;

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Salvando...';

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const json = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(json.message || 'Falha ao salvar atendimento.');
                }

                const id = (json.data && json.data.id) || json.id || Number(config.existingId || 0);
                window.location.href = '/panel-attendances-show/' + id;
            } catch (error) {
                notify(error.message || 'Falha ao salvar atendimento.', 'danger');
            } finally {
                toggleSaveState();
            }
        }

        document.addEventListener('click', function (event) {
            if (event.target.id === 'add-session') {
                addSessionRow();
            }

            if (event.target.classList.contains('js-remove-session')) {
                event.target.closest('tr').remove();
            }
        });

        document.querySelectorAll('.js-photo-input').forEach((input) => {
            input.addEventListener('change', function () {
                uploadPhoto(input.files && input.files[0], input.dataset.target);
            });
        });

        document.querySelectorAll('.js-open-camera').forEach((btn) => {
            btn.addEventListener('click', function () {
                openCamera(btn.dataset.target);
            });
        });

        document.getElementById('capturePhotoBtn').addEventListener('click', function () {
            const video = document.getElementById('cameraVideo');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            canvas.toBlob(function (blob) {
                if (blob && activeCameraTarget) {
                    uploadPhoto(blob, activeCameraTarget);
                }
                stopCamera();
                if (cameraModal) cameraModal.hide();
            }, 'image/jpeg', 0.9);
        });

        if (cameraModalEl) {
            cameraModalEl.addEventListener('shown.bs.modal', normalizeCameraModalLayering);
            cameraModalEl.addEventListener('hidden.bs.modal', function () {
                stopCamera();
                activeCameraTarget = null;
                cleanupModalArtifacts();
            });
        }

        document.getElementById('save-attendance').addEventListener('click', saveAttendance);

        if (Array.isArray(initialSessions) && initialSessions.length) {
            initialSessions.forEach((session) => addSessionRow(session));
        } else {
            addSessionRow();
        }

        attachSignaturePad('patient-signature-canvas', 'clear-patient-signature', 'patient_signature_hidden');
        attachSignaturePad('professional-signature-canvas', 'clear-professional-signature', 'professional_signature_hidden');
        toggleSaveState();
    });
</script>
@endpush
@endsection

