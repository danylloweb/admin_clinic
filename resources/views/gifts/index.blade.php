@extends('layouts.header')
@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title fs-5">Lista de Brindes</h3>
            <div class="p-1 table-responsive">
                <table id="datatable-gifts" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Telefone</th>
                        <th>Procedimento</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Criado em</th>
                        <th>Ação</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gift-detail-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes do brinde</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-white">Nome</label>
                            <div id="gift-detail-name" class="fw-semibold">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Telefone</label>
                            <div id="gift-detail-phone">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Nome do parceiro</label>
                            <div id="gift-detail-partner-name">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Telefone do parceiro</label>
                            <div id="gift-detail-partner-phone">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Procedimento</label>
                            <div id="gift-detail-procedure-name">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Valor do procedimento</label>
                            <div id="gift-detail-procedure-value">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Status</label>
                            <div id="gift-detail-status">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Origem</label>
                            <div id="gift-detail-source">-</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label text-white">Página</label>
                            <div id="gift-detail-page" class="text-break">-</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-white">Criado em</label>
                            <div id="gift-detail-created-at">-</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let giftsTable;
        let giftDetailModal;

        function normalizeGiftModalLayering() {
            const modalEl = document.getElementById('gift-detail-modal');
            if (!modalEl) {
                return;
            }

            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            modalEl.style.zIndex = '1060';
            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => {
                backdrop.style.zIndex = '1050';
                backdrop.style.pointerEvents = 'none';
            });
        }

        function cleanupGiftModalArtifacts() {
            document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }

        function formatMoney(value) {
            const numeric = Number(value || 0);
            return 'R$ ' + numeric.toFixed(2).replace('.', ',');
        }

        function formatDate(value) {
            return value ? moment(value).format('DD/MM/YYYY HH:mm') : '-';
        }

        function showGiftDetail(row) {
            document.getElementById('gift-detail-name').innerText = row.name || '-';
            document.getElementById('gift-detail-phone').innerText = row.phoneFormatted || row.phone || '-';
            document.getElementById('gift-detail-partner-name').innerText = row.partnerName || '-';
            document.getElementById('gift-detail-partner-phone').innerText = row.partnerPhoneFormatted || row.partnerPhone || '-';
            document.getElementById('gift-detail-procedure-name').innerText = row.procedureName || '-';
            document.getElementById('gift-detail-procedure-value').innerText = formatMoney(row.procedureValue);
            document.getElementById('gift-detail-status').innerText = row.status || '-';
            document.getElementById('gift-detail-source').innerText = row.source || '-';
            document.getElementById('gift-detail-page').innerText = row.page || '-';
            document.getElementById('gift-detail-created-at').innerText = formatDate(row.created_at);

            cleanupGiftModalArtifacts();
            normalizeGiftModalLayering();
            giftDetailModal.show();
            normalizeGiftModalLayering();
        }

        $(function () {
            const modalEl = document.getElementById('gift-detail-modal');
            normalizeGiftModalLayering();
            giftDetailModal = bootstrap.Modal.getOrCreateInstance(modalEl);

            modalEl.addEventListener('shown.bs.modal', function () {
                normalizeGiftModalLayering();
            });

            modalEl.addEventListener('hidden.bs.modal', function () {
                cleanupGiftModalArtifacts();
            });

            giftsTable = $('#datatable-gifts').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;
                    const limit = data.length === -1 ? 15 : data.length;
                    const columnMap = {
                        0: 'id',
                        1: 'name',
                        2: 'phone',
                        3: 'procedureName',
                        4: 'procedureValue',
                        5: 'status',
                        6: 'created_at',
                        7: 'id'
                    };
                    const orderBy = columnMap[data.order?.[0]?.column] || 'id';
                    const sortedBy = data.order?.[0]?.dir || 'desc';

                    $.ajax({
                        url: '{{ route("gifts.index") }}',
                        method: 'GET',
                        data: {
                            limit,
                            page,
                            search,
                            orderBy,
                            sortedBy,
                        },
                        success: function(response) {
                            callback({
                                recordsTotal: response.meta.pagination.total,
                                recordsFiltered: response.meta.pagination.total,
                                data: response.data,
                            });
                        },
                        error: function() {
                            callback({
                                recordsTotal: 0,
                                recordsFiltered: 0,
                                data: [],
                            });
                        }
                    });
                },
                columns: [
                    {
                        data: null,
                        render: function (_, __, row) {
                            return row.id || row._id || '-';
                        }
                    },
                    { data: 'name' },
                    {
                        data: null,
                        render: function (_, __, row) {
                            return row.phoneFormatted || row.phone || '-';
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'procedureName' },
                    {
                        data: 'procedureValue',
                        render: function (value) {
                            return formatMoney(value);
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        render: function (value) {
                            return value || '-';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at',
                        render: function (value) {
                            return formatDate(value);
                        },
                        searchable: false
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function (_, __, row) {
                            const encoded = encodeURIComponent(JSON.stringify(row));
                            return '<button class="btn btn-sm btn-primary" onclick="openGiftDetail(\'' + encoded + '\')"><i class="ph ph-eye"></i></button>';
                        }
                    }
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
                }
            });
        });

        function openGiftDetail(encodedRow) {
            const row = JSON.parse(decodeURIComponent(encodedRow));
            showGiftDetail(row);
        }
    </script>
@endpush

