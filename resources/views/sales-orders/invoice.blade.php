<!doctype html>
<html lang="pt-BR">
<head>
    <link rel="stylesheet" href="{{ asset('accordion.8001c1c2.css') }}">
    <link rel="stylesheet" href="{{ asset('app.css') }}">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle }}</title>
    <style>
        .invoice-wrap { max-width: 900px; margin: 24px auto; }
        .invoice-head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid #d1d5db; padding-bottom: 12px; margin-bottom: 16px; }
        .invoice-title { font-size: 22px; font-weight: 700; margin: 0; }
        .invoice-muted { color: #6b7280; font-size: 13px; }
        .invoice-table th, .invoice-table td { padding: 10px 8px; border-bottom: 1px solid #e5e7eb; }
        .invoice-table th { background: #f9fafb; }
        .invoice-totals { margin-top: 18px; max-width: 360px; margin-left: auto; }
        .invoice-totals-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px dashed #e5e7eb; }
        @media print {
            .no-print { display: none !important; }
            .invoice-wrap { margin: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="invoice-wrap">
    <div class="no-print mb-3 text-end">
        <button type="button" class="btn btn-secondary me-2" onclick="window.close()">Fechar</button>
        <button type="button" class="btn btn-primary" onclick="window.print()">Imprimir</button>
    </div>

    <div class="card">
        <div class="card-body p-8 print-content">
            <div class="mb-6">
                <img src="https://renovarestetica.com.br/Invertida-03.png" width="190" alt="Renovar">
            </div>

            <div class="invoice-head">
                <div>
                    <h1 class="invoice-title">Pedido de Venda</h1>
                    <div class="invoice-muted">Cliente: {{ $socialName }}</div>
                    <div class="invoice-muted">Nome: {{ $patientName }}</div>
                    <div class="invoice-muted">Telefone: {{ $phone }}</div>
                </div>
                <div class="text-end">
                    <div class="invoice-muted">Data: {{ $date }}</div>
                    <div class="invoice-muted">Pagamento: {{ $paymentLabel }}</div>
                    <div class="invoice-muted">Cartao: {{ $brandLabel }}</div>
                    <div class="invoice-muted">Parcelas: {{ $qtyInstallments }}x</div>
                </div>
            </div>

            <table class="table invoice-table mb-10">
                <thead>
                <tr>
                    <th>Procedimento</th>
                    <th class="text-end">Qtd</th>
                    <th class="text-end">Valor unitário</th>
                    <th class="text-end">Total</th>
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
                        <td class="text-end">{{ $qty }}</td>
                        <td class="text-end">R$ {{ number_format($price, 2, ',', '.') }}</td>
                        <td class="text-end">R$ {{ number_format($price * $qty, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Nenhum item informado</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            <div class="invoice-totals">
{{--                <div class="invoice-totals-row"><span>Subtotal</span><span>R$ {{ number_format($subtotal, 2, ',', '.') }}</span></div>--}}
{{--                <div class="invoice-totals-row"><span>Valor em PIX</span><span>R$ {{ number_format($pixAmount, 2, ',', '.') }}</span></div>--}}
                <div class="invoice-totals-row"><span>Valor em Debito</span><span>R$ {{ number_format($debitAmount, 2, ',', '.') }}</span></div>
                <div class="invoice-totals-row"><span>Total</span><span>R$ {{ number_format($creditTotal, 2, ',', '.') }}</span></div>
                <div class="invoice-totals-row"><strong>Parcela</strong><strong>R$ {{ number_format($installmentAmount, 2, ',', '.') }}</strong></div>
            </div>

            <div class="mt-10">
                <div class="mb-2 text-body-emphasis">Termos e Condicoes</div>
                <div class="invoice-muted" style="font-size:14px;">
                    Este documento possui validade de 24 horas a partir da data de emissao. Apos esse periodo,
                    os valores, formas de pagamento, taxas e demais condicoes comerciais podem ser alterados sem aviso previo.
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

