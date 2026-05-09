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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome</label>
                                <input type="text" name="name" id="name" value="{{ $patient->name }}" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Social</label>
                                <input type="text" name="social_name" id="social_name" value="{{ $patient->social_name }}" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="phone" id="phone" value="{{ $patient->phone }}" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Chat ID</label>
                                <input type="text" name="chat_id" id="chat_id" value="{{ $patient->chat_id }}" class="form-control" readonly>
                            </div>
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
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('panel.facial-evaluations.index', ['patientId' => $patient->id]) }}" class="btn btn-outline-info">
                                    Ver avaliacoes faciais
                                </a>
                                @if($medicalRecordStatus != "nao_gerado" && !empty($medicalRecordPanel['submitted_at']))
                                    <a href="{{ route('panel.patient.medical-record.show', ['patientId' => $patient->id]) }}" class="btn btn-outline-success">
                                        Ver prontuario
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-primary" id="generateMedicalRecordLinkBtn">
                                    {{ $medicalRecordStatus ? 'Gerar novo link' : 'Gerar link' }}
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="copyMedicalRecordLinkBtn" {{ empty($medicalRecordLink) ? 'disabled' : '' }}>
                                    Copiar link
                                </button>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Link do prontuário</label>
                            <input type="text" id="medicalRecordLinkInput" class="form-control" readonly value="{{ $medicalRecordLink }}" placeholder="Gere o link para compartilhar com o paciente">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        const form = document.getElementById('patientForm');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const patientId = document.getElementById('patientId').value;
        const medicalRecordLinkInput = document.getElementById('medicalRecordLinkInput');
        const medicalRecordStatusBadge = document.getElementById('medical-record-status-badge');
        const medicalRecordSubmittedAt = document.getElementById('medical-record-submitted-at');
        const generateMedicalRecordLinkBtn = document.getElementById('generateMedicalRecordLinkBtn');
        const copyMedicalRecordLinkBtn = document.getElementById('copyMedicalRecordLinkBtn');
        const shareMedicalRecordWhatsappBtn = document.getElementById('shareMedicalRecordWhatsappBtn');
        const medicalRecordLinkRoute = '{{ route('panel.patient.medical-record.link', ['patientId' => '__PATIENT__']) }}';
        const patientPhone = @json($patient->phone ?? '');



        cancelBtn.addEventListener('click', ()=>{
            // reload page to discard changes
            window.location.href="{{ route('panel.patient.index') }}";
        });

        function normalizePhoneForWhatsapp(value) {
            const digits = String(value || '').replace(/\D/g, '');
            if (!digits) {
                return '';
            }

            return digits.startsWith('55') ? digits : `55${digits}`;
        }

        function updateMedicalRecordStatus(status, submittedAt = '') {
            if (!medicalRecordStatusBadge) {
                return;
            }

            const map = {
                pendente: {text: 'Pendente', classes: ['bg-warning', 'text-dark']},
                preenchido: {text: 'Preenchido', classes: ['bg-success', 'text-white']},
                nao_gerado: {text: 'Não gerado', classes: ['bg-black', 'text-white']},
            };
            const config = map[status] || map.nao_gerado;
            medicalRecordStatusBadge.className = 'badge';
            config.classes.forEach((className) => medicalRecordStatusBadge.classList.add(className));
            medicalRecordStatusBadge.innerText = config.text;

            if (medicalRecordSubmittedAt) {
                medicalRecordSubmittedAt.innerText = submittedAt || '';
            }
        }

        async function issueMedicalRecordLink() {
            const originalText = generateMedicalRecordLinkBtn.innerText;
            generateMedicalRecordLinkBtn.disabled = true;
            generateMedicalRecordLinkBtn.innerText = 'Gerando...';

            try {
                const response = await fetch(medicalRecordLinkRoute.replace('__PATIENT__', patientId), {
                    method: 'GET',
                    headers: {Accept: 'application/json'},
                    credentials: 'same-origin'
                });

                if (response.ok) {
                    showToast('Link do prontuário pronto para envio.', 'success');
                    const payload = await response.json();
                    if (medicalRecordLinkInput) {
                        medicalRecordLinkInput.value = payload.link || '';
                    }

                    updateMedicalRecordStatus(payload.status || 'pendente');
                    copyMedicalRecordLinkBtn.disabled = !(payload.link);
                    shareMedicalRecordWhatsappBtn.disabled = !(payload.link);
                    generateMedicalRecordLinkBtn.innerText = 'Gerar novo link';
                }else{
                    showToast('Não foi possível gerar o link do prontuário.', 'danger');
                    return;
                }



            } catch (error) {
                // showToast(error.message || 'Erro ao gerar link do prontuário.', 'danger');
            } finally {
                generateMedicalRecordLinkBtn.disabled = false;
                if (generateMedicalRecordLinkBtn.innerText !== 'Gerar novo link') {
                    generateMedicalRecordLinkBtn.innerText = originalText;
                }
            }
        }

        async function copyMedicalRecordLink() {
            const link = medicalRecordLinkInput?.value || '';
            if (!link) {
                showToast('Gere um link antes de copiar.', 'warning');
                return;
            }

            try {
                await navigator.clipboard.writeText(link);
                showToast('Link copiado com sucesso.', 'success');
            } catch (error) {
                medicalRecordLinkInput.focus();
                medicalRecordLinkInput.select();
                showToast('Não foi possível copiar automaticamente. O link foi selecionado para cópia manual.', 'warning');
            }
        }

        function shareMedicalRecordWhatsapp() {
            const link = medicalRecordLinkInput?.value || '';
            if (!link) {
                showToast('Gere um link antes de compartilhar.', 'warning');
                return;
            }

            const phone = normalizePhoneForWhatsapp(patientPhone);
            const patientName = document.getElementById('social_name').value || document.getElementById('name').value || 'Paciente';
            const message = `Olá, ${patientName}! 💚\n\nPara continuarmos seu atendimento na Renovar, preencha seu prontuário neste link:\n${link}\n\nQuando finalizar, nossa equipe será avisada.`;
            const whatsappUrl = phone
                ? `https://wa.me/${phone}?text=${encodeURIComponent(message)}`
                : `https://wa.me/?text=${encodeURIComponent(message)}`;

            window.open(whatsappUrl, '_blank');
        }

        generateMedicalRecordLinkBtn?.addEventListener('click', issueMedicalRecordLink);
        copyMedicalRecordLinkBtn?.addEventListener('click', copyMedicalRecordLink);
        shareMedicalRecordWhatsappBtn?.addEventListener('click', shareMedicalRecordWhatsapp);

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            saveBtn.disabled = true;
            saveBtn.innerText = 'Salvando...';

            const payload = {
                name: document.getElementById('name').value,
                social_name: document.getElementById('social_name').value,
                phone: document.getElementById('phone').value,
                chat_id: document.getElementById('chat_id').value,
                birth_date: document.getElementById('birth_date').value || null,
                sex: document.getElementById('sex').value || null,
            };

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
            if (csrfMeta){ headers['X-CSRF-TOKEN'] = csrfMeta.getAttribute('content'); }

            try{
                const res = await fetch('/patients/' + patientId, {
                    method: 'PUT',
                    headers,
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });

                if (res.ok){
                    const data = await res.json();
                    showToast('Paciente atualizado com sucesso', 'success');

                    if (data.photo){
                        const img = document.getElementById('patientAvatar');
                        if (img) img.src = data.photo;
                    }
                } else if (res.status === 422){
                    const err = await res.json();
                    const messages = [];
                    if (err.errors){
                        for (const k in err.errors){ messages.push(err.errors[k].join(', ')); }
                    } else if (err.message){ messages.push(err.message); }
                    showToast(messages.join('\n'), 'danger');
                } else {
                    const txt = await res.text();
                    showToast('Erro ao salvar: ' + (txt || res.statusText), 'danger');
                }
            }catch(err){
                showToast('Erro de rede: ' + err.message, 'danger');
            }finally{
                saveBtn.disabled = false;
                saveBtn.innerText = 'Salvar';
            }
        });

    })();
</script>
@endpush
