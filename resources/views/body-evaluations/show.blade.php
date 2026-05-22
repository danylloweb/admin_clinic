@extends('layouts.header')
@section('content')
@php
    $perimetry = $bodyEvaluation->perimetry ?? [];
    $objectives = $bodyEvaluation->objectives ?? [];
    $medicalHistory = $bodyEvaluation->medical_history ?? [];
    $bodyMapAreas = $bodyEvaluation->body_map_areas ?? [];
    $evolutionSessions = $bodyEvaluation->evolution_sessions ?? [];
    $treatmentPlanLines = $bodyEvaluation->treatment_plan ?? [];
@endphp

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0"><i class="fas fa-eye me-2"></i>Visualizar Avaliação Corporal</h4>
                        <small>ID: #{{ $bodyEvaluation->id }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('panel.body-evaluations.edit', ['id' => $bodyEvaluation->id]) }}" class="btn btn-light">
                            <i class="fas fa-pen me-1"></i>Editar
                        </a>
                        <a href="{{ route('panel.patient.show', ['id' => $patient->id]) }}" class="btn btn-light">
                            <i class="fas fa-times me-1"></i>Fechar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-4"><strong>Paciente:</strong> {{ $patient->name }}</div>
                        <div class="col-md-4"><strong>Profissional:</strong> {{ $bodyEvaluation->professional->name ?? 'Não informado' }}</div>
                        <div class="col-md-4"><strong>Data:</strong> {{ optional($bodyEvaluation->created_at)->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3"><strong>Peso:</strong> {{ $bodyEvaluation->weight ?? '-' }}</div>
                        <div class="col-md-3"><strong>Altura:</strong> {{ $bodyEvaluation->height ?? '-' }}</div>
                        <div class="col-md-3"><strong>% Gordura:</strong> {{ $bodyEvaluation->fat_percentage ?? '-' }}</div>
                        <div class="col-md-3"><strong>Massa Muscular:</strong> {{ $bodyEvaluation->muscle_mass ?? '-' }}</div>
                    </div>

                    @if(!empty($objectives))
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Objetivos</h5>
                            @foreach($objectives as $item)
                                <span class="badge bg-primary me-1 mb-1">{{ $item }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($perimetry))
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Perimetria</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm mb-0">
                                    <tbody>
                                    @foreach($perimetry as $key => $value)
                                        <tr>
                                            <th style="width: 220px;">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(!empty($medicalHistory))
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Histórico médico</h5>
                            @foreach($medicalHistory as $item)
                                <span class="badge bg-info me-1 mb-1">{{ $item }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if($bodyEvaluation->previous_procedures)
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Procedimentos anteriores</h5>
                            <p class="mb-0">{{ $bodyEvaluation->previous_procedures }}</p>
                        </div>
                    @endif

                    @if($bodyEvaluation->photo_front || $bodyEvaluation->photo_profile_right || $bodyEvaluation->photo_profile_left)
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Fotos</h5>
                            <div class="row g-3">
                                @foreach([
                                    'Frente' => $bodyEvaluation->photo_front,
                                    'Perfil direito' => $bodyEvaluation->photo_profile_right,
                                    'Perfil esquerdo' => $bodyEvaluation->photo_profile_left,
                                ] as $label => $photo)
                                    @if($photo)
                                        <div class="col-md-4">
                                            <div class="card h-100">
                                                <div class="card-body text-center">
                                                    <div class="small text-muted mb-2">{{ $label }}</div>
                                                    <img src="{{ $photo }}" alt="{{ $label }}" class="img-fluid rounded" style="max-height: 260px; object-fit: cover;">
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if(!empty($bodyMapAreas))
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Mapa corporal</h5>
                            @foreach($bodyMapAreas as $item)
                                <span class="badge bg-warning text-dark me-1 mb-1">{{ $item }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($evolutionSessions))
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Evoluções</h5>
                            @foreach($evolutionSessions as $item)
                                <span class="badge bg-secondary me-1 mb-1">{{ is_array($item) ? json_encode($item) : $item }}</span>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($treatmentPlanLines))
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2">Plano de tratamento</h5>
                            @foreach($treatmentPlanLines as $item)
                                <div class="border rounded p-2 mb-2">{{ is_array($item) ? json_encode($item) : $item }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

