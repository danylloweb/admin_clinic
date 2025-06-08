@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Agendamentos
            <small>Lista de agendamentos</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Início</a></li>
            <li class="active">Agendamentos</li>
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
                            @foreach($scheduleStatuses as $status)
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

                    <button id="applyFilters" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Aplicar Filtros
                    </button>
                </div>
                <table id="schedules-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Início</th>
                        <th>Fim</th>
                        <th>Status</th>
                        <th>Atualizado em</th>
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
            const table = $('#schedules-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page    = Math.floor(data.start / data.length) + 1;
                    const search  = data.search.value;
                    const status  = $('#filterStatus').val();
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
                        1: 'when_date',
                        2: 'when_time_start',
                        3: 'when_time_end',
                        4: 'status',
                        5: 'updated_at'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir         = data.order[0].dir;
                    const orderBy          = columnMap[orderColumnIndex] || 'id';

                    $.ajax({
                        url: '{{ route('schedules.index') }}',
                        method: 'GET',
                        data: {
                            limit: data.length,
                            orderBy: orderBy,
                            sortedBy: orderDir,
                            page: page,
                            search: search,
                            status: status,
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
                    { data: 'when_date_formatted'},
                    { data: 'when_time_start'},
                    { data: 'when_time_end'},
                    { data: 'status_title' },
                    {
                        data: 'updated_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    }
                ]
            });

            $('#applyFilters').on('click', function () {
                table.ajax.reload();
            });
        });

    </script>
@endpush
