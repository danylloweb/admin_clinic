@extends('layouts.header')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                <h3 class="card-title fs-5 mb-0">Lista de Colaboradores</h3>
                <button type="button" class="btn btn-primary btn-sm" id="btn-open-create-user-modal">
                    <i class="ph ph-user-plus"></i> Novo Colaborador
                </button>
            </div>

            <div class="p-1 table-responsive">
                <table id="datatable-users" class="table table-bordered table-striped align-middle" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th>CPF</th>
                            <th>Conselho</th>
                            <th>Médico</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal cadastrar colaborador --}}
    <div class="modal fade" id="modal-create-user" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Colaborador</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12 d-flex flex-column align-items-center gap-2">
                            <label class="form-label mb-0">Foto</label>
                            <img id="create-user-photo-preview" src="https://ui-avatars.com/api/?name=Novo&background=6c757d&color=fff&size=100"
                                style="width:90px;height:90px;border-radius:50%;object-fit:cover;cursor:pointer;"
                                title="Clique para escolher foto" onclick="document.getElementById('create-user-photo-input').click()">
                            <input type="file" id="create-user-photo-input" accept="image/*" class="d-none">
                            <input type="hidden" id="create-user-photo-base64">
                            <small class="text-muted">Clique na foto para alterar</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nome completo <span class="text-danger">*</span></label>
                            <input type="text" id="create-user-name" class="form-control" placeholder="Nome completo">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail <span class="text-danger">*</span></label>
                            <input type="email" id="create-user-email" class="form-control" placeholder="email@exemplo.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Telefone <span class="text-danger">*</span></label>
                            <input type="text" id="create-user-phone" class="form-control" placeholder="(81)99999-0000" maxlength="14" inputmode="numeric">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CPF <span class="text-danger">*</span></label>
                            <input type="text" id="create-user-cpf" class="form-control" placeholder="000.000.000-00" maxlength="14">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Senha <span class="text-danger">*</span></label>
                            <input type="password" id="create-user-password" class="form-control" placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Conselho (CRM / CRN etc.)</label>
                            <input type="text" id="create-user-advice" class="form-control" placeholder="Ex: CRM 12345">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check form-switch ms-2 mb-2">
                                <input class="form-check-input" type="checkbox" id="create-user-has-medical">
                                <label class="form-check-label" for="create-user-has-medical">Profissional de saúde</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-save-create-user">Salvar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let usersTable;
    let createUserModal;

    function normalizeModalLayering(modalId) {
        const modalEl = document.getElementById(modalId);
        if (!modalEl) return;
        if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
        modalEl.style.zIndex = '1060';
        document.querySelectorAll('.modal-backdrop').forEach(b => b.style.zIndex = '1050');
    }

    function cleanupModalArtifacts() {
        document.querySelectorAll('.modal-backdrop').forEach(b => b.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
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

    function resetCreateUserForm() {
        document.getElementById('create-user-name').value = '';
        document.getElementById('create-user-email').value = '';
        document.getElementById('create-user-phone').value = '';
        document.getElementById('create-user-cpf').value = '';
        document.getElementById('create-user-password').value = '';
        document.getElementById('create-user-advice').value = '';
        document.getElementById('create-user-has-medical').checked = false;
        document.getElementById('create-user-photo-base64').value = '';
        document.getElementById('create-user-photo-preview').src =
            'https://ui-avatars.com/api/?name=Novo&background=6c757d&color=fff&size=100';
        document.getElementById('create-user-photo-input').value = '';
    }

    document.getElementById('create-user-photo-input').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('create-user-photo-preview').src = e.target.result;
            document.getElementById('create-user-photo-base64').value = e.target.result;
        };
        reader.readAsDataURL(file);
    });

    document.getElementById('create-user-phone').addEventListener('input', function () {
        this.value = maskPhone(this.value);
    });

    document.getElementById('create-user-cpf').addEventListener('input', function () {
        this.value = maskCpf(this.value);
    });

    async function saveNewUser() {
        const btn = document.getElementById('btn-save-create-user');
        const name     = document.getElementById('create-user-name').value.trim();
        const email    = document.getElementById('create-user-email').value.trim();
        const phone    = document.getElementById('create-user-phone').value.replace(/\D/g, '');
        const cpf      = document.getElementById('create-user-cpf').value.replace(/\D/g, '');
        const password = document.getElementById('create-user-password').value;
        const advice   = document.getElementById('create-user-advice').value.trim();
        const hasMedical = document.getElementById('create-user-has-medical').checked ? 'Sim' : 'Não';
        const img      = document.getElementById('create-user-photo-base64').value || null;

        if (!name) { showToast('Preencha o nome.', 'danger'); return; }
        if (!email) { showToast('Preencha o e-mail.', 'danger'); return; }
        if (!phone) { showToast('Preencha o telefone.', 'danger'); return; }
        if (!cpf) { showToast('Preencha o CPF.', 'danger'); return; }
        if (!password) { showToast('Preencha a senha.', 'danger'); return; }

        const prev = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'Salvando...';

        try {
            const res = await fetch('{{ route("users.store") }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, phone, cpf, password, advice, has_medical: hasMedical, img })
            });

            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                const msgs = Object.values(err.errors || {}).flat().join('\n');
                showToast(msgs || err.message || 'Erro ao cadastrar', 'danger');
                return;
            }

            showToast('Colaborador cadastrado com sucesso.', 'success');
            createUserModal.hide();
            resetCreateUserForm();
            usersTable.ajax.reload(null, false);
        } catch (e) {
            showToast(e.message || 'Erro ao cadastrar', 'danger');
        } finally {
            btn.disabled = false;
            btn.innerText = prev;
        }
    }

    $(function () {
        usersTable = $('#datatable-users').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [[0, 'desc']],
            ajax: function (data, callback) {
                const page  = Math.floor(data.start / data.length) + 1;
                const search = data.search.value;
                const limit = data.length === -1 ? 15 : data.length;
                const columnMap = { 0: 'id', 2: 'name', 3: 'email', 4: 'phone' };
                const orderBy  = columnMap[data.order?.[0]?.column] || 'id';
                const sortedBy = data.order?.[0]?.dir || 'desc';

                $.ajax({
                    url: '{{ route("users.index") }}',
                    method: 'GET',
                    data: { limit, page, search, orderBy, sortedBy },
                    success: function (response) {
                        callback({
                            recordsTotal:    response.meta?.pagination?.total || 0,
                            recordsFiltered: response.meta?.pagination?.total || 0,
                            data: response.data || []
                        });
                    },
                    error: function () {
                        callback({ recordsTotal: 0, recordsFiltered: 0, data: [] });
                    }
                });
            },
            columns: [
                { data: 'id' },
                {
                    data: 'img',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const src = data || `https://ui-avatars.com/api/?name=${encodeURIComponent(row.name || 'U')}&background=6c757d&color=fff&size=60`;
                        return `<img src="${src}" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">`;
                    }
                },
                { data: 'name' },
                { data: 'email', orderable: false },
                { data: 'phone', orderable: false },
                { data: 'cpf', orderable: false, searchable: false },
                { data: 'advice', orderable: false, searchable: false, render: d => d || '-' },
                {
                    data: 'has_medical',
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        const isYes = String(data).toLowerCase() === 'sim';
                        return isYes
                            ? '<span class="badge bg-success">Sim</span>'
                            : '<span class="badge bg-primary">Não</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<a href="{{ url('/panel-users-edit') }}/${row.id}" class="btn btn-sm btn-outline-primary">
                                    <i class="ph ph-pencil-simple-line"></i> Editar
                                </a>`;
                    }
                }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
        });
    });

    document.getElementById('btn-open-create-user-modal').addEventListener('click', function () {
        resetCreateUserForm();
        normalizeModalLayering('modal-create-user');
        createUserModal.show();
        normalizeModalLayering('modal-create-user');
    });

    document.getElementById('btn-save-create-user').addEventListener('click', saveNewUser);

    document.getElementById('modal-create-user').addEventListener('shown.bs.modal', function () {
        normalizeModalLayering('modal-create-user');
    });
    document.getElementById('modal-create-user').addEventListener('hidden.bs.modal', cleanupModalArtifacts);

    createUserModal = new bootstrap.Modal(document.getElementById('modal-create-user'));
</script>
@endpush

