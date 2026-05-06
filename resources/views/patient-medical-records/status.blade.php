<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Status do prontuário' }}</title>
    <style>
        :root {
            --renovar-primary: #0f766e;
            --renovar-primary-dark: #0a4f4a;
            --renovar-accent: #d4a85c;
            --renovar-bg: #f7f4ee;
            --renovar-surface: #ffffff;
            --renovar-text: #18322f;
            --renovar-muted: #6b7f7b;
            --renovar-shadow: 0 20px 50px rgba(15, 118, 110, 0.12);
            --tone-success-bg: #eaf7f1;
            --tone-success-text: #146a4c;
            --tone-warning-bg: #fff6e8;
            --tone-warning-text: #8a6117;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 20px;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--renovar-text);
            background:
                radial-gradient(circle at top right, rgba(212, 168, 92, 0.18), transparent 28%),
                linear-gradient(180deg, #fdfbf8 0%, var(--renovar-bg) 100%);
        }

        .card {
            width: min(100%, 520px);
            background: var(--renovar-surface);
            border-radius: 28px;
            box-shadow: var(--renovar-shadow);
            padding: 28px 24px;
            text-align: center;
        }

        .logo {
            width: 88px;
            margin-bottom: 80px;
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

        .badge.success {
            background: var(--tone-success-bg);
            color: var(--tone-success-text);
        }

        .badge.warning {
            background: var(--tone-warning-bg);
            color: var(--tone-warning-text);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 1.8rem;
        }

        p {
            margin: 0;
            color: var(--renovar-muted);
            line-height: 1.65;
        }

        .link {
            display: inline-block;
            margin-top: 22px;
            padding: 14px 18px;
            border-radius: 16px;
            color: #fff;
            background: linear-gradient(135deg, var(--renovar-primary), var(--renovar-primary-dark));
            text-decoration: none;
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="card">
    <img src="{{ asset('img/logo reduzida.png') }}" alt="Renovar" class="logo mb-2">
    <div class="badge {{ ($tone ?? 'success') === 'success' ? 'success' : 'warning' }}">
        {{ ($tone ?? 'success') === 'success' ? '✓' : '!' }}
    </div>
    <h1>{{ $headline ?? 'Tudo certo!' }}</h1>
    <p>{{ $description ?? 'Seu registro foi concluído.' }}</p>
    <a href="https://renovarestetica.com.br/" class="link">Voltar para a Renovar</a>
</div>
</body>
</html>

