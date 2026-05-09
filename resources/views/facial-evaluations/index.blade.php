@extends('layouts.header')
@section('content')

    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                <h3 class="card-title fs-5 mb-0">Fichas de Avaliação Facial</h3>
                @if($patient)
                    <a href="{{ route('panel.facial-evaluations.create', ['patientId' => $patient->id]) }}" class="btn btn-success btn-lg">
                        <i class="fas fa-plus"></i>Nova Ficha
                    </a>
                @endif
            </div>

            <div class="p-1 table-responsive">
                <table id="datatable-facial-evaluations" class="table table-bordered table-striped align-middle" style="width: 100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data Criação</th>
                        <th>Tipo de Pele</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@push('scripts')
<script>
    const patientId = {{ (int)($patient->id ?? 0) }};
    let facialTable;

    $(function () {
        facialTable = $('#datatable-facial-evaluations').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            order: [[0, 'desc']],
            ajax: function (data, callback) {
                const page = Math.floor(data.start / data.length) + 1;
                const limit = data.length === -1 ? 15 : data.length;
                const search = data.search.value;
                const columnMap = { 0: 'id', 1: 'created_at', 2: 'skin_type' };
                const orderBy = columnMap[data.order?.[0]?.column] || 'id';
                const sortedBy = data.order?.[0]?.dir || 'desc';

                $.ajax({
                    url: '{{ url('/panel-facial-evaluations') }}',
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
                { data: 'id', render: data => `<strong>#${data}</strong>` },
                {
                    data: 'created_at',
                    render: function (value) {
                        if (!value) return '-';
                        const date = new Date(value);
                        if (Number.isNaN(date.getTime())) return value;
                        return date.toLocaleString('pt-BR');
                    }
                },
                {
                    data: 'skin_type',
                    render: function (value) {
                        if (!value) return '<span class="text-muted">-</span>';
                        const label = String(value).charAt(0).toUpperCase() + String(value).slice(1);
                        return `<span class="badge bg-info">${label}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        const signed = !!row.signed_at || !!row.consent_accepted;
                        return signed
                            ? '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Assinado</span>'
                            : '<span class="badge bg-warning"><i class="fas fa-hourglass-half me-1"></i>Pendente</span>';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        const showUrl = `{{ url('/panel-facial-evaluations-show') }}/${row.id}`;
                        const editUrl = `{{ url('/panel-facial-evaluations-edit') }}/${row.id}`;
                        const canSend = !(row.signed_at || row.consent_accepted);
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="${showUrl}" class="btn btn-info" title="Visualizar">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="${editUrl}" class="btn btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                ${canSend ? `<button type="button" class="btn btn-primary" onclick="sendSignatureLink(${row.id})" title="Enviar Link"><i class="fab fa-whatsapp"></i></button>` : ''}
                                <button type="button" class="btn btn-danger" onclick="deleteEvaluation(${row.id})" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json' }
        });
    });

    function sendSignatureLink(id) {
        fetch(`/panel-facial-evaluations-send-link/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.error) {
                if (data.whatsapp_url) {
                    window.open(data.whatsapp_url, '_blank');
                    showToast('Link aberto no WhatsApp!', 'success');
                } else {
                    showToast('Link enviado com sucesso.', 'success');
                }
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(err => showToast('Erro: ' + err.message, 'danger'));
    }

    function deleteEvaluation(id) {
        if (confirm('Deseja deletar esta ficha de avaliação?')) {
            fetch(`/panel-facial-evaluations-delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    showToast(data.message || 'Erro ao deletar', 'danger');
                } else {
                    showToast('Ficha deletada com sucesso!', 'success');
                    if (facialTable) {
                        facialTable.ajax.reload(null, false);
                    }
                }
            })
            .catch(err => showToast('Erro: ' + err.message, 'danger'));
        }
    }
</script>
@endpush
@endsection

