<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Ficha de Avaliação Facial | Renovar Estética</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container-sign {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        .header-sign {
            text-align: center;
            margin-bottom: 30px;
        }
        .header-sign img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .header-sign h1 {
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header-sign p {
            color: #666;
            font-size: 14px;
        }
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
        .consent-text {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #856404;
        }
        .btn-sign {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            font-weight: bold;
            width: 100%;
            margin-top: 20px;
            transition: transform 0.2s;
        }
        .btn-sign:hover {
            transform: translateY(-2px);
            color: white;
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
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
        .clear-btn:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-sign">
        <div class="header-sign">
            <img src="https://renovarestetica.com.br/Original-01.png" alt="Renovar Estética">
            <h1>Confirmação de Avaliação</h1>
            <p>Ficha de Avaliação Facial - Paciente: <strong>{{ $facialEvaluation->patient->name }}</strong></p>
        </div>

        <div class="info-section">
            <h5><i class="fas fa-info-circle"></i> Informações da Ficha</h5>
            <div class="row mt-3">
                <div class="col-6">
                    <p class="mb-1"><strong>Paciente:</strong></p>
                    <p class="text-muted">{{ $facialEvaluation->patient->name }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-1"><strong>Profissional:</strong></p>
                    <p class="text-muted">{{ $facialEvaluation->professional?->name ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-6">
                    <p class="mb-1"><strong>Tipo de Pele:</strong></p>
                    <p class="text-muted">{{ $facialEvaluation->skin_type ?? 'Não informado' }}</p>
                </div>
                <div class="col-6">
                    <p class="mb-1"><strong>Fototipo:</strong></p>
                    <p class="text-muted">{{ $facialEvaluation->fitzpatrick_type ?? 'Não informado' }}</p>
                </div>
            </div>
        </div>

        <form id="signatureForm">
            @csrf

            <div class="form-group mb-3">
                <label class="form-label"><strong>Sua Assinatura Digital</strong></label>
                <p class="text-muted mb-2">Assine com o mouse ou toque</p>
                <button type="button" class="clear-btn" id="clearCanvas">
                    <i class="fas fa-redo"></i> Limpar
                </button>
                <canvas id="signatureCanvas" class="signature-canvas"></canvas>
            </div>

            <div class="consent-text">
                <p class="mb-0">
                    <i class="fas fa-file-contract"></i> <strong>Termos e Condições:</strong>
                    Confirmo que as informações fornecidas na ficha de avaliação são corretas.
                    Autorizo o uso das informações e fotos para fins de avaliação e tratamento estético.
                    Esta ficha e seus dados expirão em 24 horas.
                </p>
            </div>

            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="consentCheck" required>
                <label class="form-check-label" for="consentCheck">
                    <strong>Aceito os termos e confirmo minha assinatura</strong>
                </label>
            </div>

            <button type="submit" class="btn-sign">
                <i class="fas fa-check-circle me-2"></i>Confirmar e Assinar
            </button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const canvas = document.getElementById('signatureCanvas');
        const ctx = canvas.getContext('2d');
        let isDrawing = false;
        let lastX = 0;
        let lastY = 0;

        // Ajustar tamanho do canvas
        function resizeCanvas() {
            const rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;
        }
        resizeCanvas();

        // Mouse events
        canvas.addEventListener('mousedown', (e) => {
            isDrawing = true;
            [lastX, lastY] = [e.offsetX, e.offsetY];
        });

        canvas.addEventListener('mousemove', (e) => {
            if (!isDrawing) return;
            const x = e.offsetX;
            const y = e.offsetY;

            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();

            [lastX, lastY] = [x, y];
        });

        canvas.addEventListener('mouseup', () => isDrawing = false);
        canvas.addEventListener('mouseout', () => isDrawing = false);

        // Touch events
        canvas.addEventListener('touchstart', (e) => {
            const rect = canvas.getBoundingClientRect();
            const touch = e.touches[0];
            isDrawing = true;
            [lastX, lastY] = [touch.clientX - rect.left, touch.clientY - rect.top];
        });

        canvas.addEventListener('touchmove', (e) => {
            if (!isDrawing) return;
            e.preventDefault();
            const rect = canvas.getBoundingClientRect();
            const touch = e.touches[0];
            const x = touch.clientX - rect.left;
            const y = touch.clientY - rect.top;

            ctx.strokeStyle = '#000';
            ctx.lineWidth = 2;
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(x, y);
            ctx.stroke();

            [lastX, lastY] = [x, y];
        });

        canvas.addEventListener('touchend', () => isDrawing = false);

        // Limpar canvas
        document.getElementById('clearCanvas').addEventListener('click', () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        });

        // Submeter assinatura
        document.getElementById('signatureForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const consent = document.getElementById('consentCheck').checked;
            if (!consent) {
                alert('Você deve aceitar os termos e condições.');
                return;
            }

            const signatureData = canvas.toDataURL('image/png');

            try {
                const response = await fetch('{{ route("facial-evaluation.process-sign", ["token" => $token]) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        patient_signature: signatureData,
                        consent_accepted: consent
                    })
                });

                const data = await response.json();

                if (!data.error) {
                    document.body.innerHTML = `
                        <div class="container-sign">
                            <div class="text-center">
                                <div style="font-size: 60px; color: #28a745; margin-bottom: 20px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h2 style="color: #28a745; margin-bottom: 20px;">Assinatura Registrada com Sucesso!</h2>
                                <p style="color: #666; margin-bottom: 20px;">
                                    Obrigado por confirmar sua ficha de avaliação facial.
                                    Nossa equipe entrará em contato em breve para agendar seu procedimento.
                                </p>
                                <p style="color: #999; font-size: 14px;">
                                    <i class="fas fa-info-circle"></i> Esta página será fechada em 5 segundos...
                                </p>
                            </div>
                        </div>
                    `;
                    setTimeout(() => {
                        window.close();
                    }, 5000);
                } else {
                    alert('Erro: ' + data.message);
                }
            } catch (error) {
                alert('Erro ao processar assinatura: ' + error.message);
            }
        });
    </script>
</body>
</html>

