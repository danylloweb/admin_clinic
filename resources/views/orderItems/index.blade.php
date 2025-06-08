@extends('layouts.app')
@section('content')
    <section class="content-header">
        <h1>
            Items
            <small>Lista de Items pedidos</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Inicio</a></li>
            <li class="active">OrdersItems</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="form-inline" style="margin-bottom: 10px;">
                    <div class="form-group" style="margin-right: 10px;">
                        <label for="filterStatus">Status:</label>
                        <select id="filterStatus" class="form-control">
                            <option value="">Todos</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}">{{ $status->translate() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin-right: 10px;">
                        <label for="startDate">Data Início:</label>
                        <input type="date" id="startDate" class="form-control">
                    </div>

                    <div class="form-group" style="margin-right: 10px;">
                        <label for="endDate">Data Fim:</label>
                        <input type="date" id="endDate" class="form-control">
                    </div>

                    <div class="form-group" style="margin-right: 10px;">
                        <label for="Type">Tipo:</label>
                        <select id="filterType" class="form-control">
                            <option value="">Todos</option>
                            @foreach($types as $type)
                                <option value="{{ $type->value }}">{{ $type->translateReport() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button id="applyFilters" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Aplicar Filtros
                    </button>

                    <button id="generatePdf" class="btn btn-success">
                        <i class="fa fa-file-pdf-o"></i> Gerar PDF
                    </button>
                </div>
                <table id="orders-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pedido Externo</th>
                        <th>Descrição Serviço</th>
                        <th>Descrição Produto</th>
                        <th>Preço</th>
                        <th>Status</th>
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
            let filterType = '';
            const table = $('#orders-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;
                    statusFilter  = $('#filterStatus').val();
                    filterType  = $('#filterType').val();
                    let startDate = $('#startDate').val();
                    let endDate   = $('#endDate').val();

                    if (startDate && endDate && startDate > endDate) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Data inválida',
                            text: 'A data inicial não pode ser maior que a data final.',
                        });
                        $('#startDate').val('');
                        $('#endDate').val('');
                        startDate = null;
                        endDate   = null;
                    }
                    const columnMap = {
                        0: 'id',
                        1: 'external_order_id',
                        2: 'ref_description',
                        3: 'ref_parent_description',
                        4: 'price',
                        5: 'status_title',
                        6: 'updated_at',
                        7: 'id'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir         = data.order[0].dir;
                    const orderBy          = columnMap[orderColumnIndex] || 'id';
                    $.ajax({
                        url: '{{ route('order.items.index') }}',
                        method: 'GET',
                        data: {
                            limit: data.length,
                            orderBy: orderBy,
                            sortedBy: orderDir,
                            page: page,
                            search: search,
                            status: statusFilter,
                            type:filterType,
                            start_date: startDate,
                            end_date: endDate
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
                    { data: 'ref_description' },
                    { data: 'ref_parent_description' },
                    { data: 'price' },
                    { data: 'status_title' },
                    {
                        data: 'updated_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                        data: 'id',
                        render: function(data, type, row) {
                            const showUrl = '{{ route("painel.order.item.show", ":id") }}'.replace(':id', data);
                            return `<a href="${showUrl}" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> Ver</a>`;
                        }, orderable: false, searchable: false
                    }
                ]
            });
            $('#applyFilters').on('click', function () {
                table.ajax.reload();
            });
        });
        $('#generatePdf').on('click', function () {
            const statusFilter = $('#filterStatus').val();
            const filterType = $('#filterType').val();
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();

            if (startDate && endDate && startDate > endDate) {
                Swal.fire({
                    icon: 'error',
                    title: 'Data inválida',
                    text: 'A data inicial não pode ser maior que a data final.',
                });
                return;
            }

            const search = $('#orders-table_filter input').val() ?? '';
            const params = new URLSearchParams({
                status: statusFilter,
                type: filterType,
                start_date: startDate,
                end_date: endDate,
                search: search
            });
            Swal.fire({
                title: 'Gerando PDF...',
                text: 'Aguarde enquanto o relatório está sendo preparado.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            setTimeout(() => {
                const reportUrl = `{{ route('painel.order.items.report') }}?${params.toString()}`;
                const printWindow = window.open(reportUrl, '_blank');

                const interval = setInterval(() => {
                    if (printWindow.document && printWindow.document.readyState === 'complete') {
                        clearInterval(interval);
                        printWindow.focus();
                        Swal.close();
                    }
                }, 500);

                setTimeout(() => {
                    Swal.close();
                }, 5000);
            }, 500);
        });
    </script>
@endpush
