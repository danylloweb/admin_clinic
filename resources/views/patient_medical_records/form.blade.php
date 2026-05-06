<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Prontuário do Paciente' }}</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #18322f;
            background:
                radial-gradient(circle at top right, rgba(212, 168, 92, 0.18), transparent 28%),
                linear-gradient(180deg, #fdfbf8 0%, #f7f4ee 100%);
        }

        .page {
            min-height: 100vh;
            padding: 24px 16px 40px;
        }

        .shell {
            width: min(100%, 760px);
            margin: 0 auto;
        }

        .hero {
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.96), rgba(10, 79, 74, 0.92));
            color: #fff;
            border-radius: 28px;
            padding: 28px 22px;
            box-shadow: 0 20px 50px rgba(15, 118, 110, 0.12);
            position: relative;
            overflow: hidden;
        }

        .hero::after {
            content: "";
            position: absolute;
            inset: auto -60px -80px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(212, 168, 92, 0.18);
        }

        .logo {
            width: 84px;
            height: auto;
            margin-bottom: 18px;
            filter: brightness(0) invert(1);
        }

        .hero h1 {
            margin: 0 0 8px;
            font-size: 1.8rem;
            line-height: 1.1;
        }

        .hero p {
            margin: 0;
            color: rgba(255, 255, 255, 0.84);
            line-height: 1.6;
        }

        .patient-card,
        .form-card {
            margin-top: 18px;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 118, 110, 0.12);
            border: 1px solid rgba(15, 118, 110, 0.08);
        }

        .patient-card {
            padding: 18px;
        }

        .patient-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .patient-pill {
            background: #f5fbf9;
            border: 1px solid #d9e5df;
            border-radius: 16px;
            padding: 14px 16px;
        }

        .patient-pill small,
        .section-subtitle,
        .helper,
        .field-hint {
            color: #6b7f7b;
        }

        .patient-pill strong {
            display: block;
            margin-top: 4px;
            color: #18322f;
            font-size: 0.98rem;
        }

        .form-card {
            padding: 20px 18px 24px;
        }

        .section {
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(15, 118, 110, 0.08);
        }

        .section:last-child {
            border-bottom: 0;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        h2 {
            margin: 0 0 6px;
            font-size: 1.08rem;
        }

        .section-subtitle {
            margin: 0 0 16px;
            line-height: 1.5;
            font-size: 0.95rem;
        }

        .grid {
            display: grid;
            gap: 14px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 0.94rem;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #d9e5df;
            border-radius: 16px;
            padding: 15px 16px;
            font: inherit;
            color: #18322f;
            background: #fff;
            transition: border-color .2s ease, box-shadow .2s ease;
            appearance: none;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #0f766e;
            box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.12);
        }

        .select-wrap {
            position: relative;
        }

        .select-wrap::after {
            content: "⌄";
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7f7b;
            pointer-events: none;
        }

        .consent {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            background: #f8faf9;
            border: 1px solid #d9e5df;
            border-radius: 18px;
            padding: 16px;
        }

        .consent input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-top: 2px;
            accent-color: #0f766e;
            padding: 0;
        }

        .button {
            width: 100%;
            border: 0;
            border-radius: 18px;
            padding: 16px 18px;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
            color: #fff;
            background: linear-gradient(135deg, #0f766e, #0a4f4a);
            box-shadow: 0 14px 28px rgba(15, 118, 110, 0.22);
        }

        .button:hover {
            filter: brightness(1.03);
        }

        .alert {
            margin-bottom: 18px;
            padding: 14px 16px;
            border-radius: 16px;
            background: #fff7e8;
            color: #835c12;
            border: 1px solid rgba(212, 168, 92, 0.35);
        }
        .is-invalid {
            border-color: #d14343 !important;
            box-shadow: 0 0 0 3px rgba(209, 67, 67, 0.12) !important;
        }

        .field-error {
            margin-top: 6px;
            font-size: 0.82rem;
            color: #b43232;
        }

        .required::after {
            content: " *";
            color: #d4a85c;
        }

        .footer-note {
            margin-top: 16px;
            text-align: center;
            color: #6b7f7b;
            font-size: 0.92rem;
            line-height: 1.6;
        }

        @media (min-width: 640px) {
            .page {
                padding: 32px 20px 48px;
            }

            .patient-grid,
            .grid.two-cols {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .hero,
            .patient-card,
            .form-card {
                padding-left: 24px;
                padding-right: 24px;
            }
        }
        #toast-container {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .toast {
            min-width: 260px;
            max-width: 320px;
            padding: 14px 16px;
            border-radius: 16px;
            color: #fff;
            font-size: 0.9rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            animation: slideIn .3s ease;
        }

        .toast.success {
            background: linear-gradient(135deg, #0f766e, #0a4f4a);
        }

        .toast.error {
            background: linear-gradient(135deg, #d14343, #a83232);
        }

        @keyframes slideIn {
            from { transform: translateX(120%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeOut {
            to { opacity: 0; transform: translateX(120%); }
        }

        @media (max-width: 640px) {
            #toast-container {
                top: auto;
                bottom: 16px;
                right: 50%;
                transform: translateX(50%);
                width: calc(100% - 32px);
            }

            .toast {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="shell">
        <section class="hero">
            <img src="https://renovarestetica.com.br/Original-01.png" alt="Renovar" class="logo">
            <h1>Cadastro do Paciente</h1>
            <p>Olá, {{ $patient->social_name ?: $patient->name }}. Antes do seu atendimento, pedimos que preencha este formulário com atenção. Leva só alguns minutos Obrigado.🥰</p>
        </section>

        <section class="patient-card">
            <div class="patient-grid">
                <div class="patient-pill">
                    <small>Paciente</small>
                    <strong>{{ $patient->social_name ?: $patient->name }}</strong>
                </div>
                <div class="patient-pill">
                    <small>Contato cadastrado</small>
                    <strong>{{ $patient->phone ?: 'Não informado' }}</strong>
                </div>
            </div>
        </section>

        <form class="form-card" id="medical-record-form" method="POST" action="{{ route('patient-medical-record.submit', ['token' => $record->access_token]) }}">
            @csrf

            @if (isset($errors) && $errors->any())
                <div class="alert" id="server-feedback">
                    Verifique os campos destacados e tente novamente.
                </div>
            @endif

            <section class="section">
                <h2>Contato de apoio</h2>
                <p class="section-subtitle">Se necessário, quem podemos acionar em caso de urgência?</p>
                <div class="grid two-cols">
                    <div>
                        <label for="emergency_contact_name">Nome do contato</label>
                        <input type="text" id="emergency_contact_name" required name="emergency_contact_name" value="{{ old('emergency_contact_name', $record->emergency_contact_name) }}" placeholder="Ex.: Maria da Silva">
                    </div>
                    <div>
                        <label for="emergency_contact_phone">Telefone do contato</label>
                        <input type="text" id="emergency_contact_phone" required name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $record->emergency_contact_phone) }}" placeholder="(81) 99999-0000" inputmode="tel">
                    </div>
                </div>
            </section>

            <section class="section">
                <h2>Objetivo do tratamento</h2>
                <p class="section-subtitle">Conte para nós o que você busca com o atendimento.</p>
                <div class="grid">
                    <div>
                        <label for="treatment_goals">Objetivo principal</label>
                        <textarea id="treatment_goals" name="treatment_goals" required placeholder="Ex.: melhorar flacidez, gordura localizada, acompanhamento pós-procedimento...">{{ old('treatment_goals', $record->treatment_goals) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2>Hábitos e rotina</h2>
                <p class="section-subtitle">Essas informações ajudam a personalizar melhor o seu cuidado.</p>
                <div class="grid two-cols">
                    <div>
                        <label for="type_of_food">Como você considera sua alimentação?</label>
                        <div class="select-wrap">
                            <select id="type_of_food" name="type_of_food" required>
                                <option value="">Selecione</option>
                                @foreach (['Boa', 'Regular', 'Ruim'] as $option)
                                    <option value="{{ $option }}" @selected(old('type_of_food', $record->type_of_food) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="practice_physical_activity">Pratica atividade física?</label>
                        <div class="select-wrap">
                            <select id="practice_physical_activity" name="practice_physical_activity" required>
                                <option value="">Selecione</option>
                                @foreach (['Sim', 'As vezes', 'Não'] as $option)
                                    <option value="{{ $option }}" @selected(old('practice_physical_activity', $record->practice_physical_activity) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="consume_alcohol">Consome bebida alcoólica?</label>
                        <div class="select-wrap">
                            <select id="consume_alcohol" name="consume_alcohol" required>
                                <option value="">Selecione</option>
                                @foreach (['Sim', 'As vezes', 'Não'] as $option)
                                    <option value="{{ $option }}" @selected(old('consume_alcohol', $record->consume_alcohol) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="smoke">Fuma?</label>
                        <div class="select-wrap">
                            <select id="smoke" name="smoke" required>
                                <option value="">Selecione</option>
                                @foreach (['Sim', 'As vezes', 'Não'] as $option)
                                    <option value="{{ $option }}" @selected(old('smoke', $record->smoke) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="liters_of_water_per_day">Litros de água por dia</label>
                        <input type="number" id="liters_of_water_per_day" required name="liters_of_water_per_day" min="0" max="20" value="{{ old('liters_of_water_per_day', $record->liters_of_water_per_day) }}" placeholder="Ex.: 2">
                        <div class="field-hint">Informe um valor aproximado.</div>
                    </div>
                    <div>
                        <label for="children">Filhos</label>
                        <input type="text" id="children" name="children" value="{{ old('children', $record->children) }}" placeholder="Ex.: 2">
                    </div>
                </div>
            </section>

            <section class="section">
                <h2>Saúde e histórico</h2>
                <p class="section-subtitle">Esses dados são importantes para sua segurança durante a avaliação e o tratamento.</p>
                <div class="grid two-cols">
                    <div>
                        <label for="use_medication">Usa alguma medicação?</label>
                        <textarea id="use_medication" name="use_medication" placeholder="Informe quais e para que usa">{{ old('use_medication', $record->use_medication) }}</textarea>
                    </div>
                    <div>
                        <label for="have_allergies">Possui alergias?</label>
                        <textarea id="have_allergies" name="have_allergies" placeholder="Se sim, informe quais">{{ old('have_allergies', $record->have_allergies) }}</textarea>
                    </div>
                    <div>
                        <label for="use_anabolic_hormones">Usa hormônios ou anabolizantes?</label>
                        <textarea id="use_anabolic_hormones" name="use_anabolic_hormones" placeholder="Se sim, detalhe">{{ old('use_anabolic_hormones', $record->use_anabolic_hormones) }}</textarea>
                    </div>
                    <div>
                        <label for="blood_type">Tipo sanguíneo</label>
                        <div class="select-wrap">
                            <select id="blood_type" name="blood_type">
                                <option value="">Selecione</option>
                                @foreach (['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-', 'Outros'] as $option)
                                    <option value="{{ $option }}" @selected(old('blood_type', $record->blood_type) === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @foreach ([
                        'pacemaker' => 'Possui marca-passo?',
                        'metal_prosthesis' => 'Possui prótese/metais no corpo?',
                        'diabetes' => 'Tem diabetes?',
                        'oncology' => 'Tem histórico oncológico?',
                        'arterial_hypertension' => 'Tem hipertensão arterial?',
                    ] as $field => $label)
                        <div>
                            <label for="{{ $field }}">{{ $label }}</label>
                            <div class="select-wrap">
                                <select id="{{ $field }}" name="{{ $field }}" required>
                                    <option value="">Selecione</option>
                                    @foreach (['Sim', 'Não'] as $option)
                                        <option value="{{ $option }}" @selected(old($field, $record->{$field}) === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <h2>Observações adicionais</h2>
                <p class="section-subtitle">Se quiser, adicione algo importante para a equipe saber antes do atendimento.</p>
                <div class="grid">
                    <div>
                        <label for="observation">Observações</label>
                        <textarea id="observation" name="observation" placeholder="Ex.: restrições, cirurgias, sensibilidade, contexto clínico...">{{ old('observation', $record->observation) }}</textarea>
                    </div>
                </div>
            </section>

            <section class="section">
                <h2>Confirmação</h2>
                <p class="section-subtitle">Ao enviar, você confirma que as informações acima são verdadeiras.</p>
                <div class="grid">
                    <div class="consent">
                        <input type="checkbox" id="lgpd_consent" required name="lgpd_consent" value="1" {{ old('lgpd_consent', $record->lgpd_consent) ? 'checked' : '' }}>
                        <div>
                            <label for="lgpd_consent" class="required" style="margin-bottom: 4px;">Autorizo o uso dessas informações para meu atendimento</label>
                            <div class="helper">As informações serão utilizadas exclusivamente pela equipe da Renovar para avaliação e condução do atendimento.</div>
                        </div>
                    </div>
                    <div>
                        <label for="signature_name" class="required">Nome completo para assinatura</label>
                        <input type="text" id="signature_name" required name="signature_name" value="{{ old('signature_name', $record->signature_name ?: ($patient->social_name ?: $patient->name)) }}" placeholder="Digite seu nome completo">
                    </div>
                </div>
            </section>

            <button type="submit" class="button" id="submit-medical-record">Enviar prontuário</button>
            <div class="footer-note">Se tiver qualquer dificuldade, fale com nossa equipe pelo WhatsApp e enviaremos um novo link.</div>
        </form>
    </div>
</div>
<div id="toast-container"></div>
<script>
    (function () {
        const form = document.getElementById('medical-record-form');
        const submitButton = document.getElementById('submit-medical-record');
        if (!form || !submitButton) {
            return;
        }
        function clearFieldErrors() {
            form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
            form.querySelectorAll('.field-error').forEach((el) => el.remove());
        }

        function showFormFeedback(message, isSuccess) {
            const container = document.getElementById('toast-container');

            const toast = document.createElement('div');
            toast.className = `toast ${isSuccess ? 'success' : 'error'}`;
            toast.innerText = message;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.animation = 'fadeOut .3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function showFieldErrors(errors) {
            Object.entries(errors || {}).forEach(([field, messages]) => {
                const input = form.querySelector(`[name="${field}"]`);
                if (!input) return;

                input.classList.add('is-invalid');

                const errorEl = document.createElement('div');
                errorEl.className = 'field-error';
                errorEl.innerText = Array.isArray(messages) ? messages[0] : String(messages);

                input.insertAdjacentElement('afterend', errorEl);
            });
        }

        function scrollToFirstError() {
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            clearFieldErrors();
            if (!form.checkValidity()) {
                const invalidFields = form.querySelectorAll(':invalid');

                invalidFields.forEach((field) => {
                    field.classList.add('is-invalid');

                    // mensagem padrão do browser
                    const message = field.validationMessage;

                    const errorEl = document.createElement('div');
                    errorEl.className = 'field-error';
                    errorEl.innerText = message;

                    // evita duplicar erro
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('field-error')) {
                        field.insertAdjacentElement('afterend', errorEl);
                    }
                });

                showFormFeedback('Preencha os campos obrigatórios corretamente.', false);

                scrollToFirstError();
                return; // ⛔ BLOQUEIA envio
            }

            const originalText = submitButton.innerText;
            submitButton.disabled = true;
            submitButton.innerText = 'Enviando...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                let payload;

                try {
                    payload = await response.json();
                } catch (e) {
                    payload = {};
                }

                if (response.ok) {
                    showFormFeedback(payload.message || 'Prontuário enviado com sucesso.', true);
                    const redirectTo = '{{ route('patient-medical-record.status',['token' => $record->access_token]) }}';
                    setTimeout(() => {
                        window.location.href = redirectTo;
                    }, 300);
                    return;
                }

                if (response.status === 422) {
                    showFormFeedback(payload.message || 'Verifique os campos destacados e tente novamente.', false);
                    showFieldErrors(payload.errors || {});
                    scrollToFirstError();
                    return;
                }
                const errorMessage = payload.message || `Erro ${response.status}: Não foi possível enviar agora.`;
                showFormFeedback(errorMessage, false);
            } catch (error) {
                console.log('Erro ao enviar formulário:', error);
            } finally {
                submitButton.disabled = false;
                submitButton.innerText = originalText;
            }
        });
    })();
</script>
</body>
</html>

