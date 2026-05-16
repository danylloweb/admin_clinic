<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impressão de Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .print-container {
            background: #fff;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .print-header {
            background: linear-gradient(135deg, #7b2d5f 0%, #b66a8e 100%);
            color: #fff;
            padding: 30px 40px;
            text-align: center;
        }

        .print-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .print-header p {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }

        .print-meta {
            background: #f8f9fa;
            padding: 15px 40px;
            border-bottom: 1px solid #dee2e6;
            font-size: 12px;
            color: #6c757d;
        }

        .print-meta strong {
            color: #212529;
        }

        .print-content {
            padding: 40px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0;
        }

        .print-table thead th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #495057;
        }

        .print-table tbody td {
            border: 1px solid #dee2e6;
            padding: 12px;
            font-size: 13px;
            color: #212529;
        }

        .print-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .print-table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .print-empty {
            text-align: center;
            padding: 60px 40px;
            color: #6c757d;
        }

        .print-empty p {
            font-size: 16px;
            margin: 0;
        }

        .print-footer {
            background: #f8f9fa;
            padding: 20px 40px;
            border-top: 1px solid #dee2e6;
            text-align: center;
            color: #6c757d;
            font-size: 11px;
        }

        .action-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-print {
            background: #0d6efd;
            color: #fff;
        }

        .btn-print:hover {
            background: #0b5ed7;
        }

        .btn-close-tab {
            background: #6c757d;
            color: #fff;
        }

        .btn-close-tab:hover {
            background: #5c636a;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .print-container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
            }

            .action-buttons {
                display: none !important;
            }

            .print-table thead th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-table tbody tr:nth-child(even) {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-header {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @page {
            margin: 10mm;
            size: A4;
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="print-header">
            <h1>Relatório de Agendamentos</h1>
            <p>Clínica de Estética Renovar</p>
        </div>

        <div class="print-meta">
            <strong>Data de emissão:</strong> {{ now()->format('d/m/Y H:i') }}
            @if($filterInfo)
                | <strong>Período:</strong> {{ $filterInfo }}
            @endif
        </div>

        <div class="print-content">
            @if($schedules->isEmpty())
                <div class="print-empty">
                    <p>Nenhum agendamento encontrado para os filtros aplicados.</p>
                </div>
            @else
                <div class="table-wrapper">
                    <table class="print-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Procedimento</th>
                                <th>Valor</th>
                                <th>Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr>
                                    <td>{{ $schedule->id }}</td>
                                    <td>{{ $schedule->patient->social_name ?? $schedule->patient->name ?? '-' }}</td>
                                    <td>{{ $schedule->procedure->name ?? '-' }}</td>
                                    <td>R$ {{ number_format($schedule->procedure->price ?? 0, 2, ',', '.') }}</td>
                                    <td>{{ $schedule->time ? substr($schedule->time, 0, 5) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="print-footer">
                    <p>Total de agendamentos: <strong>{{ $schedules->count() }}</strong></p>
                    <p style="margin-top: 10px;">Documento gerado automaticamente pelo sistema Renovar Estética</p>
                </div>
            @endif
        </div>
    </div>

    <div class="action-buttons">
        <button class="btn btn-print" onclick="window.print()">
            <span>🖨️ Imprimir</span>
        </button>
        <button class="btn btn-close-tab" onclick="window.close()">
            <span>✕ Fechar</span>
        </button>
    </div>
</body>
</html>

