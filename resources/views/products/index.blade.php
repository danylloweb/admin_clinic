@extends('layouts.header')
@section('content')
<div class="">
    <div class="card mb-3">
        <div class="card-body">
            <h3 class="card-title fs-5">Cadastro de Produtos e Controle de Estoque</h3>
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Categoria</label>
                    <select id="filter-category" class="form-select">
                        <option value="">Todos</option>
                        <option value="cosmetic">Cosmético</option>
                        <option value="dermocosmetic">Dermocosmético</option>
                        <option value="medicine">Medicamento</option>
                        <option value="botulinum_toxin">Toxina Botulínica</option>
                        <option value="filler">Preenchedor</option>
                        <option value="biostimulator">Bioestimulador</option>
                        <option value="enzyme">Enzimas</option>
                        <option value="equipment">Equipamento</option>
                        <option value="disposable_material">Material Descartável</option>
                        <option value="consumable_material">Material de Consumo</option>
                        <option value="input">Insumo</option>
                        <option value="other">Outro</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select id="filter-status" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Rastreabilidade</label>
                    <select id="filter-tracking" class="form-select">
                        <option value="">Todos</option>
                        <option value="1">Com Rastreamento</option>
                        <option value="0">Sem Rastreamento</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button id="btn-apply-filters" class="btn btn-primary">Filtrar</button>
                    <button id="btn-clear-filters" class="btn btn-secondary">Limpar</button>
                </div>
            </div>
            <div class="p-1 table-responsive">
                <table id="datatable-products" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Estoque Atual</th>
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
    let productsTable;

    $(function () {
        productsTable = $('#datatable-products').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            ajax: function(data, callback, settings) {
                const page = Math.floor(data.start / data.length) + 1;
                const search = data.search.value;
                const limit = data.length === -1 ? 0 : data.length;

                const columnMap = {
                    0: 'id',
                    1: 'internal_code',
                    2: 'name',
                    3: 'category_type',
                    4: 'current_stock',
                    5: 'status',
                    6: 'updated_at',
                    7: 'id'
                };

                const orderColumnIndex = data.order[0].column;
                const orderDir = data.order[0].dir;
                const orderBy = columnMap[orderColumnIndex] || 'id';

                $.ajax({
                    url: '{{ route("products.index") }}',
                    method: 'GET',
                    data: {
                        limit: limit,
                        orderBy: orderBy,
                        sortedBy: orderDir,
                        page: page,
                        search: search,
                        category_type: document.getElementById('filter-category').value || null,
                        status: document.getElementById('filter-status').value || null,
                        requires_patient_tracking: document.getElementById('filter-tracking').value || null,
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
                { data: 'internal_code' },
                { data: 'name' },
                {
                    data: 'category_label',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'current_stock',
                    render: function(data) {
                        return '<span class="badge bg-info">' + data + '</span>';
                    },
                    orderable: false,
                    searchable: false
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        const status = row.status ? 'Ativo' : 'Inativo';
                        const badge = row.status ? 'badge bg-success' : 'badge bg-danger';
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
                            <button class="btn btn-sm btn-primary me-1" onclick="viewProduct(${data})">
                                <i class="ph ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProduct(${data})">
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
            productsTable.ajax.reload();
        });

        document.getElementById('btn-clear-filters').addEventListener('click', function () {
            document.getElementById('filter-category').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-tracking').value = '';
            productsTable.ajax.reload();
        });
    });

    function viewProduct(id) {
        window.location.href = '{{ route("panel.products.show", ":id") }}'.replace(':id', id);
    }

    function deleteProduct(id) {
        if (confirm('Deseja realmente deletar este produto?')) {
            $.ajax({
                url: '/products/' + id,
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function() {
                    showToast('Produto deletado com sucesso', 'success');
                    productsTable.ajax.reload();
                },
                error: function(xhr) {
                    showToast('Erro ao deletar produto', 'danger');
                }
            });
        }
    }
</script>
@endpush

