@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Pricings
            <small>Registros de precificação</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Início</a></li>
            <li class="active">Pricings</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <table id="pricings-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Serviço</th>
                        <th>Ref ID</th>
                        <th>Ref Parent ID</th>
                        <th>Valor</th>
                        <th>Atualizado em</th>
                        <th>Ações</th>
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
            const table = $('#pricings-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    let search_value = data.search.value;
                    let search = null;
                    if (search_value.length > 4){
                        search = search_value;
                    }
                    const columnMap = {
                        0: 'id',
                        1: 'service_name',
                        2: 'ref_id',
                        3: 'ref_parent_id',
                        5: 'amount',
                        6: 'updated_at'
                    };

                    const orderColumnIndex = data.order[0].column;
                    const orderDir         = data.order[0].dir;
                    const orderBy          = columnMap[orderColumnIndex] || 'id';
                    $.ajax({
                        url: '{{ route('pricings.index') }}',
                        method: 'GET',
                        data: {
                            limit: data.length,
                            orderBy: orderBy,
                            sortedBy: orderDir,
                            page: page,
                            search: search,
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
                    { data: 'service_name' },
                    {
                        data: 'ref_id',
                        orderable: false,
                        searchable: false
                    },
                    { data: 'ref_parent_id' },
                    {
                        data: 'amount',
                        render: function(data) {
                            return 'R$ ' + parseFloat(data).toFixed(2).replace('.', ',');
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        },
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'id',
                        render: function(id) {
                            return `
                                <button class="btn btn-sm btn-info renew-price-btn" data-id="${id}">
                                    <i class="fa fa-sync-alt"></i> Atualizar Preço
                                </button>`;
                        },
                        orderable: false,
                        searchable: false,
                    }
                ]
            });
        });
        $('#pricings-table').on('click', '.renew-price-btn', function () {
            const pricingId = $(this).data('id');

            Swal.fire({
                title: 'Atualizando preço...',
                text: 'Por favor, aguarde.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('renew.princing.by.provider') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'b473bf7f-3ef8-4b90-a37a-bce820efe5e8',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: pricingId })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.error === true) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro ao atualizar preço',
                            text: result.message || 'Erro inesperado.'
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'Preço atualizado com sucesso!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            $('#pricings-table').DataTable().ajax.reload(null, false);
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro de rede',
                        text: 'Não foi possível conectar ao servidor.'
                    });
                });
        });
    </script>
@endpush
