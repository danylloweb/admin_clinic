<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            background: #ffffff;
        }
        .page {
            padding: 28px 32px;
        }
        .header {
            background: linear-gradient(135deg, #7b2d5f 0%, #b66a8e 100%);
            color: #ffffff;
            border-radius: 18px;
            padding: 22px 24px;
            margin-bottom: 24px;
        }
        .header-table,
        .info-table,
        .totals-table,
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-cell {
            width: 180px;
            vertical-align: top;
        }
        .logo-box {
            display: inline-block;
            padding: 10px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.14);
        }
        .logo-box img {
            width: 118px;
            height: auto;
            display: block;
        }
        .brand-fallback {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: .4px;
        }
        .doc-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 6px;
        }
        .doc-subtitle {
            margin: 0;
            font-size: 13px;
            opacity: .92;
        }
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #7b2d5f;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: .7px;
        }
        .panel {
            border: 1px solid #ead7e0;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 18px;
            background: #fffafb;
        }
        .panel-left,
        .panel-right {
            width: 48%;
            vertical-align: top;
        }
        .panel-spacer {
            width: 4%;
        }
        .label {
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .value {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            padding-top: 3px;
            padding-bottom: 10px;
        }
        .items-table thead th {
            background: #7b2d5f;
            color: #ffffff;
            padding: 11px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            text-align: left;
        }
        .items-table thead th.text-right,
        .items-table tbody td.text-right {
            text-align: right;
        }
        .items-table tbody td {
            border-bottom: 1px solid #ead7e0;
            padding: 10px;
            background: #ffffff;
        }
        .items-table tbody tr:nth-child(even) td {
            background: #fcf5f8;
        }
        .totals-wrap {
            margin-top: 18px;
        }
        .totals-box {
            width: 290px;
            margin-left: auto;
            border: 1px solid #ead7e0;
            border-radius: 16px;
            overflow: hidden;
            background: #ffffff;
        }
        .totals-box th,
        .totals-box td {
            padding: 10px 14px;
            border-bottom: 1px solid #f0e3e9;
        }
        .totals-box th {
            color: #6b7280;
            font-weight: 600;
            text-align: left;
            width: 58%;
        }
        .totals-box td {
            text-align: right;
            font-weight: 700;
            color: #111827;
        }
        .totals-box tr.highlight th,
        .totals-box tr.highlight td {
            background: #7b2d5f;
            color: #ffffff;
            border-bottom: none;
            font-size: 13px;
        }
        .notes {
            margin-top: 24px;
            padding: 16px 18px;
            border-radius: 16px;
            background: #f7f0f3;
            border: 1px solid #ead7e0;
        }
        .notes p {
            margin: 0;
            color: #4b5563;
            line-height: 1.5;
            font-size: 11px;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: #9ca3af;
            font-size: 10px;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell">
                    <div class="logo-box">
                            <img src="{{ $logoDataUri }}"  width="200px" alt="Renovar Estetica">
                    </div>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <h1 class="doc-title">Pedido de Venda</h1>
                    <p class="doc-subtitle">Documento comercial gerado para acompanhamento do paciente</p>
                    <p class="doc-subtitle" style="margin-top: 8px;">Emissão: {{ $date }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table" style="margin-bottom: 18px;">
        <tr>
            <td class="panel-left">
                <div class="panel">
                    <div class="section-title">Dados do paciente</div>
                    <div class="label">Cliente</div>
                    <div class="value">{{ $socialName }}</div>

                    <div class="label">Nome</div>
                    <div class="value">{{ $patientName }}</div>

                    <div class="label">Telefone</div>
                    <div class="value">{{ $phone }}</div>
                </div>
            </td>
            <td class="panel-spacer"></td>
            <td class="panel-right">
                <div class="panel">
                    <div class="section-title">Condições de pagamento</div>
                    <div class="label">Forma de pagamento</div>
                    <div class="value">{{ $paymentLabel }}</div>

                    <div class="label">Bandeira</div>
                    <div class="value">{{ $brandLabel }}</div>

                    <div class="label">Parcelamento</div>
                    <div class="value">{{ $qtyInstallments }}x</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
        <tr>
            <th>Procedimento</th>
            <th class="text-right" style="width: 70px;">Qtd</th>
            <th class="text-right" style="width: 120px;">Valor unitário</th>
            <th class="text-right" style="width: 130px;">Total</th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $item)
            @php
                $price = (float)($item['price'] ?? 0);
                $qty = (int)($item['qty'] ?? 0);
            @endphp
            <tr>
                <td>{{ $item['name'] ?? '-' }}</td>
                <td class="text-right">{{ $qty }}</td>
                <td class="text-right">R${{ number_format($price, 2, ',', '.') }}</td>
                <td class="text-right">R${{ number_format($price * $qty, 2, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center; color: #6b7280;">Nenhum item informado</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals-table">
            <tr>
                <td></td>
                <td style="width: 300px; vertical-align: top;">
                    <table class="totals-box">
                        <tr>
                            <th>Subtotal</th>
                            <td> R${{ number_format($subtotal, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Valor em PIX</th>
                            <td> R${{ number_format($pixAmount, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Valor em Débito</th>
                            <td> R${{ number_format($debitAmount, 2, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Total no Crédito</th>
                            <td> R${{ number_format($creditTotal, 2, ',', '.') }}</td>
                        </tr>
                        <tr class="highlight">
                            <th>Valor da Parcela</th>
                            <td> R${{ number_format($installmentAmount, 2, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="notes">
        <div class="section-title" style="margin-bottom: 8px;">Observações</div>
        <p>
            Este documento possui validade de 24 horas a partir da data de emissão. Após esse período,
            os valores, formas de pagamento, taxas e demais condições comerciais podem ser alterados sem aviso prévio.
        </p>
    </div>

</div>
</body>
</html>

