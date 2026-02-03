@extends('layouts.header')
@section('content')
    <div class="">
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title fs-5">Lista de Procedimentos</h3>
                <div class="p-1 table-responsive">
                    <table id="datatable-procedure" class="table table-bordered table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Preço a vista</th>
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
        $(function () {
            const table = $('#datatable-procedure').DataTable({
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
                        2: 'procedure_type_name',
                        3: 'price',
                        4: 'status',
                        5: 'updated_at',
                        6: 'id'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir = data.order[0].dir;
                    const orderBy = columnMap[orderColumnIndex] || 'id';

                    $.ajax({
                        url: '{{ route("procedures.index") }}',
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
                    { data: 'procedure_type_name',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'price',
                       render: function(data) {
                            return 'R$ ' + parseFloat(data).toFixed(2).replace('.', ',');
                       },
                      orderable: false,
                      searchable: false
                    },
                    { data: 'status',
                        orderable: false,
                        searchable: false
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
                            <button class="btn btn-sm btn-primary me-1" onclick="viewProcedure(${data})">
                              <i class="ph ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteProcedure(${data})">
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
        });

        function viewProcedure(id) {
            window.location.href = '{{ route("panel.procedure.show", ":id") }}'.replace(':id', id);
        }

        function deleteProcedure(id) {
            console.log('Deletar procedimento com ID:', id);
        }
    </script>
@endpush
