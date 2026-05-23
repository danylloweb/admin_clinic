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
                        <input id="photo_before_input" type="file" class="form-control" accept="image/*">
                        <img id="photo_before_preview" class="img-fluid rounded mt-2 border" style="max-height:220px;" src="{{ $attendance->photo_before ?? '' }}" alt="Foto antes">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Foto depois</label>
                        <input id="photo_after_input" type="file" class="form-control" accept="image/*">
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

@push('scripts')
<script>
    const initialSessions = @json($sessions);

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

    function bindPreview(inputId, hiddenId, previewId) {
        document.getElementById(inputId).addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                const data = e.target.result;
                document.getElementById(hiddenId).value = data;
                document.getElementById(previewId).src = data;
            };
            reader.readAsDataURL(file);
        });
    }

    function saveAttendance() {
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

        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrf
            },
            body: JSON.stringify(payload)
        })
            .then((response) => response.json().then((json) => ({ response, json })))
            .then(({ response, json }) => {
                if (!response.ok) {
                    throw new Error(json.message || 'Falha ao salvar atendimento.');
                }

                const id = (json.data && json.data.id) || json.id || Number(config.existingId || 0);
                window.location.href = '/panel-attendances-show/' + id;
            })
            .catch((error) => {
                alert(error.message || 'Falha ao salvar atendimento.');
            });
    }

    document.addEventListener('click', function (event) {
        if (event.target.id === 'add-session') {
            addSessionRow();
        }

        if (event.target.classList.contains('js-remove-session')) {
            event.target.closest('tr').remove();
        }
    });

    document.getElementById('save-attendance').addEventListener('click', saveAttendance);

    if (Array.isArray(initialSessions) && initialSessions.length) {
        initialSessions.forEach((session) => addSessionRow(session));
    } else {
        addSessionRow();
    }

    bindPreview('photo_before_input', 'photo_before_hidden', 'photo_before_preview');
    bindPreview('photo_after_input', 'photo_after_hidden', 'photo_after_preview');
    attachSignaturePad('patient-signature-canvas', 'clear-patient-signature', 'patient_signature_hidden');
    attachSignaturePad('professional-signature-canvas', 'clear-professional-signature', 'professional_signature_hidden');
</script>
@endpush
@endsection

