@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            No Quotations
            <small>Registros sem cotação</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-dashboard"></i> Início</a></li>
            <li class="active">No Quotations</li>
        </ol>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-body">
                <div class="form-group">
                    <label for="serviceId"
                         title="Essa função vai atualizar o cache-memory de cotação"
                    >Selecione o Serviço:</label>
                    <select id="serviceId" class="form-control"
                            style="width: 190px; display: inline-block; margin-bottom: 10px;">
                        <option value="">Todos</option>
                        @foreach($services as $service)
                            <option value="{{ $service->value }}">{{ $service->getTranslation() }}</option>
                        @endforeach
                    </select>
                    <button id="refreshPricingBtn" class="btn btn-warning" style="margin-left: 10px;">
                        <i class="fa fa-sync"></i> Atualizar Precificação
                    </button>
                    <label for="processNoQuotationBtn"
                          title="Essa função atualiza os preços de SKU sem cotação com base no preço de SKU com cotação"
                           style="margin-left: 30px">Esse botão atualiza o preços de SKU:</label>
                    <button id="processNoQuotationBtn"
                          title="Essa função atualiza os preços de SKU sem cotação com base no preço de SKU com cotação"
                          class="btn btn-primary" style="margin-left: 10px;">
                        <i class="fa fa-sync"></i> Processar registros
                    </button>
                </div>

                <table id="no-quotations-table" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ref ID</th>
                        <th>Ref Parent ID</th>
                        <th>Lido</th>
                        <th>Criado em</th>
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
            const table = $('#no-quotations-table').DataTable({
                processing: true,
                serverSide: true,
                searching: true,
                ajax: function(data, callback, settings) {
                    const page = Math.floor(data.start / data.length) + 1;
                    const search = data.search.value;

                    $.ajax({
                        url: '{{ route('no-quotations.index') }}',
                        method: 'GET',
                        data: {
                            limit: data.length,
                            orderBy: 'created_at',
                            sortedBy: 'desc',
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
                    { data: '_id' },
                    { data: 'ref_id' },
                    { data: 'ref_parent_id' },
                    {
                        data: 'readed',
                        render: function(data) {
                            return data ? 'Sim' : 'Não';
                        }
                    },
                    {
                        data: 'created_at',
                        render: function(data) {
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    }
                ]
            });
        });
        $(function () {
            $('#refreshPricingBtn').on('click', function () {
                const serviceId = $('#serviceId').val();

                if (!serviceId) {
                    return Swal.fire('Atenção', 'Selecione um serviço antes de atualizar.', 'warning');
                }
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Deseja Renovar cache de Cotação?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, processar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('refresh-memory-pricing') }}',
                            method: 'POST',
                            headers: {
                                'Authorization': 'b473bf7f-3ef8-4b90-a37a-bce820efe5e8',
                            },
                            data: {
                                service_id: serviceId
                            },
                            beforeSend() {
                                $('#refreshPricingBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Atualizando...');
                            },
                            success(response) {
                                if (response.error) {
                                    Swal.fire('Erro!', 'Houve um problema ao renovar cache.', 'error');
                                    $('#refreshPricingBtn').prop('disabled', false).html('<i class="fa fa-sync"></i> Atualizar Precificação')
                                    return false;
                                }
                                Swal.fire('Sucesso!', 'Registros processados com sucesso.', 'success');
                                return false;
                            },
                            error(xhr) {
                                return false;
                            },
                            complete() {
                                $('#refreshPricingBtn').prop('disabled', false).html('<i class="fa fa-sync"></i> Atualizar Precificação');
                                return false;
                            }
                        });
                    }
                });
            });
        });

        $(function () {
            $('#processNoQuotationBtn').on('click', function () {
                const btn = $(this);

                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Deseja processar os registros sem cotação?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, processar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('no-quotations.process') }}',
                            method: 'POST',
                            headers: {
                                'Authorization': 'b473bf7f-3ef8-4b90-a37a-bce820efe5e8',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            beforeSend() {
                                btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processando...');
                            },
                            success(response) {
                                Swal.fire('Sucesso!', 'Registros processados com sucesso.', 'success');
                                $('#no-quotations-table').DataTable().ajax.reload();
                            },
                            error(xhr) {
                                Swal.fire('Erro!', 'Houve um problema ao processar os registros.', 'error');
                            },
                            complete() {
                                btn.prop('disabled', false).html('<i class="fa fa-sync"></i> Processar Sem Cotação');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
