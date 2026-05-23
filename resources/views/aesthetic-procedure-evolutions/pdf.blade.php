@php
    $sessions = $attendance->evolution_sessions ?? [];
    $lastSession = !empty($sessions) ? end($sessions) : null;
    $lastSessionDate = $lastSession['date'] ?? null;
    $lastSessionTime = $lastSession['time'] ?? null;
    $lastSessionStamp = $lastSessionDate
        ? trim($lastSessionDate . ' ' . ($lastSessionTime ?? ''))
        : 'Sem sessao registrada';
@endphp
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Atendimento #{{ $attendance->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 8px; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px; vertical-align: top; }
        .grid { width: 100%; margin-top: 12px; }
        .grid td { border: 0; width: 50%; }
        .signature { border: 1px solid #ddd; height: 90px; text-align: center; }
        .signature img { max-height: 85px; max-width: 100%; }
        .photo img { max-width: 100%; max-height: 220px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
@if(!empty($isPrint))
    <button class="no-print" onclick="window.print()">Imprimir</button>
@endif

<h1>Atendimento</h1>
<div><strong>ID:</strong> #{{ $attendance->id }}</div>
<div><strong>Paciente:</strong> {{ $patient->social_name ?: $patient->name }}</div>
<div><strong>Profissional:</strong> {{ $attendance->professional->name ?? '-' }}</div>
<div><strong>Procedimento:</strong> {{ $attendance->procedure_name ?: '-' }}</div>
<div><strong>Inicio:</strong> {{ optional($attendance->start_date)->format('d/m/Y') ?: '-' }}</div>
<div><strong>Resultado:</strong> {{ $attendance->result_evaluation ?: '-' }}</div>
<div><strong>Ultima sessao:</strong> {{ $lastSessionStamp }}</div>
<div><strong>Assinado em:</strong> {{ optional($attendance->signed_at)->format('d/m/Y H:i') ?: '-' }}</div>

<table>
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
            <td colspan="8" class="muted">Sem sessao registrada</td>
        </tr>
    @endforelse
    </tbody>
</table>

<table class="grid">
    <tr>
        <td class="photo">
            <div><strong>Foto antes</strong></div>
            @if($attendance->photo_before)
                <img src="{{ $attendance->photo_before }}" alt="Foto antes">
            @else
                <div class="muted">Nao informada</div>
            @endif
        </td>
        <td class="photo">
            <div><strong>Foto depois</strong></div>
            @if($attendance->photo_after)
                <img src="{{ $attendance->photo_after }}" alt="Foto depois">
            @else
                <div class="muted">Nao informada</div>
            @endif
        </td>
    </tr>
</table>

<table class="grid">
    <tr>
        <td>
            <div><strong>Assinatura paciente</strong></div>
            <div class="signature">
                @if($attendance->patient_signature)
                    <img src="{{ $attendance->patient_signature }}" alt="Assinatura paciente">
                @endif
            </div>
        </td>
        <td>
            <div><strong>Assinatura profissional</strong></div>
            <div class="signature">
                @if($attendance->professional_signature)
                    <img src="{{ $attendance->professional_signature }}" alt="Assinatura profissional">
                @endif
            </div>
        </td>
    </tr>
</table>
</body>
</html>

