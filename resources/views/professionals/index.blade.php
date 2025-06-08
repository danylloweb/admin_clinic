@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Profissionais
            <small>Lista de profissionais</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Início</a></li>
            <li class="active">Profissionais</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <table id="professionals-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Documento</th>
                        <th>Avatar</th>
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
            const table = $('#professionals-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;
                    const columnMap = {
                        0: 'id',
                        1: 'name',
                        2: 'document',
                        3: 'avatar_url',
                        5: 'updated_at'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir         = data.order[0].dir;
                    const orderBy          = columnMap[orderColumnIndex] || 'id';
                    $.ajax({
                        url: '{{ route('professionals.index') }}',
                        method: 'GET',
                        data: {
                            limit: data.length,
                            orderBy: orderBy,
                            sortedBy: orderDir,
                            page: page,
                            search: search
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
                    { data: 'name' },
                    {
                        data: 'document',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'avatar_url',
                        render: function(data) {
                            return data ? `<img src="${data}" alt="Avatar" width="40" height="40" style="border-radius: 50%;">` : '—';
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    }
                ]
            });
        });
    </script>
@endpush
