@extends('layouts.header')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-checklist me-2"></i>Ficha de Avaliação Facial
                        </h4>
                        <small>Paciente: {{ $patient->name ?? 'Selecione um paciente' }}</small>
                    </div>
                    <a href="{{ route('panel.patient.show', ['id' => $patient->id ?? 0]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-times"></i> Fechar
                    </a>
                </div>

                <div class="card-body">
                    <form id="facialEvaluationForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="patient_id" value="{{ $patient->id ?? '' }}">
                        <input type="hidden" name="professional_id" value="{{ auth()->user()->id ?? '' }}">

                        <!-- Seção: Dados do Paciente -->
                        <div class="card mb-4 border-2 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Dados do Paciente</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" value="{{ $patient->name ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">CPF</label>
                                        <input type="text" class="form-control cpf-mask" name="cpf" value="{{ $facialEvaluation->cpf ?? '' }}" placeholder="000.000.000-00">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" value="{{ $patient->birth_date ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Telefone</label>
                                        <input type="text" class="form-control phone-mask" value="{{ $patient->phone ?? '' }}" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">E-mail</label>
                                        <input type="email" class="form-control" name="email" value="{{ $facialEvaluation->email ?? '' }}" placeholder="email@example.com">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">WhatsApp</label>
                                        <input type="text" class="form-control phone-mask" name="whatsapp" value="{{ $facialEvaluation->whatsapp ?? '' }}" placeholder="(11) 9 9999-9999">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Queixa Principal -->
                        <div class="card mb-4 border-2 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i>Queixa Principal</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="chief_complaint" rows="4" placeholder="Descreva a principal queixa ou motivo da avaliação...">{{ $facialEvaluation->chief_complaint ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Seção: Tipo de Pele -->
                        <div class="card mb-4 border-2 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-droplet me-2"></i>Tipo de Pele</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach(['normal' => 'Normal', 'oily' => 'Oleosa', 'dry' => 'Seca', 'mixed' => 'Mista', 'sensitive' => 'Sensível'] as $value => $label)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="skin_type" id="skin_{{ $value }}" value="{{ $value }}" {{ ($facialEvaluation->skin_type ?? '') === $value ? 'checked' : '' }}>
                                            <label class="form-check-label" for="skin_{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Avaliação de Pele -->
                        <div class="card mb-4 border-2 border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-magnifying-glass me-2"></i>Avaliação de Pele</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label">Oleosidade (0-10)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="range" class="form-range" name="oiliness" min="0" max="10" value="{{ $facialEvaluation->oiliness ?? 5 }}" id="oiliness-slider">
                                            <span class="badge bg-primary" id="oiliness-value">{{ $facialEvaluation->oiliness ?? 5 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Hidratação (0-10)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="range" class="form-range" name="hydration" min="0" max="10" value="{{ $facialEvaluation->hydration ?? 5 }}" id="hydration-slider">
                                            <span class="badge bg-primary" id="hydration-value">{{ $facialEvaluation->hydration ?? 5 }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Sensibilidade (0-10)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="range" class="form-range" name="sensitivity" min="0" max="10" value="{{ $facialEvaluation->sensitivity ?? 5 }}" id="sensitivity-slider">
                                            <span class="badge bg-primary" id="sensitivity-value">{{ $facialEvaluation->sensitivity ?? 5 }}</span>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <div class="row">
                                    @foreach([
                                        'acne' => 'Acne',
                                        'melasma' => 'Melasma',
                                        'wrinkles' => 'Rugas',
                                        'flaccidity' => 'Flacidez',
                                        'spots' => 'Manchas',
                                        'dilated_pores' => 'Poros Dilatados'
                                    ] as $field => $label)
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input problem-check" type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1" {{ ($facialEvaluation->$field ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="{{ $field }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                        <div id="{{ $field }}-notes" class="mt-2" style="display: {{ ($facialEvaluation->$field ?? false) ? 'block' : 'none' }};">
                                            <textarea class="form-control form-control-sm" name="{{ $field }}_notes" placeholder="Observações..." rows="2">{{ $facialEvaluation->{"${field}_notes"} ?? '' }}</textarea>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Fototipo de Fitzpatrick -->
                        <div class="card mb-4 border-2 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-palette me-2"></i>Fototipo de Fitzpatrick</h5>
                            </div>
                            <div class="card-body">
                                <select class="form-select" name="fitzpatrick_type">
                                    <option value="">Selecione...</option>
                                    @foreach(['I', 'II', 'III', 'IV', 'V', 'VI'] as $type)
                                    <option value="{{ $type }}" {{ ($facialEvaluation->fitzpatrick_type ?? '') === $type ? 'selected' : '' }}>
                                        Tipo {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Seção: Histórico Estético -->
                        <div class="card mb-4 border-2 border-purple">
                            <div class="card-header bg-purple text-white" style="background-color: #6f42c1;">
                                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Histórico Estético</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach(['botox' => 'Botox', 'preenchimento' => 'Preenchimento', 'laser' => 'Laser', 'peeling' => 'Peeling', 'bioestimulador' => 'Bioestimulador'] as $value => $label)
                                    <div class="col-md-6 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="aesthetic_history[]" id="aesthetic_{{ $value }}" value="{{ $value }}"
                                                {{ in_array($value, $facialEvaluation->aesthetic_history ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="aesthetic_{{ $value }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Alergias -->
                        <div class="card mb-4 border-2 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Alergias</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="allergies" rows="3" placeholder="Descreva qualquer alergia conhecida...">{{ $facialEvaluation->allergies ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Seção: Medicamentos em Uso -->
                        <div class="card mb-4 border-2 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-pills me-2"></i>Medicamentos em Uso</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="medications_in_use" rows="3" placeholder="Liste os medicamentos em uso...">{{ $facialEvaluation->medications_in_use ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Seção: Objetivo do Paciente -->
                        <div class="card mb-4 border-2 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Objetivo do Paciente</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="patient_objective" rows="3" placeholder="Qual é o objetivo do paciente com o tratamento?">{{ $facialEvaluation->patient_objective ?? '' }}</textarea>
                            </div>
                        </div>

                        <!-- Seção: Plano de Tratamento -->
                        <div class="card mb-4 border-2 border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-stethoscope me-2"></i>Plano de Tratamento</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Procedimento Indicado</label>
                                        <input type="text" class="form-control" name="treatment_procedure" placeholder="Ex: Microagulhamento" value="{{ $facialEvaluation->treatment_plan['procedure'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quantidade de Sessões</label>
                                        <input type="number" class="form-control" name="treatment_sessions" placeholder="0" value="{{ $facialEvaluation->treatment_plan['sessions'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Frequência</label>
                                        <input type="text" class="form-control" name="treatment_frequency" placeholder="Ex: 1x por semana" value="{{ $facialEvaluation->treatment_plan['frequency'] ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Observações</label>
                                        <input type="text" class="form-control" name="treatment_observations" placeholder="Observações adicionais" value="{{ $facialEvaluation->treatment_plan['observations'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Registro Fotográfico -->
                        <div class="card mb-4 border-2 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Registro Fotográfico</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Foto Frontal</label>
                                        <input type="file" class="form-control photo-input" name="photo_front" accept="image/*">
                                        <img id="preview-front" class="img-fluid mt-2 rounded" style="max-height: 200px; display: {{ $facialEvaluation->photo_front ? 'block' : 'none' }};" src="{{ $facialEvaluation->photo_front ?? '' }}" alt="Foto frontal">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Perfil Direito</label>
                                        <input type="file" class="form-control photo-input" name="photo_profile_right" accept="image/*">
                                        <img id="preview-right" class="img-fluid mt-2 rounded" style="max-height: 200px; display: {{ $facialEvaluation->photo_profile_right ? 'block' : 'none' }};" src="{{ $facialEvaluation->photo_profile_right ?? '' }}" alt="Perfil direito">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Perfil Esquerdo</label>
                                        <input type="file" class="form-control photo-input" name="photo_profile_left" accept="image/*">
                                        <img id="preview-left" class="img-fluid mt-2 rounded" style="max-height: 200px; display: {{ $facialEvaluation->photo_profile_left ? 'block' : 'none' }};" src="{{ $facialEvaluation->photo_profile_left ?? '' }}" alt="Perfil esquerdo">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Seção: Termo de Consentimento -->
                        <div class="card mb-4 border-2 border-primary">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-file-contract me-2"></i>Termo de Consentimento</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="consent_accepted" id="consent" value="1" {{ ($facialEvaluation->consent_accepted ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="consent">
                                        Confirmo que li e aceito os termos de consentimento. Autorizo o uso das informações e fotos coletadas para fins de avaliação e tratamento estético.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="d-flex gap-2 justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-success btn-lg" id="saveBtn">
                                    <i class="fas fa-save me-2"></i>Salvar Ficha
                                </button>
                                <a href="{{ route('panel.patient.show', ['id' => $patient->id ?? 0]) }}" class="btn btn-secondary btn-lg ms-2">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                            @if($facialEvaluation && isset($facialEvaluation->id))
                            <div>
                                <button type="button" class="btn btn-info btn-lg" id="generateTokenBtn">
                                    <i class="fas fa-link me-2"></i>Gerar Link de Assinatura
                                </button>
                                <button type="button" class="btn btn-warning btn-lg ms-2" id="sendLinkBtn">
                                    <i class="fab fa-whatsapp me-2"></i>Enviar por WhatsApp
                                </button>
                            </div>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Máscaras
        new IMask(document.querySelector('.cpf-mask'), {
            mask: '000.000.000-00'
        });

        new IMask(document.querySelectorAll('.phone-mask'), {
            mask: '(00) 00000-0000'
        });

        // Preview de imagens
        document.querySelectorAll('.photo-input').forEach((input, index) => {
            const previewId = ['preview-front', 'preview-right', 'preview-left'][index];
            input.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        document.getElementById(previewId).src = event.target.result;
                        document.getElementById(previewId).style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        // Mostrar/ocultar notas de problemas
        document.querySelectorAll('.problem-check').forEach(check => {
            check.addEventListener('change', function () {
                const field = this.name;
                const notesDiv = document.getElementById(field + '-notes');
                notesDiv.style.display = this.checked ? 'block' : 'none';
            });
        });

        // Atualizar valores dos sliders
        ['oiliness', 'hydration', 'sensitivity'].forEach(field => {
            const slider = document.getElementById(field + '-slider');
            const value = document.getElementById(field + '-value');
            slider.addEventListener('input', function () {
                value.textContent = this.value;
            });
        });

        // Submit do formulário
        document.getElementById('facialEvaluationForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            await saveFacialEvaluation();
        });

        // Gerar token
        document.getElementById('generateTokenBtn')?.addEventListener('click', async function () {
            const id = new URLSearchParams(window.location.search).get('id') || '{{ $facialEvaluation->id ?? '' }}';
            if (!id) {
                showToast('Salve a ficha primeiro.', 'warning');
                return;
            }

            try {
                const response = await fetch(`/panel-facial-evaluations-generate-token/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (!data.error) {
                    showToast(data.message, 'success');
                    // Atualizar URL para incluir o link
                    const urlInput = document.createElement('input');
                    urlInput.type = 'text';
                    urlInput.className = 'form-control';
                    urlInput.value = data.signature_url;
                    urlInput.readOnly = true;
                    // Mostrar em um modal ou salvar para uso posterior
                } else {
                    showToast(data.message, 'danger');
                }
            } catch (error) {
                showToast('Erro ao gerar token: ' + error.message, 'danger');
            }
        });

        // Enviar link por WhatsApp
        document.getElementById('sendLinkBtn')?.addEventListener('click', async function () {
            const id = new URLSearchParams(window.location.search).get('id') || '{{ $facialEvaluation->id ?? '' }}';
            if (!id) {
                showToast('Salve a ficha primeiro.', 'warning');
                return;
            }

            try {
                const response = await fetch(`/panel-facial-evaluations-send-link/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (!data.error) {
                    window.open(data.whatsapp_url, '_blank');
                    showToast('Abrindo WhatsApp...', 'success');
                } else {
                    showToast(data.message, 'danger');
                }
            } catch (error) {
                showToast('Erro ao enviar link: ' + error.message, 'danger');
            }
        });

        async function saveFacialEvaluation() {
            const form = document.getElementById('facialEvaluationForm');
            const formData = new FormData(form);

            // Coletar aesthetic_history
            const aestheticHistory = [];
            document.querySelectorAll('input[name="aesthetic_history[]"]:checked').forEach(check => {
                aestheticHistory.push(check.value);
            });

            // Criar objeto treatment_plan
            const treatmentPlan = {
                procedure: formData.get('treatment_procedure'),
                sessions: formData.get('treatment_sessions'),
                frequency: formData.get('treatment_frequency'),
                observations: formData.get('treatment_observations')
            };

            const data = {
                patient_id: formData.get('patient_id'),
                cpf: formData.get('cpf'),
                email: formData.get('email'),
                whatsapp: formData.get('whatsapp'),
                chief_complaint: formData.get('chief_complaint'),
                skin_type: formData.get('skin_type'),
                oiliness: parseInt(formData.get('oiliness')),
                hydration: parseInt(formData.get('hydration')),
                sensitivity: parseInt(formData.get('sensitivity')),
                acne: formData.get('acne') ? true : false,
                acne_notes: formData.get('acne_notes'),
                melasma: formData.get('melasma') ? true : false,
                melasma_notes: formData.get('melasma_notes'),
                wrinkles: formData.get('wrinkles') ? true : false,
                wrinkles_notes: formData.get('wrinkles_notes'),
                flaccidity: formData.get('flaccidity') ? true : false,
                flaccidity_notes: formData.get('flaccidity_notes'),
                spots: formData.get('spots') ? true : false,
                spots_notes: formData.get('spots_notes'),
                dilated_pores: formData.get('dilated_pores') ? true : false,
                dilated_pores_notes: formData.get('dilated_pores_notes'),
                fitzpatrick_type: formData.get('fitzpatrick_type'),
                aesthetic_history: aestheticHistory,
                allergies: formData.get('allergies'),
                medications_in_use: formData.get('medications_in_use'),
                patient_objective: formData.get('patient_objective'),
                treatment_plan: treatmentPlan,
                consent_accepted: formData.get('consent_accepted') ? true : false,
            };

            try {
                const url = '{{ isset($facialEvaluation) ? route("panel.facial-evaluations.update", ["id" => $facialEvaluation->id]) : route("panel.facial-evaluations.store") }}';
                const method = '{{ isset($facialEvaluation) ? "PUT" : "POST" }}';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.error) {
                    showToast(result.message || 'Erro ao salvar', 'danger');
                } else {
                    showToast('Ficha salva com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.href = `{{ route('panel.facial-evaluations.index', ['patientId' => $patient->id]) }}`;
                    }, 1500);
                }
            } catch (error) {
                showToast('Erro: ' + error.message, 'danger');
            }
        }
    });
</script>
@endpush
@endsection

