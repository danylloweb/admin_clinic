@extends('layouts.header')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <h3 class="card-title fs-5">Histórico de Backups</h3>

        <div class="row g-3 mb-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select id="filter-status" class="form-select">
                    <option value="">Todos</option>
                    <option value="completed">Concluído</option>
                    <option value="running">Executando</option>
                    <option value="failed">Falhou</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button id="btn-apply-filters" class="btn btn-primary">Filtrar</button>
                <button id="btn-clear-filters" class="btn btn-secondary">Limpar</button>
            </div>
        </div>

        <div class="p-1 table-responsive">
            <table id="datatable-backups" class="table table-bordered table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Arquivo</th>
                    <th>Destino</th>
                    <th>Tamanho</th>
                    <th>Status</th>
                    <th>Finalizado em</th>
                    <th>Ações</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let backupsTable;

    function backupStatusBadge(status) {
        if (status === 'completed') {
            return '<span class="badge bg-success">Concluído</span>';
        }
        if (status === 'running') {
            return '<span class="badge bg-warning text-dark">Executando</span>';
        }
        if (status === 'failed') {
            return '<span class="badge bg-danger">Falhou</span>';
        }
        return '<span class="badge bg-secondary">' + (status || 'Desconhecido') + '</span>';
    }

    $(function () {
        backupsTable = $('#datatable-backups').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: function (data, callback) {
                const page = Math.floor(data.start / data.length) + 1;
                const search = data.search.value;
                const limit = data.length === -1 ? 15 : data.length;

                const columnMap = {
                    0: 'created_at',
                    1: 'file_name',
                    2: 'storage_disk',
                    3: 'size_bytes',
                    4: 'status',
                    5: 'completed_at',
                    6: 'created_at'
                };

                const orderColumnIndex = data.order?.[0]?.column ?? 5;
                const orderDir = data.order?.[0]?.dir ?? 'desc';
                const orderBy = columnMap[orderColumnIndex] || 'created_at';

                $.ajax({
                    url: '{{ route("backups.index") }}',
                    method: 'GET',
                    data: {
                        limit: limit,
                        page: page,
                        search: search,
                        orderBy: orderBy,
                        sortedBy: orderDir,
                        status: document.getElementById('filter-status').value || null,
                    },
                    success: function (response) {
                        callback({
                            recordsTotal: response.meta.pagination.total,
                            recordsFiltered: response.meta.pagination.total,
                            data: response.data
                        });
                    },
                    error: function () {
                        callback({
                            recordsTotal: 0,
                            recordsFiltered: 0,
                            data: []
                        });
                    }
                });
            },
            columns: [
                {
                    data: 'id',
                    render: function (data) {
                        return data || '-';
                    }
                },
                { data: 'file_name' },
                {
                    data: null,
                    render: function (_, __, row) {
                        const disk = row.storage_disk || '-';
                        const path = row.storage_path || '-';
                        return `
                            <div><span class="badge bg-info text-dark">${disk}</span></div>
                            <small class="text-muted">${path}</small>
                        `;
                    }
                },
                { data: 'size_label' },
                {
                    data: 'status',
                    render: function (data) {
                        return backupStatusBadge(data);
                    }
                },
                {
                    data: 'completed_at',
                    render: function (data, type, row) {
                        const value = data || row.created_at;
                        return value ? moment(value).format('DD/MM/YYYY HH:mm') : '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        const downloadButton = row.is_downloadable
                            ? `<a class="btn btn-sm btn-primary" href="${row.download_url}">
                                    <i class="ph ph-download"></i> Baixar
                               </a>`
                            : `<button class="btn btn-sm btn-secondary" disabled>
                                    <i class="ph ph-download"></i>
                               </button>`;

                        const errorButton = row.error_message
                            ? `<button class="btn btn-sm btn-danger ms-1" onclick='showBackupError(${JSON.stringify(String(row.error_message))})'>
                                    <i class="ph ph-warning-circle"></i>
                               </button>`
                            : '';

                        return downloadButton + errorButton;
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            order: [[5, 'desc']]
        });

        document.getElementById('btn-apply-filters').addEventListener('click', function () {
            backupsTable.ajax.reload();
        });

        document.getElementById('btn-clear-filters').addEventListener('click', function () {
            document.getElementById('filter-status').value = '';
            backupsTable.ajax.reload();
        });
    });

    function showBackupError(message) {
        showToast(message || 'Erro não informado', 'danger');
    }
</script>
@endpush

