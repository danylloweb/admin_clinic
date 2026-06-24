@extends('layouts.header')
@section('content')
        <div class="card mb-3">
            <div class="card-body">
                <h3 class="card-title fs-5">Lista de Campanhas</h3>
                <div class="p-1 table-responsive">
                    <table id="datatable-campaigns" class="table table-bordered table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Nome</th>
                                <th>Data</th>
                                <th>Imagem</th>
                                <th>Status</th>
                                <th>Ação</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        $(function () {
            const table = $('#datatable-campaigns').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                order: [[0, 'desc']],
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;

                    const columnMap = {
                        0: 'id',
                        1: 'name',
                        2: 'date',
                        3: 'url_image',
                        4: 'status',
                        5: 'id'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir = data.order[0].dir || 'desc';
                    const orderBy = columnMap[orderColumnIndex] || 'id';

                    $.ajax({
                        url: '{{ route("campaigns.index") }}',
                        method: 'GET',
                        data: {
                            limit: 15,
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
                    { data: 'date',
                        render: function (data) {
                            return moment(data).format('DD/MM/YYYY');
                        },
                        orderable: false,
                        searchable: false
                    },
                    { data: 'url_image',
                        render: function (data) {
                           return `<img src="${data}" alt="imagem" style="width: 60px; height: auto; border-radius: 5px;"/>`;
                        },
                      orderable: false,
                      searchable: false
                    },
                    { data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        render: function (data) {
                            return `
                            <button class="btn btn-sm btn-primary me-1" onclick="viewCampaign(${data})">
                              <i class="ph ph-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCampaign(${data})">
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

        function viewCampaign(id) {
            window.location.href = '{{ route("panel-campaign-show", ":id") }}'.replace(':id', id);
        }

        function deleteCampaign(id) {
            console.log('Deletar campanha com ID:', id);
        }
    </script>
@endpush
