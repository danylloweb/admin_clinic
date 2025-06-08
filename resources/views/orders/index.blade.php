@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h1>
            Orders
            <small>Lista de pedidos</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">Orders</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="form-group">
                    <label for="statusFilter">Filtrar por status:</label>
                    <select id="statusFilter" class="form-control" style="width: 190px; display: inline-block; margin-bottom: 10px;">
                        <option value="">Todos</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}">{{ $status->translate() }}</option>
                        @endforeach
                    </select>
                </div>
                <table id="orders-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pedido Externo</th>
                        <th>Hash</th>
                        <th>Status</th>
                        <th>CEP</th>
                        <th>Atualizado em</th>
                        <th>Ação</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(function () {
            let statusFilter = '';
            const table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;

                    $.ajax({
                        url: '{{ route('orders.index') }}',
                        method: 'GET',
                        data: {
                            limit: data.length,
                            orderBy: 'id',
                            sortedBy: 'desc',
                            page: page,
                            search: search,
                            status: statusFilter
                        },
                        headers: {
                            'Authorization': 'b473bf7f-3ef8-4b90-a37a-bce820efe5e8',
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
                    { data: 'external_order_id' },
                    { data: 'order_hash' },
                    { data: 'status_title' },
                    { data: 'zip_code' },
                    {
                        data: 'updated_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                      data: 'id',
                       render: function(data, type, row) {
                        const showUrl = '{{ route("painel.orders.show", ":id") }}'.replace(':id', data);
                        return `<a href="${showUrl}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> Ver</a>`;
                       }, orderable: false, searchable: false
                    }
                ]
            });
            $('#statusFilter').on('change', function () {
                statusFilter = $(this).val();
                table.ajax.reload();
            });
        });
    </script>
@endpush
