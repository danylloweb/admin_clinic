@extends('layouts.header')
@section('content')
<div class="container py-6">
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
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

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" id="name" value="{{ $patient->name }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nome Social</label>
                            <input type="text" name="social_name" id="social_name" value="{{ $patient->social_name }}" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="phone" id="phone" value="{{ $patient->phone }}" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Chat ID</label>
                            <input type="text" name="chat_id" id="chat_id" value="{{ $patient->chat_id }}" class="form-control" readonly>
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
        const alertEl = document.getElementById('formAlert');
        const patientId = document.getElementById('patientId').value;



        cancelBtn.addEventListener('click', ()=>{
            // reload page to discard changes
            window.location.href="{{ route('panel.patient.index') }}";
        });

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
