@extends('layouts.header')
@section('content')
    <div class="card mb-3">
    <div class="row">
        <div class="col-lg-12 col-md-12 mx-auto">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <img src="{{ $photo ?? 'https://ui-avatars.com/api/?name='.urlencode($patient->name) }}" alt="avatar" id="patientAvatar" class="rounded-circle me-3" style="width:64px;height:64px;object-fit:cover;">
                    <div>
                        <h5 class="mb-0">{{ $patient->name }}</h5>
                        <small class="text-muted">Paciente #{{ $patient->id }}</small>
                    </div>
                </div>
                <div class="card-body">
                    <form id="patientForm">
                        <input type="hidden" name="id" id="patientId" value="{{ $patient->id }}">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" name="name" id="name" value="{{ $patient->name }}" class="form-control" required>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Nome Social</label>
                                <input type="text" name="social_name" id="social_name" value="{{ $patient->social_name }}" class="form-control">
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="phone" id="phone" value="{{ $patient->phone }}" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Chat ID</label>
                                <input type="text" name="chat_id" id="chat_id" value="{{ $patient->chat_id }}" class="form-control" readonly>
                            </div>

                        </div>
                        <div class="row">


                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control" value="{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('Y-m-d') : '' }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sexo</label>
                                <select name="sex" id="sex" class="form-select">
                                    <option value="M" {{ $patient->sex==='M' ? 'selected' : '' }}>Masculino</option>
                                    <option value="F" {{ $patient->sex==='F' ? 'selected' : '' }}>Feminino</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" id="saveBtn" class="btn btn-primary">Salvar</button>
                            <button type="button" id="cancelBtn" class="btn btn-secondary ms-2">Cancelar</button>
                        </div>

                        <div id="formAlert" class="alert d-none" role="alert"></div>
                    </form>

                    <hr class="my-4">

                    @php
                        $medicalRecordSubmittedAt = $medicalRecordPanel['submitted_at'] ?? 'nao_gerado';
                        $medicalRecordStatus = $medicalRecordPanel['status'];
                        $medicalRecordLink = $medicalRecordPanel['link'] ?? '';
                    @endphp

                    <div class="rounded-4 p-4" style="background:linear-gradient(135deg, rgba(15,118,110,.08), rgba(212,168,92,.12)); border:1px solid rgba(15,118,110,.12);">
                        <div class="d-flex flex-column flex-lg-row gap-3 justify-content-between align-items-lg-center">
                            <div>
                                <h5 class="mb-1">Prontuário digital</h5>
                                <p class="mb-2 text-muted">Gere um link único para o paciente preencher o prontuário pelo celular. O token permanece válido até o envio do formulário.</p>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <span class="badge {{ $medicalRecordStatus != "nao_gerado" ? 'bg-success' : 'bg-black' }}" id="medical-record-status-badge">
                                        {{ $medicalRecordStatus != "nao_gerado" ? 'Preenchido' : 'Não gerado' }}
                                    </span>
                                    @if($medicalRecordStatus != "nao_gerado" && !empty($medicalRecordPanel['submitted_at']))
                                        <small class="text-white" id="medical-record-submitted-at">Preenchido em {{ \Carbon\Carbon::parse($medicalRecordPanel['submitted_at'])->format('d/m/Y H:i:s') }}</small>
                                    @else
                                        <small class="text-white" id="medical-record-submitted-at"></small>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2 w-100 w-lg-auto">
                                <div class="d-flex flex-wrap gap-2">
                                    @if($medicalRecordStatus != "nao_gerado" && !empty($medicalRecordPanel['submitted_at']))
                                        <a href="{{ route('panel.patient.medical-record.show', ['patientId' => $patient->id]) }}" target="_blank" class="btn btn-outline-success">
                                            Ver prontuario
                                        </a>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-outline-primary" id="generateMedicalRecordLinkBtn">
                                        {{ $medicalRecordStatus ? 'Gerar novo link' : 'Gerar link' }}
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="copyMedicalRecordLinkBtn" {{ empty($medicalRecordLink) ? 'disabled' : '' }}>
                                        Copiar link
                                    </button>
                                    <button type="button" class="btn btn-outline-success" id="shareMedicalRecordWhatsappBtn" {{ empty($medicalRecordLink) ? 'disabled' : '' }}>
                                        Enviar WhatsApp
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Link do prontuário</label>
                            <input type="text" id="medicalRecordLinkInput" class="form-control" readonly value="{{ $medicalRecordLink }}" placeholder="Gere o link para compartilhar com o paciente">
                        </div>
                    </div>

                    {{-- Tabs: Avaliações e Atendimentos --}}
                    <div class="mt-4">
                        <ul class="nav nav-tabs" id="patientTabsNav" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab-attendances-btn" data-bs-toggle="tab" data-bs-target="#tab-attendances" type="button" role="tab">
                                    <i class="fas fa-notes-medical me-1"></i>Atendimentos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-facial-btn" data-bs-toggle="tab" data-bs-target="#tab-facial" type="button" role="tab">
                                    <i class="fas fa-smile me-1"></i>Avaliações Faciais
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab-body-btn" data-bs-toggle="tab" data-bs-target="#tab-body" type="button" role="tab">
                                    <i class="fas fa-running me-1"></i>Avaliações Corporais
                                </button>
                            </li>

                        </ul>

                        <div class="tab-content border border-top-0 rounded-bottom p-3" id="patientTabsContent">

                            {{-- Tab: Atendimentos --}}
                            <div class="tab-pane fade show active" id="tab-attendances" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Atendimentos</h6>
                                </div>
                                <div class="table-responsive">
                                    <table id="dt-attendances" class="table table-bordered table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Agendamento</th>
                                                <th>Procedimento</th>
                                                <th>Início</th>
                                                <th>Resultado</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            {{-- Tab: Avaliações Faciais --}}
                            <div class="tab-pane fade" id="tab-facial" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Avaliações Faciais</h6>
                                    <a href="{{ route('panel.facial-evaluations.create', ['patientId' => $patient->id]) }}" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-1"></i>Nova Ficha
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table id="dt-facial" class="table table-bordered table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Data</th>
                                                <th>Objetivo</th>
                                                <th>Status</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                            {{-- Tab: Avaliações Corporais --}}
                            <div class="tab-pane fade" id="tab-body" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Avaliações Corporais</h6>
                                    <a href="{{ route('panel.body-evaluations.create', ['patientId' => $patient->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Nova Avaliação
                                    </a>
                                </div>
                                <div class="table-responsive">
                                    <table id="dt-body" class="table table-bordered table-striped align-middle" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Objetivo</th>
                                                <th>IMC</th>
                                                <th>Peso</th>
                                                <th>Gordura %</th>
                                                <th>Ações</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    {{-- /Tabs --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        const patientId = document.getElementById('patientId').value;

        // ─── Patient form ────────────────────────────────────────────────────────
        const form       = document.getElementById('patientForm');
        const saveBtn    = document.getElementById('saveBtn');
        const cancelBtn  = document.getElementById('cancelBtn');
        const patientPhone = @json($patient->phone ?? '');

        cancelBtn.addEventListener('click', () => {
            window.location.href = "{{ route('panel.patient.index') }}";
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            saveBtn.disabled = true;
            saveBtn.innerText = 'Salvando...';

            const payload = {
                name:       document.getElementById('name').value,
                social_name:document.getElementById('social_name').value,
                phone:      document.getElementById('phone').value,
                chat_id:    document.getElementById('chat_id').value,
                birth_date: document.getElementById('birth_date').value || null,
                sex:        document.getElementById('sex').value || null,
            };

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const headers = { 'Accept': 'application/json', 'Content-Type': 'application/json' };
            if (csrfMeta) headers['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content');

            try {
                const res = await fetch('/patients/' + patientId, {
                    method: 'PUT', headers, body: JSON.stringify(payload), credentials: 'same-origin'
                });
                if (res.ok) {
                    const data = await res.json();
                    showToast('Paciente atualizado com sucesso', 'success');
                    if (data.photo) {
                        const img = document.getElementById('patientAvatar');
                        if (img) img.src = data.photo;
                    }
                } else if (res.status === 422) {
                    const err = await res.json();
                    const messages = [];
                    if (err.errors) { for (const k in err.errors) messages.push(err.errors[k].join(', ')); }
                    else if (err.message) messages.push(err.message);
                    showToast(messages.join('\n'), 'danger');
                } else {
                    showToast('Erro ao salvar: ' + ((await res.text()) || res.statusText), 'danger');
                }
            } catch (err) {
                showToast('Erro de rede: ' + err.message, 'danger');
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerText = 'Salvar';
            }
        });

        // ─── Prontuário ──────────────────────────────────────────────────────────
        const medicalRecordLinkInput      = document.getElementById('medicalRecordLinkInput');
        const medicalRecordStatusBadge    = document.getElementById('medical-record-status-badge');
        const medicalRecordSubmittedAtEl  = document.getElementById('medical-record-submitted-at');
        const generateMedicalRecordLinkBtn= document.getElementById('generateMedicalRecordLinkBtn');
        const copyMedicalRecordLinkBtn    = document.getElementById('copyMedicalRecordLinkBtn');
        const shareMedicalRecordWhatsappBtn = document.getElementById('shareMedicalRecordWhatsappBtn');
        const medicalRecordLinkRoute      = '{{ route('panel.patient.medical-record.link', ['patientId' => '__PATIENT__']) }}';

        function normalizePhoneForWhatsapp(value) {
            const digits = String(value || '').replace(/\D/g, '');
            return digits ? (digits.startsWith('55') ? digits : `55${digits}`) : '';
        }

        function updateMedicalRecordStatus(status, submittedAt = '') {
            if (!medicalRecordStatusBadge) return;
            const map = {
                pendente:   { text: 'Pendente',    classes: ['bg-warning', 'text-dark'] },
                preenchido: { text: 'Preenchido',  classes: ['bg-success', 'text-white'] },
                nao_gerado: { text: 'Não gerado',  classes: ['bg-black',   'text-white'] },
            };
            const config = map[status] || map.nao_gerado;
            medicalRecordStatusBadge.className = 'badge';
            config.classes.forEach(c => medicalRecordStatusBadge.classList.add(c));
            medicalRecordStatusBadge.innerText = config.text;
            if (medicalRecordSubmittedAtEl) medicalRecordSubmittedAtEl.innerText = submittedAt || '';
        }

        async function issueMedicalRecordLink() {
            const originalText = generateMedicalRecordLinkBtn.innerText;
            generateMedicalRecordLinkBtn.disabled = true;
            generateMedicalRecordLinkBtn.innerText = 'Gerando...';
            try {
                const response = await fetch(medicalRecordLinkRoute.replace('__PATIENT__', patientId), {
                    method: 'GET', headers: { Accept: 'application/json' }, credentials: 'same-origin'
                });
                if (response.ok) {
                    const payload = await response.json();
                    if (medicalRecordLinkInput) medicalRecordLinkInput.value = payload.link || '';
                    updateMedicalRecordStatus(payload.status || 'pendente');
                    copyMedicalRecordLinkBtn.disabled = !payload.link;
                    if (shareMedicalRecordWhatsappBtn) shareMedicalRecordWhatsappBtn.disabled = !payload.link;
                    generateMedicalRecordLinkBtn.innerText = 'Gerar novo link';
                    showToast('Link do prontuário pronto para envio.', 'success');
                } else {
                    showToast('Não foi possível gerar o link do prontuário.', 'danger');
                }
            } catch (e) {
                // silent
            } finally {
                generateMedicalRecordLinkBtn.disabled = false;
                if (generateMedicalRecordLinkBtn.innerText !== 'Gerar novo link')
                    generateMedicalRecordLinkBtn.innerText = originalText;
            }
        }

        async function copyMedicalRecordLink() {
            const link = medicalRecordLinkInput?.value || '';
            if (!link) { showToast('Gere um link antes de copiar.', 'warning'); return; }
            try {
                await navigator.clipboard.writeText(link);
                showToast('Link copiado com sucesso.', 'success');
            } catch {
                medicalRecordLinkInput.focus(); medicalRecordLinkInput.select();
                showToast('Não foi possível copiar automaticamente. O link foi selecionado para cópia manual.', 'warning');
            }
        }

        function shareMedicalRecordWhatsapp() {
            const link = medicalRecordLinkInput?.value || '';
            if (!link) { showToast('Gere um link antes de compartilhar.', 'warning'); return; }
            const phone       = normalizePhoneForWhatsapp(patientPhone);
            const patientName = document.getElementById('social_name').value || document.getElementById('name').value || 'Paciente';
            const message     = `Olá, ${patientName}! 💚\n\nPara continuarmos seu atendimento na Renovar, preencha seu prontuário neste link:\n${link}\n\nQuando finalizar, nossa equipe será avisada.`;
            const url         = phone ? `https://wa.me/${phone}?text=${encodeURIComponent(message)}` : `https://wa.me/?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        }

        generateMedicalRecordLinkBtn?.addEventListener('click', issueMedicalRecordLink);
        copyMedicalRecordLinkBtn?.addEventListener('click', copyMedicalRecordLink);
        shareMedicalRecordWhatsappBtn?.addEventListener('click', shareMedicalRecordWhatsapp);

        // ─── Tabs / DataTables (lazy init) ───────────────────────────────────────
        const tablesInitialized = { attendances: false, facial: false, body: false };

        function initAttendancesTable() {
            if (tablesInitialized.attendances) return;
            tablesInitialized.attendances = true;

            $('#dt-attendances').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: function (data, callback) {
                    const page     = Math.floor(data.start / data.length) + 1;
                    const limit    = data.length === -1 ? 15 : data.length;
                    const search   = data.search.value;
                    const colMap   = { 0: 'id', 1: 'schedule_id', 3: 'start_date' };
                    const orderBy  = colMap[data.order?.[0]?.column] || 'id';
                    const sortedBy = data.order?.[0]?.dir || 'desc';
                    $.ajax({
                        url: '{{ url('/panel-attendances') }}',
                        method: 'GET',
                        data: { patient_id: patientId, limit, page, search, orderBy, sortedBy },
                        success: r  => callback({ recordsTotal: r.meta?.pagination?.total || 0, recordsFiltered: r.meta?.pagination?.total || 0, data: r.data || [] }),
                        error:   () => callback({ recordsTotal: 0, recordsFiltered: 0, data: [] })
                    });
                },
                columns: [
                    { data: 'id',               render: v => `<strong>#${v}</strong>` },
                    { data: 'schedule_id',       render: v => v ? `#${v}` : '-' },
                    { data: 'procedure_name',    render: v => v || '-' },
                    { data: 'start_date',        render: v => v || '-' },
                    { data: 'result_evaluation', render: v => v || '-' },
                    {
                        data: null, orderable: false, searchable: false,
                        render: (_, __, row) => `
                            <div class="btn-group btn-group-sm">
                                <a href="{{ url('/panel-attendances-edit') }}/${row.id}" class="btn btn-primary">Abrir</a>
                                <a href="{{ url('/panel-attendances-show') }}/${row.id}" class="btn btn-outline-secondary">Ver</a>
                            </div>`
                    }
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
            });
        }

        function initFacialTable() {
            if (tablesInitialized.facial) return;
            tablesInitialized.facial = true;

            $('#dt-facial').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: function (data, callback) {
                    const page     = Math.floor(data.start / data.length) + 1;
                    const limit    = data.length === -1 ? 15 : data.length;
                    const search   = data.search.value;
                    const colMap   = { 0: 'id', 1: 'created_at', 2: 'skin_type' };
                    const orderBy  = colMap[data.order?.[0]?.column] || 'id';
                    const sortedBy = data.order?.[0]?.dir || 'desc';
                    $.ajax({
                        url: '{{ url('/panel-facial-evaluations') }}',
                        method: 'GET',
                        data: { patient_id: patientId, limit, page, search, orderBy, sortedBy },
                        success: r  => callback({ recordsTotal: r.meta?.pagination?.total || 0, recordsFiltered: r.meta?.pagination?.total || 0, data: r.data || [] }),
                        error:   () => callback({ recordsTotal: 0, recordsFiltered: 0, data: [] })
                    });
                },
                columns: [
                    { data: 'id', render: v => `<strong>#${v}</strong>` },
                    { data: 'created_at', render: v => v ? new Date(v).toLocaleString('pt-BR') : '-' },
                    { data: 'patient_objective', render: v => v || '<span class="text-muted">-</span>' },
                    {
                        data: null, orderable: false, searchable: false,
                        render: (_, __, row) => (row.signed_at || row.consent_accepted)
                            ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Assinado</span>'
                            : '<span class="badge bg-warning"><i class="fas fa-hourglass-half me-1"></i>Pendente</span>'
                    },
                    {
                        data: null, orderable: false, searchable: false,
                        render: (_, __, row) => `
                            <div class="btn-group btn-group-sm">
                                <a href="{{ url('/panel-facial-evaluations-edit') }}/${row.id}" class="btn btn-warning">Editar</a>
                            </div>`
                    }
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
            });
        }

        function initBodyTable() {
            if (tablesInitialized.body) return;
            tablesInitialized.body = true;

            $('#dt-body').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, 'desc']],
                ajax: function (data, callback) {
                    const page     = Math.floor(data.start / data.length) + 1;
                    const limit    = data.length === -1 ? 15 : data.length;
                    const search   = data.search.value;
                    const colMap   = { 0: 'id', 3: 'weight', 4: 'fat_percentage' };
                    const orderBy  = colMap[data.order?.[0]?.column] || 'id';
                    const sortedBy = data.order?.[0]?.dir || 'desc';
                    $.ajax({
                        url: '{{ url('/panel-body-evaluations') }}',
                        method: 'GET',
                        data: { patient_id: patientId, limit, page, search, orderBy, sortedBy },
                        success: r  => callback({ recordsTotal: r.meta?.pagination?.total || 0, recordsFiltered: r.meta?.pagination?.total || 0, data: r.data || [] }),
                        error:   () => callback({ recordsTotal: 0, recordsFiltered: 0, data: [] })
                    });
                },
                columns: [
                    { data: 'id', render: v => `<strong>#${v}</strong>` },
                    { data: 'treatment_plan' },
                    {
                        data: null, orderable: false, searchable: false,
                        render: (_, __, row) => {
                            const w = parseFloat(row.weight || 0), h = parseFloat(row.height || 0);
                            if (!w || !h) return '<span class="text-muted">-</span>';
                            return `<span class="badge bg-primary">${(w / Math.pow(h / 100, 2)).toFixed(1)}</span>`;
                        }
                    },
                    { data: 'weight',         render: v => v ? `${v} kg` : '<span class="text-muted">-</span>' },
                    { data: 'fat_percentage',  render: v => v ? `${v}%`   : '<span class="text-muted">-</span>' },
                    {
                        data: null, orderable: false, searchable: false,
                        render: (_, __, row) => `
                            <div class="btn-group btn-group-sm">
                                <a href="{{ url('/panel-body-evaluations-edit') }}/${row.id}" class="btn btn-primary">Abrir</a>
                            </div>`
                    }
                ],
                language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
            });
        }

        // Init default active tab (Atendimentos) immediately on DOM ready
        $(function () {
            initAttendancesTable();
        });

        // Lazy init remaining tabs on first show
        document.getElementById('tab-facial-btn').addEventListener('shown.bs.tab', function () {
            initFacialTable();
            $('#dt-facial').DataTable().columns.adjust().draw(false);
        });
        document.getElementById('tab-body-btn').addEventListener('shown.bs.tab', function () {
            initBodyTable();
            $('#dt-body').DataTable().columns.adjust().draw(false);
        });
        document.getElementById('tab-attendances-btn').addEventListener('shown.bs.tab', function () {
            $('#dt-attendances').DataTable().columns.adjust().draw(false);
        });


    })();
</script>
@endpush
