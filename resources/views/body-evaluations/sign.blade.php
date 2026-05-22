<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Ficha de Avaliação Corporal | Renovar Estética</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 24px;
        }
        .container-sign {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 720px;
            width: 100%;
            padding: 40px;
        }
        .header-sign { text-align: center; margin-bottom: 30px; }
        .header-sign img { max-width: 150px; margin-bottom: 20px; }
        .info-section {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .signature-canvas {
            border: 2px solid #ddd;
            border-radius: 5px;
            cursor: crosshair;
            background: white;
            width: 100%;
            height: 150px;
        }
        .clear-btn {
            background: #6c757d;
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .clear-btn:hover { background: #5a6268; color: white; }
        .btn-sign {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container-sign">
        <div class="header-sign">
            <img src="https://renovarestetica.com.br/Original-01.png" alt="Renovar Estética">
            <h1>Confirmação de Avaliação</h1>
            <p>Ficha de Avaliação Corporal - Paciente: <strong>{{ $bodyEvaluation->patient->name }}</strong></p>
        </div>

        <div class="info-section">
            <h5><i class="fas fa-info-circle"></i> Informações da Ficha</h5>
            <div class="row mt-3">
                <div class="col-6">
                    <p class="mb-1"><strong>Paciente:</strong></p>
                    <p class="text-muted">{{ $bodyEvaluation->patient->name }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-1"><strong>Profissional:</strong></p>
                    <p class="text-muted">{{ $bodyEvaluation->professional?->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-4"><p class="mb-1"><strong>Peso:</strong></p><p class="text-muted">{{ $bodyEvaluation->weight ?? 'Não informado' }}</p></div>
                <div class="col-4"><p class="mb-1"><strong>Altura:</strong></p><p class="text-muted">{{ $bodyEvaluation->height ?? 'Não informado' }}</p></div>
                <div class="col-4"><p class="mb-1"><strong>Consentimento:</strong></p><p class="text-muted">{{ $bodyEvaluation->consent_accepted ? 'Aceito' : 'Não' }}</p></div>
            </div>
        </div>

        <form id="signatureForm">
            @csrf
            <div class="form-group mb-3">
                <label class="form-label"><strong>Sua Assinatura Digital</strong></label>
                <p class="text-muted mb-2">Assine com o mouse ou toque</p>
                <button type="button" class="clear-btn" id="clearCanvas"><i class="fas fa-redo"></i> Limpar</button>
                <canvas id="signatureCanvas" class="signature-canvas"></canvas>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="consentAccepted" required>
                <label class="form-check-label" for="consentAccepted">
                    Li e aceito os termos da ficha de avaliação corporal
                </label>
            </div>

            <button type="submit" class="btn-sign"><i class="fas fa-pen-fancy me-2"></i>Confirmar Assinatura</button>
        </form>
    </div>
</body>
</html>

