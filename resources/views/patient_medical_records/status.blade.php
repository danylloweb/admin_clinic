<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prontuario Enviado</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #18322f;
            background:
                radial-gradient(circle at top right, rgba(212, 168, 92, 0.18), transparent 28%),
                linear-gradient(180deg, #fdfbf8 0%, #f7f4ee 100%);
        }

        .card {
            width: min(100%, 520px);
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 20px 50px rgba(15, 118, 110, 0.12);
            padding: 28px 24px;
            text-align: center;
        }

        .logo {
            width: 288px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 72px;
            min-height: 72px;
            border-radius: 22px;
            margin-bottom: 16px;
            font-size: 2rem;
            font-weight: 700;
        }

        h1 {
            margin: 0 0 10px;
            font-size: 1.8rem;
        }

        p {
            margin: 0;
            color: #6b7f7b;
            line-height: 1.65;
        }

        .link {
            display: inline-block;
            margin-top: 22px;
            padding: 14px 18px;
            border-radius: 16px;
            color: #fff;
            background: linear-gradient(135deg, #0f766e, #0a4f4a);
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
<img src="https://renovarestetica.com.br/Original-01.png" alt="Renovar" class="logo">
<div class="card">
    <div class="badge" style="background: {{ ($tone ?? 'success') === 'success' ? '#eaf7f1' : '#fff6e8' }}; color: {{ ($tone ?? 'success') === 'success' ? '#146a4c' : '#8a6117' }};">
        {{ ($tone ?? 'success') === 'success' ? '✓' : '!' }}
    </div>
    <h1>{{ $headline ?? 'Tudo certo!' }}</h1>
    <p>{{ $description ?? 'Seu registro foi concluído.' }}</p>
    <a href="https://renovarestetica.com.br/" class="link">Voltar para a Renovar</a>
</div>
</body>
</html>

