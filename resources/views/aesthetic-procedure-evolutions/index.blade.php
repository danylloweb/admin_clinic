@extends('layouts.header')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <h3 class="card-title fs-5 mb-0">Atendimentos</h3>
            @if($patient)
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <a href="{{ route('panel.patient.show', ['id' => $patient->id]) }}" class="btn btn-secondary btn-lg">Voltar</a>
                </div>
            @endif
        </div>

        <div class="p-1 table-responsive">
            <table id="datatable-attendances" class="table table-bordered table-striped align-middle" style="width: 100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Agendamento</th>
                    <th>Procedimento</th>
                    <th>Inicio</th>
                    <th>Resultado</th>
                    <th>Acoes</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const patientId = {{ (int)($patient->id ?? 0) }};

    $(function () {
        $('#datatable-attendances').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [[0, 'desc']],
            ajax: function (data, callback) {
                const page = Math.floor(data.start / data.length) + 1;
                const limit = data.length === -1 ? 15 : data.length;
                const search = data.search.value;
                const columnMap = { 0: 'id', 1: 'schedule_id', 3: 'start_date' };
                const orderBy = columnMap[data.order?.[0]?.column] || 'id';
                const sortedBy = data.order?.[0]?.dir || 'desc';

                $.ajax({
                    url: '{{ url('/panel-attendances') }}',
                    method: 'GET',
                    data: { patient_id: patientId, limit, page, search, orderBy, sortedBy },
                    success: function (response) {
                        callback({
                            recordsTotal: response.meta?.pagination?.total || 0,
                            recordsFiltered: response.meta?.pagination?.total || 0,
                            data: response.data || []
                        });
                    },
                    error: function () {
                        callback({ recordsTotal: 0, recordsFiltered: 0, data: [] });
                    }
                });
            },
            columns: [
                { data: 'id', render: (data) => `<strong>#${data}</strong>` },
                { data: 'schedule_id', render: (v) => v ? `#${v}` : '-' },
                { data: 'procedure_name', render: (v) => v || '-' },
                { data: 'start_date', render: (v) => v || '-' },
                { data: 'result_evaluation', render: (v) => v || '-' },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        const editUrl = `{{ url('/panel-attendances-edit') }}/${row.id}`;
                        const showUrl = `{{ url('/panel-attendances-show') }}/${row.id}`;
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="${editUrl}" class="btn btn-primary" title="Editar">Abrir</a>
                                <a href="${showUrl}" class="btn btn-outline-secondary" title="Visualizar">Ver</a>
                            </div>
                        `;
                    }
                }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
        });
    });
</script>
@endpush
@endsection

