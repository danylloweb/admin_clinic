@extends('layouts.header')
@section('content')
    <div class="card mb-3">
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Editar Colaborador</h5>
                        <span id="user-name-badge" class="badge fs-7 bg-primary"></span>
                    </div>
                    <div class="card-body" id="edit-user-body">
                        <div class="text-center py-5">
                            <div class="spinner-border" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const USER_ID = {{ (int) $userId }};
    let currentUser = null;

    async function apiGet(url) {
        const res = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
        if (!res.ok) throw new Error('Erro na requisição');
        return res.json();
    }

    async function apiPut(url, data) {
        const res = await fetch(url, {
            method: 'PUT',
            credentials: 'same-origin',
            headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            const msgs = Object.values(err.errors || {}).flat().join('\n');
            throw new Error(msgs || err.message || 'Erro ao salvar');
        }
        return res.json();
    }

    function maskPhone(value) {
        let v = String(value || '').replace(/\D/g, '').slice(0, 11);
        if (v.length > 7) return '(' + v.slice(0,2) + ')' + v.slice(2,7) + '-' + v.slice(7);
        if (v.length > 2) return '(' + v.slice(0,2) + ')' + v.slice(2);
        if (v.length > 0) return '(' + v;
        return '';
    }

    function maskCpf(value) {
        let v = String(value || '').replace(/\D/g, '').slice(0, 11);
        if (v.length > 9) return v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6,9) + '-' + v.slice(9);
        if (v.length > 6) return v.slice(0,3) + '.' + v.slice(3,6) + '.' + v.slice(6);
        if (v.length > 3) return v.slice(0,3) + '.' + v.slice(3);
        return v;
    }

    function renderForm(user) {
        currentUser = user;
        document.getElementById('user-name-badge').innerText = user.name || '';

        const imgSrc = user.img || `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name||'U')}&background=6c757d&color=fff&size=100`;
        const hasMedical = String(user.has_medical || '').toLowerCase() === 'sim';
        const phoneFormatted = maskPhone(String(user.phone || ''));
        const cpfFormatted = maskCpf(String(user.cpf || ''));

        document.getElementById('edit-user-body').innerHTML = `
            <div class="row g-3">
                <div class="col-12 d-flex flex-column align-items-center gap-2">
                    <label class="form-label mb-0">Foto</label>
                    <img id="edit-user-photo-preview" src="${imgSrc}"
                        style="width:100px;height:100px;border-radius:50%;object-fit:cover;cursor:pointer;"
                        title="Clique para alterar foto"
                        onclick="document.getElementById('edit-user-photo-input').click()">
                    <input type="file" id="edit-user-photo-input" accept="image/*" class="d-none">
                    <input type="hidden" id="edit-user-photo-base64">
                    <small class="text-muted">Clique na foto para alterar</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nome completo</label>
                    <input type="text" id="edit-user-name" class="form-control" value="${user.name || ''}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">E-mail</label>
                    <input type="email" id="edit-user-email" class="form-control" value="${user.email || ''}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telefone</label>
                    <input type="text" id="edit-user-phone" class="form-control" value="${phoneFormatted}" maxlength="14" inputmode="numeric">
                </div>
                <div class="col-md-4">
                    <label class="form-label">CPF</label>
                    <input type="text" id="edit-user-cpf" class="form-control" value="${cpfFormatted}" maxlength="14">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Nova Senha <small class="text-muted">(deixe em branco para não alterar)</small></label>
                    <input type="password" id="edit-user-password" class="form-control" placeholder="Nova senha">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Conselho (CRM / CRN etc.)</label>
                    <input type="text" id="edit-user-advice" class="form-control" value="${user.advice || ''}">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check form-switch ms-2 mb-2">
                        <input class="form-check-input" type="checkbox" id="edit-user-has-medical" ${hasMedical ? 'checked' : ''}>
                        <label class="form-check-label" for="edit-user-has-medical">Profissional de saúde</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="button" class="btn btn-primary" id="btn-save-user">Salvar alterações</button>
                <a href="{{ route('panel.users.index') }}" class="btn btn-secondary">Voltar</a>
            </div>
        `;

        document.getElementById('edit-user-photo-input').addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('edit-user-photo-preview').src = e.target.result;
                document.getElementById('edit-user-photo-base64').value = e.target.result;
            };
            reader.readAsDataURL(file);
        });

        document.getElementById('edit-user-phone').addEventListener('input', function () {
            this.value = maskPhone(this.value);
        });

        document.getElementById('edit-user-cpf').addEventListener('input', function () {
            this.value = maskCpf(this.value);
        });

        document.getElementById('btn-save-user').addEventListener('click', saveUser);
    }

    async function saveUser() {
        const btn = document.getElementById('btn-save-user');
        const name     = document.getElementById('edit-user-name').value.trim();
        const email    = document.getElementById('edit-user-email').value.trim();
        const phone    = document.getElementById('edit-user-phone').value.replace(/\D/g, '');
        const cpf      = document.getElementById('edit-user-cpf').value.replace(/\D/g, '');
        const password = document.getElementById('edit-user-password').value;
        const advice   = document.getElementById('edit-user-advice').value.trim();
        const hasMedical = document.getElementById('edit-user-has-medical').checked ? 'Sim' : 'Não';
        const img      = document.getElementById('edit-user-photo-base64').value || null;

        if (!name) { showToast('Preencha o nome.', 'danger'); return; }

        const payload = { name, email, phone, cpf, advice, has_medical: hasMedical };
        if (password) payload.password = password;
        if (img) payload.img = img;

        const prev = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Salvando...';

        try {
            await apiPut(`{{ url('/') }}/users/${USER_ID}`, payload);
            showToast('Colaborador atualizado com sucesso.', 'success');
            loadUser();
        } catch (e) {
            showToast(e.message || 'Erro ao salvar', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerText = prev;
        }
    }

    async function loadUser() {
        try {
            const data = await apiGet(`{{ url('/') }}/users/${USER_ID}`);
            const user = data.data ?? data;
            renderForm(user);
        } catch (e) {
            document.getElementById('edit-user-body').innerHTML =
                `<div class="alert alert-danger">Erro ao carregar colaborador: ${e.message}</div>`;
        }
    }

    loadUser();
</script>
@endpush

