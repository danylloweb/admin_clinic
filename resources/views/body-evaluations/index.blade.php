@extends('layouts.header')
@section('content')
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <h3 class="card-title fs-5 mb-0">Fichas de Avaliacao Corporal</h3>
            @if($patient)
                <a href="{{ route('panel.body-evaluations.create', ['patientId' => $patient->id]) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nova Avaliacao
                </a>
            @endif
        </div>

        <div class="p-1 table-responsive">
            <table id="datatable-body-evaluations" class="table table-bordered table-striped align-middle" style="width: 100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data Criacao</th>
                        <th>Profissional</th>
                        <th>IMC</th>
                        <th>Peso</th>
                        <th>Gordura %</th>
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
    let bodyTable;

    $(function () {
        bodyTable = $('#datatable-body-evaluations').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [[0, 'desc']],
            ajax: function (data, callback) {
                const page = Math.floor(data.start / data.length) + 1;
                const limit = data.length === -1 ? 15 : data.length;
                const search = data.search.value;
                const columnMap = { 0: 'id', 1: 'created_at', 4: 'weight', 5: 'fat_percentage' };
                const orderBy = columnMap[data.order?.[0]?.column] || 'id';
                const sortedBy = data.order?.[0]?.dir || 'desc';

                $.ajax({
                    url: '{{ url('/panel-body-evaluations') }}',
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
                {
                    data: 'created_at',
                    render: function (value) {
                        if (!value) return '-';
                        const dt = new Date(value);
                        return Number.isNaN(dt.getTime()) ? value : dt.toLocaleString('pt-BR');
                    }
                },
                {
                    data: 'professional',
                    orderable: false,
                    render: function (value) {
                        return value?.name || '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        const weight = parseFloat(row.weight || 0);
                        const height = parseFloat(row.height || 0);
                        if (!weight || !height) return '<span class="text-muted">-</span>';
                        const bmi = weight / Math.pow(height / 100, 2);
                        return `<span class="badge bg-primary">${bmi.toFixed(1)}</span>`;
                    }
                },
                {
                    data: 'weight',
                    render: function (value) {
                        return value ? `${value} kg` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: 'fat_percentage',
                    render: function (value) {
                        return value ? `${value}%` : '<span class="text-muted">-</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        const showUrl = `{{ url('/panel-body-evaluations-show') }}/${row.id}`;
                        const editUrl = `{{ url('/panel-body-evaluations-edit') }}/${row.id}`;
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="${showUrl}" class="btn btn-info" title="Visualizar"><i class="fas fa-eye"></i></a>
                                <a href="${editUrl}" class="btn btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
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
