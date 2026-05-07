@extends('layouts.header')

@section('content')
<div class="container py-4">
    <div class="row g-3">
        <div class="col-12">
            <div class="card p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                    <div>
                        <h4 class="mb-1">Prontuario do paciente</h4>
                        <p class="mb-0 text-muted">Visualizacao completa das informacoes enviadas pelo paciente.</p>
                    </div>
                    <a href="{{ route('panel.patient.show', ['id' => $patient->id]) }}" class="btn btn-outline-secondary">
                        Voltar para paciente
                    </a>
                </div>

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100">
                            <small class="text-muted d-block">Paciente</small>
                            <strong>{{ $patient->social_name ?: $patient->name }}</strong>
                            <small class="text-muted d-block mt-2">Telefone</small>
                            <strong>{{ $patient->phone ?: '-' }}</strong>
                            <small class="text-muted d-block mt-2">Status do prontuario</small>
                            @if(!$record)
                                <span class="badge bg-secondary">Nao preenchido</span>
                            @elseif($record->submitted_at)
                                <span class="badge bg-success">Preenchido</span>
                                <small class="text-muted d-block mt-1">Em {{ \Carbon\Carbon::parse($record->submitted_at)->format('d/m/Y H:i') }}</small>
                            @else
                                <span class="badge bg-warning text-dark">Pendente</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="border rounded p-3 h-100">
                            <h6 class="mb-3">Contato de apoio e assinatura</h6>
                            <div class="row g-2">
                                <div class="col-md-6"><small class="text-muted">Contato de emergencia</small><div>{{ $record?->emergency_contact_name ?: '-' }}</div></div>
                                <div class="col-md-6"><small class="text-muted">Telefone de emergencia</small><div>{{ $record?->emergency_contact_phone ?: '-' }}</div></div>
                                <div class="col-md-6"><small class="text-muted">Assinatura</small><div>{{ $record?->signature_name ?: '-' }}</div></div>
                                <div class="col-md-6"><small class="text-muted">Consentimento LGPD</small><div>{{ $record?->lgpd_consent ? 'Sim' : 'Nao' }}</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-4">
                <h6 class="mb-3">Objetivo e observacoes</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted">Objetivo do tratamento</small>
                        <div class="border rounded p-2 mt-1">{{ $record?->treatment_goals ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Observacoes</small>
                        <div class="border rounded p-2 mt-1">{{ $record?->observation ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-4">
                <h6 class="mb-3">Habitos e rotina</h6>
                <div class="row g-3">
                    <div class="col-md-3"><small class="text-muted">Alimentacao</small><div>{{ $record?->type_of_food ?: '-' }}</div></div>
                    <div class="col-md-3"><small class="text-muted">Atividade fisica</small><div>{{ $record?->practice_physical_activity ?: '-' }}</div></div>
                    <div class="col-md-3"><small class="text-muted">Consumo de alcool</small><div>{{ $record?->consume_alcohol ?: '-' }}</div></div>
                    <div class="col-md-3"><small class="text-muted">Fuma</small><div>{{ $record?->smoke ?: '-' }}</div></div>
                    <div class="col-md-3"><small class="text-muted">Litros de agua/dia</small><div>{{ $record?->liters_of_water_per_day ?? '-' }}</div></div>
                    <div class="col-md-3"><small class="text-muted">Filhos</small><div>{{ $record?->children ?: '-' }}</div></div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card p-4">
                <h6 class="mb-3">Saude e historico clinico</h6>
                <div class="row g-3">
                    <div class="col-md-4"><small class="text-muted">Tipo sanguineo</small><div>{{ $record?->blood_type ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Marcapasso</small><div>{{ $record?->pacemaker ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Protese/metais</small><div>{{ $record?->metal_prosthesis ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Diabetes</small><div>{{ $record?->diabetes ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Historico oncologico</small><div>{{ $record?->oncology ?: '-' }}</div></div>
                    <div class="col-md-4"><small class="text-muted">Hipertensao arterial</small><div>{{ $record?->arterial_hypertension ?: '-' }}</div></div>
                    <div class="col-md-6"><small class="text-muted">Medicacoes</small><div class="border rounded p-2 mt-1">{{ $record?->use_medication ?: '-' }}</div></div>
                    <div class="col-md-6"><small class="text-muted">Alergias</small><div class="border rounded p-2 mt-1">{{ $record?->have_allergies ?: '-' }}</div></div>
                    <div class="col-md-12"><small class="text-muted">Hormonos/anabolizantes</small><div class="border rounded p-2 mt-1">{{ $record?->use_anabolic_hormones ?: '-' }}</div></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

