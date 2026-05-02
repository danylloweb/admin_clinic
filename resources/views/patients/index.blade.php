@extends('layouts.header')
@section('content')
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                    <h3 class="card-title fs-5 mb-0">Lista de Pacientes</h3>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-open-create-patient-modal">
                        <i class="ph ph-user-plus"></i> Novo paciente
                    </button>
                </div>
                <div class="p-1 table-responsive">
                    <table id="datatable-patients" class="table table-bordered table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nome</th>
                                <th>Contato</th>
                                <th>Foto</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- Modal de visualização da foto -->
        <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-body p-0 text-center position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        <img id="photoModalImg" src="" alt="Foto" style="max-width:100%; height:auto; display:block; margin:0 auto; border-radius:6px;"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-create-patient" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cadastrar novo paciente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nome</label>
                                <input type="text" id="new-patient-name" class="form-control" placeholder="Nome completo">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nome social</label>
                                <input type="text" id="new-patient-social-name" class="form-control" placeholder="Opcional">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Telefone</label>
                                <input type="text" id="new-patient-phone" class="form-control" placeholder="(81)99999-0000" maxlength="15" inputmode="numeric">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Data de nascimento</label>
                                <input type="date" id="new-patient-birth-date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sexo</label>
                                <select id="new-patient-sex" class="form-select">
                                    <option value="F">Feminino</option>
                                    <option value="M">Masculino</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="btn-save-new-patient">Salvar paciente</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        const MODAL_IDS = ['photoModal', 'modal-create-patient'];
        let patientsTable;
        let createPatientModal;
        let backdropObserver = null;

        function maskPhoneValue(value) {
            let v = String(value || '').replace(/\D/g, '');
            if (v.length > 11) {
                v = v.slice(0, 11);
            }

            if (v.length > 7) {
                return '(' + v.slice(0, 2) + ')' + v.slice(2, 7) + '-' + v.slice(7);
            }
            if (v.length > 2) {
                return '(' + v.slice(0, 2) + ')' + v.slice(2);
            }
            if (v.length > 0) {
                return '(' + v;
            }
            return '';
        }

        function resetCreatePatientForm() {
            document.getElementById('new-patient-name').value = '';
            document.getElementById('new-patient-social-name').value = '';
            document.getElementById('new-patient-phone').value = '';
            document.getElementById('new-patient-birth-date').value = '';
            document.getElementById('new-patient-sex').value = 'F';
        }

        function normalizeModalLayering(modalId) {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) {
                return;
            }

            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            modalEl.style.zIndex = '1060';
            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                backdrop.style.zIndex = '1050';
            });
        }

        function reconcileModalBackdrops() {
            const visibleModals = Array.from(document.querySelectorAll('.modal.show'));
            const backdrops = Array.from(document.querySelectorAll('.modal-backdrop'));

            if (visibleModals.length === 0) {
                backdrops.forEach((backdrop) => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                return;
            }

            backdrops.forEach((backdrop, index) => {
                if (index > 0) {
                    backdrop.remove();
                }
            });

            const activeBackdrop = document.querySelector('.modal-backdrop');
            if (activeBackdrop) {
                activeBackdrop.style.zIndex = '1050';
            }

            visibleModals.forEach((modalEl) => {
                modalEl.style.zIndex = '1060';
            });
        }

        function enforceBackdropSafety() {
            const visibleModals = document.querySelectorAll('.modal.show').length;
            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                if (visibleModals === 0) {
                    backdrop.remove();
                    return;
                }

                backdrop.style.zIndex = '1050';
                backdrop.style.pointerEvents = 'none';
            });
        }

        function observeBackdropInsertions() {
            if (backdropObserver) {
                backdropObserver.disconnect();
            }

            backdropObserver = new MutationObserver(() => {
                enforceBackdropSafety();
                reconcileModalBackdrops();
            });

            backdropObserver.observe(document.body, { childList: true });
        }

        function initializeModalLayering() {
            MODAL_IDS.forEach((modalId) => {
                const modalEl = document.getElementById(modalId);
                if (!modalEl) {
                    return;
                }

                normalizeModalLayering(modalId);
                modalEl.addEventListener('shown.bs.modal', function () {
                    normalizeModalLayering(modalId);
                    setTimeout(reconcileModalBackdrops, 0);
                    setTimeout(enforceBackdropSafety, 0);
                });
                modalEl.addEventListener('hidden.bs.modal', function () {
                    setTimeout(reconcileModalBackdrops, 0);
                    setTimeout(enforceBackdropSafety, 0);
                });
            });
        }

        async function createPatient() {
            const saveButton = document.getElementById('btn-save-new-patient');
            const name = document.getElementById('new-patient-name').value.trim();
            const socialName = document.getElementById('new-patient-social-name').value.trim();
            const phone = document.getElementById('new-patient-phone').value.trim();
            const birthDate = document.getElementById('new-patient-birth-date').value;
            const sex = document.getElementById('new-patient-sex').value || 'F';

            if (!name) {
                showToast('Preencha o nome do paciente.', 'danger');
                return;
            }

            if (!phone) {
                showToast('Preencha o telefone do paciente.', 'danger');
                return;
            }

            if (!birthDate) {
                showToast('Preencha a data de nascimento do paciente.', 'danger');
                return;
            }

            const previousText = saveButton.innerText;
            saveButton.disabled = true;
            saveButton.innerText = 'Salvando...';

            try {
                const res = await fetch('{{ route('patients.store') }}', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        name,
                        social_name: socialName,
                        phone,
                        birth_date: birthDate,
                        sex,
                    })
                });

                if (!res.ok) {
                    const errorResponse = await res.json().catch(() => ({}));
                    const validationMessages = Object.values(errorResponse.errors || {}).flat().join('\n');
                    showToast(validationMessages || errorResponse.message || 'Erro ao cadastrar paciente', 'danger');
                    return;
                }

                showToast('Paciente cadastrado com sucesso.', 'success');
                createPatientModal.hide();
                resetCreatePatientForm();
                if (patientsTable) {
                    patientsTable.ajax.reload(null, false);
                }
            } catch (error) {
                showToast(error.message || 'Erro ao cadastrar paciente', 'danger');
            } finally {
                saveButton.disabled = false;
                saveButton.innerText = previousText;
            }
        }

        $(function () {
            patientsTable = $('#datatable-patients').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;
                    const limit = data.length === -1 ? 0 : data.length;
                    const columnMap = {
                        0: 'id',
                        1: 'name',
                        2: 'phone',
                        3: 'photo',
                        4: 'id'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir = data.order[0].dir;
                    const orderBy = columnMap[orderColumnIndex] || 'id';

                    $.ajax({
                        url: '{{ route("patients.index") }}',
                        method: 'GET',
                        data: {
                            limit: limit,
                            orderBy: orderBy,
                            sortedBy: orderDir,
                            page: page,
                            search: search
                        },
                        success: function(response) {
                            callback({
                                recordsTotal: response.meta.pagination.total,
                                recordsFiltered: response.meta.pagination.total,
                                data: response.data
                            });
                        }
                    });
                },
                columns: [
                    { data: 'id' },
                    { data: 'name' },
                    { data: 'phone',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'photo',
                        render: function (data) {
                            return `<img src="${data}" data-src="${data}" alt="imagem" class="img-clickable" style="width:60px; height:auto; border-radius:5px; cursor:pointer;"/>`;
                        },
                      orderable: false,
                      searchable: false
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                            <button class="btn btn-sm btn-primary me-1" onclick="viewPatient(${data})">
                              <i class="ph ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-primary me-1" onclick="chatPatient(${data})">
                              <i class="ph ph-whatsapp-logo"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deletePatient(${data})">
                              <i class="ph ph-trash"></i>
                            </button>
                          `;
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                }
            });
            // Delegated click handler para imagens (funciona com render do DataTable)
            $('#datatable-patients').on('click', '.img-clickable', function (e) {
                e.preventDefault();
                const src = $(this).data('src') || $(this).attr('src');
                $('#photoModalImg').attr('src', src);

                const modalEl = document.getElementById('photoModal');
                if (typeof bootstrap === 'undefined') {
                    console.error('Bootstrap JS não carregado.');
                    return;
                }
                // getOrCreateInstance evita criar múltiplas instâncias que podem quebrar o fechamento
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                normalizeModalLayering('photoModal');
                modal.show();
                normalizeModalLayering('photoModal');
            });
        });

        $(function () {
            const modalEl = document.getElementById('photoModal');
            if (!modalEl) return;

            // Clique no fundo do modal (target === modal) ou na imagem fecha
            $(modalEl).on('click', function (e) {
                if (e.target === modalEl || e.target.id === 'photoModalImg') {
                    closePhotoModal();
                }
            });

            // Garante que o botão .btn-close também invoque a função
            $('#photoModal .btn-close').on('click', function () {
                closePhotoModal();
            });

            // Também limpa o src quando o modal for totalmente ocultado
            modalEl.addEventListener('hidden.bs.modal', function () {
                const img = document.getElementById('photoModalImg');
                if (img) img.src = '';
            });
        });

        function closePhotoModal() {
            const modalEl = document.getElementById('photoModal');
            if (!modalEl || typeof bootstrap === 'undefined') return;
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.hide();
            const img = document.getElementById('photoModalImg');
            if (img) img.src = '';
        }

        function viewPatient(id) {
            window.location.href = '{{ route("panel.patient.show", ":id") }}'.replace(':id', id);
        }

        function chatPatient(id) {
            window.location.href = '{{ route("panel.patient.chat", ":id") }}'.replace(':id', id);
        }

        function deletePatient(id) {
            console.log('Deletar campanha com ID:', id);
        }

        document.getElementById('btn-open-create-patient-modal').addEventListener('click', function () {
            resetCreatePatientForm();
            normalizeModalLayering('modal-create-patient');
            createPatientModal.show();
            normalizeModalLayering('modal-create-patient');
        });

        document.getElementById('btn-save-new-patient').addEventListener('click', createPatient);
        document.getElementById('new-patient-phone').addEventListener('input', function () {
            this.value = maskPhoneValue(this.value);
        });
        observeBackdropInsertions();
        initializeModalLayering();
        createPatientModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-create-patient'));
    </script>
@endpush
