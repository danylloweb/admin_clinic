@extends('layouts.header')
@section('content')
<div class="container py-6">
    <div class="row">
        <div class="col-12 col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <div>
                        <h5 class="mb-0">Novo Paciente</h5>
                        <small class="text-muted">Criar novo paciente</small>
                    </div>
                </div>
                <div class="card-body">
                    <form id="patientForm">
                        <input type="hidden" name="id" id="patientId" value="">

                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="name" id="name"  class="form-control" required>
                        </div>

                        <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome Social</label>
                            <input type="text" name="social_name" id="social_name"  class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="phone" id="phone" class="form-control" required
                                   placeholder="(81)99999-0000" maxlength="15" inputmode="numeric" onkeyup="maskPhone(this)">
                        </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Data de Nascimento</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control" >
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sexo</label>
                                <select name="sex" id="sex" class="form-select">
                                    <option value="F">Feminino</option>
                                    <option value="M">Masculino</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" id="saveBtn" class="btn btn-primary">Criar</button>
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
        // máscara de telefone (ex: (81)98587-9004) aplicada no onkeyup
        window.maskPhone = function(el){
            if(!el) return;
            let v = el.value.replace(/\D/g,'');
            if(v.length > 11) v = v.slice(0,11);
            if(v.length > 7){
                // (99)99999-9999
                el.value = '(' + v.slice(0,2) + ')' + v.slice(2,7) + '-' + v.slice(7);
            } else if(v.length > 2){
                // (99)99999 or (99)9999
                el.value = '(' + v.slice(0,2) + ')' + v.slice(2);
            } else if(v.length > 0){
                el.value = '(' + v;
            } else {
                el.value = '';
            }
        };

    (function(){
        const form = document.getElementById('patientForm');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const alertEl = document.getElementById('formAlert');

        cancelBtn.addEventListener('click', ()=>{
            // voltar para a lista
            window.location.href = "{{ route('panel.patient.index') }}";
        });

        // Atualiza avatar preview quando nome mudar
        const nameInput = document.getElementById('name');
        nameInput.addEventListener('input', ()=>{
            const img = document.getElementById('patientAvatar');
            if (!img) return;
            const name = nameInput.value.trim() || 'Novo Paciente';
            img.src = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name);
        });

        form.addEventListener('submit', async function(e){
            e.preventDefault();
            saveBtn.disabled = true;
            saveBtn.innerText = 'Salvando...';

            const payload = {
                name: document.getElementById('name').value,
                social_name: document.getElementById('social_name').value,
                phone: document.getElementById('phone').value,
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
                const res = await fetch('/patients', {
                    method: 'POST',
                    headers,
                    body: JSON.stringify(payload),
                    credentials: 'same-origin'
                });

                if (res.ok){
                    const data = await res.json();
                    showToast('Paciente criado com sucesso', 'success');

                    if (data.photo){
                        const img = document.getElementById('patientAvatar');
                        if (img) img.src = data.photo;
                    }

                    // se retornou id, redireciona para a tela de show
                    if (data.id){
                        window.location.href = '/patients/' + data.id;
                    } else {
                        // fallback: recarrega lista
                        setTimeout(()=> window.location.href = "{{ route('panel.patient.index') }}", 900);
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
                saveBtn.innerText = 'Criar';
            }
        });

    })();
</script>
@endpush
