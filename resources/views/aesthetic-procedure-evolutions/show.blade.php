@extends('layouts.header')
@section('content')
@php
    $sessions = $attendance->evolution_sessions ?? [];
@endphp

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Visualizar Atendimento</h4>
                <small>ID #{{ $attendance->id }}</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('panel.attendances.edit', ['id' => $attendance->id]) }}" class="btn btn-light btn-sm">Editar</a>
                <a href="{{ route('panel.attendances.print', ['id' => $attendance->id]) }}" class="btn btn-light btn-sm" target="_blank">Imprimir</a>
                <a href="{{ route('panel.attendances.export-pdf', ['id' => $attendance->id]) }}" class="btn btn-light btn-sm">Exportar PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-4"><strong>Paciente:</strong> {{ $patient->social_name ?: $patient->name }}</div>
                <div class="col-md-4"><strong>Procedimento:</strong> {{ $attendance->procedure_name ?: '-' }}</div>
                <div class="col-md-4"><strong>Inicio:</strong> {{ optional($attendance->start_date)->format('d/m/Y') ?: '-' }}</div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Data</th>
                        <th>Procedimento</th>
                        <th>Equipamento</th>
                        <th>Parametros</th>
                        <th>Produtos</th>
                        <th>Reacao</th>
                        <th>Observacoes</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td>{{ $session['session_number'] ?? '-' }}</td>
                            <td>{{ $session['date'] ?? '-' }}</td>
                            <td>{{ $session['procedure_performed'] ?? '-' }}</td>
                            <td>{{ $session['equipment_used'] ?? '-' }}</td>
                            <td>{{ $session['parameters_used'] ?? '-' }}</td>
                            <td>{{ $session['products_used'] ?? '-' }}</td>
                            <td>{{ $session['patient_reaction'] ?? '-' }}</td>
                            <td>{{ $session['observations'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Sem sessao registrada</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">Foto antes</label>
                    @if($attendance->photo_before)
                        <img src="{{ $attendance->photo_before }}" class="img-fluid rounded border" alt="Antes">
                    @else
                        <div class="text-muted">Nao informada</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto depois</label>
                    @if($attendance->photo_after)
                        <img src="{{ $attendance->photo_after }}" class="img-fluid rounded border" alt="Depois">
                    @else
                        <div class="text-muted">Nao informada</div>
                    @endif
                </div>
            </div>

            <div class="mb-3"><strong>Resultado:</strong> {{ $attendance->result_evaluation ?: '-' }}</div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Assinatura do paciente</label>
                    @if($attendance->patient_signature)
                        <img src="{{ $attendance->patient_signature }}" class="img-fluid rounded border" alt="Assinatura paciente">
                    @else
                        <div class="text-muted">Nao assinada</div>
                    @endif
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assinatura do profissional</label>
                    @if($attendance->professional_signature)
                        <img src="{{ $attendance->professional_signature }}" class="img-fluid rounded border" alt="Assinatura profissional">
                    @else
                        <div class="text-muted">Nao assinada</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

