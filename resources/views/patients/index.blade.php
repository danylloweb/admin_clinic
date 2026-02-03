@extends('layouts.header')
@section('content')
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title fs-5">Lista de Pacientes</h3>
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
@endsection

@push('scripts')
    <script>
        $(function () {
            const table = $('#datatable-patients').DataTable({
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
                modal.show();
                _removeBackdrops();
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
        function _removeBackdrops() {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.paddingRight = '';
            document.body.style.overflow = '';
        }
    </script>
@endpush
