<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relatório de Itens do Pedido</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: left; }
        h2 { margin-bottom: 10px; }
    </style>
</head>
<body onload="window.print()">
<h2>Relatório de Itens de Serviço | {{ count($items['data']) }} - items</h2>
<p><strong>Total:</strong> R$ {{ number_format($total, 2, ',', '.') }}</p>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Pedido Externo</th>
        <th>Nome</th>
        <th>Descrição Serviço</th>
        <th>Descrição Produto</th>
        <th>Preço</th>
        <th>Status</th>
        <th>Atualizado em</th>
    </tr>
    </thead>
    <tbody>
    @foreach($items['data'] as $item)
        <tr>
            <td>{{ $item['id'] }}</td>
            <td>{{ $item['external_order_id'] }}</td>
            <td>{{ $item['customer_name'] }}</td>
            <td>{{ $item['ref_description'] }}</td>
            <td>{{ $item['ref_parent_description'] }}</td>
            <td>R${{ number_format($item['price'], 2, ',', '.') }}</td>
            <td>{{ $item['status_title'] }}</td>
            <td>{{ \Carbon\Carbon::parse($item['updated_at'])->format('d/m/Y H:i') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
