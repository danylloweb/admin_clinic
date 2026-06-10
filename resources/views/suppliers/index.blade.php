@extends('layouts.header')
@section('content')
<div class="">
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title fs-5">Gestão de Fornecedores</h3>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button id="btn-apply-filters" class="btn btn-primary">Filtrar</button>
                    <button id="btn-clear-filters" class="btn btn-secondary">Limpar</button>
                </div>
            </div>
            <div class="p-1 table-responsive">
                <table id="datatable-suppliers" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>CNPJ</th>
                            <th>Contato</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Atualizado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let suppliersTable;

    $(function () {
        suppliersTable = $('#datatable-suppliers').DataTable({
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
                    2: 'cnpj',
                    3: 'contact_name',
                    4: 'email',
                    5: 'active',
                    6: 'updated_at',
                    7: 'id'
                };

                const orderColumnIndex = data.order[0].column;
                const orderDir = data.order[0].dir;
                const orderBy = columnMap[orderColumnIndex] || 'id';

                $.ajax({
                    url: '{{ route("suppliers.index") }}',
                    method: 'GET',
                    data: {
                        limit: limit,
                        orderBy: orderBy,
                        sortedBy: orderDir,
                        page: page,
                        search: search,
                        active: document.getElementById('filter-status').value || null,
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
                {
                    data: 'cnpj',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'contact_name',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: 'email',
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const status = row.active ? 'Ativo' : 'Inativo';
                        const badge = row.active ? 'badge bg-success' : 'badge bg-danger';
                        return '<span class="' + badge + '">' + status + '</span>';
                    }
                },
                {
                    data: 'updated_at',
                    render: function(data) {
                        return moment(data).format('DD/MM/YYYY HH:mm');
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data) {
                        return `
                            <button class="btn btn-sm btn-primary me-1" onclick="viewSupplier(${data})" title="Visualizar">
                                <i class="ph ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-warning me-1" onclick="editSupplier(${data})" title="Editar">
                                <i class="ph ph-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteSupplier(${data})" title="Deletar">
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

        document.getElementById('btn-apply-filters').addEventListener('click', function () {
            suppliersTable.ajax.reload();
        });

        document.getElementById('btn-clear-filters').addEventListener('click', function () {
            document.getElementById('filter-status').value = '';
            suppliersTable.ajax.reload();
        });
    });

    function viewSupplier(id) {
        window.location.href = '{{ route("panel.suppliers.show", ":id") }}'.replace(':id', id);
    }

    function editSupplier(id) {
        window.location.href = '{{ route("panel.suppliers.edit", ":id") }}'.replace(':id', id);
    }

    function deleteSupplier(id) {
        if (confirm('Deseja realmente deletar este fornecedor?')) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            $.ajax({
                url: '/suppliers/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function() {
                    showToast('Fornecedor deletado com sucesso', 'success');
                    suppliersTable.ajax.reload();
                },
                error: function(xhr) {
                    showToast('Erro ao deletar fornecedor', 'danger');
                }
            });
        }
    }
</script>
@endpush

