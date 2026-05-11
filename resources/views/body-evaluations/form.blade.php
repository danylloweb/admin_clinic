@extends('layouts.header')
@section('content')
@php
    $isEdit = !empty($bodyEvaluation) && !empty($bodyEvaluation->id);

    $perimetry = $bodyEvaluation->perimetry ?? [];
    $cellulite = $bodyEvaluation->cellulite ?? [];
    $flaccidity = $bodyEvaluation->flaccidity ?? [];
    $objectives = $bodyEvaluation->objectives ?? [];
    $medicalHistory = $bodyEvaluation->medical_history ?? [];
    $bodyMapAreas = $bodyEvaluation->body_map_areas ?? [];
    $evolutionSessions = $bodyEvaluation->evolution_sessions ?? [];
    $treatmentPlanLines = $bodyEvaluation->treatment_plan ?? [];
@endphp

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Avaliacao Corporal</h4>
                    <a href="{{ route('panel.patient.show', ['id' => $patient->id]) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-times me-1"></i>Fechar
                    </a>
                </div>

                <div class="card-body">
                    <form id="bodyEvaluationForm"
                          action="{{ $isEdit ? route('panel.body-evaluations.update', ['id' => $bodyEvaluation->id]) : route('panel.body-evaluations.store') }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @if($isEdit)
                            @method('PUT')
                        @endif

                        <input type="hidden" name="patient_id" value="{{ $patient->id }}">
                        <input type="hidden" name="professional_id" value="{{ auth()->id() }}">

                        <input type="hidden" name="perimetry" id="perimetry-hidden">
                        <input type="hidden" name="cellulite" id="cellulite-hidden">
                        <input type="hidden" name="flaccidity" id="flaccidity-hidden">
                        <input type="hidden" name="body_map_areas" id="body-map-areas-hidden">
                        <input type="hidden" name="objectives" id="objectives-hidden">
                        <input type="hidden" name="medical_history" id="medical-history-hidden">
                        <input type="hidden" name="evolution_sessions" id="evolution-sessions-hidden">
                        <input type="hidden" name="treatment_plan" id="treatment-plan-hidden">

                        <div class="card mb-4 border-2">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Dados do Paciente</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nome</label>
                                        <input type="text" class="form-control" value="{{ $patient->name }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" value="{{ $patient->birth_date ? \Carbon\Carbon::parse($patient->birth_date)->format('Y-m-d') : '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Telefone</label>
                                        <input type="text" class="form-control" value="{{ $patient->phone }}" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Medidas Corporais e IMC</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Peso (kg)</label>
                                        <input type="number" name="weight" class="form-control js-weight" step="0.1" value="{{ $bodyEvaluation->weight ?? '' }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Altura (cm)</label>
                                        <input type="number" name="height" class="form-control js-height" step="0.1" value="{{ $bodyEvaluation->height ?? '' }}">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">IMC</label>
                                        <input type="text" id="bmi-display" class="form-control" readonly>
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Gordura (%)</label>
                                        <input type="number" name="fat_percentage" class="form-control" step="0.1" value="{{ $bodyEvaluation->fat_percentage ?? '' }}">
                                    </div>
                                    <div class="col-md-2 mb-3">
                                        <label class="form-label">Musculatura (%)</label>
                                        <input type="number" name="muscle_mass" class="form-control" step="0.1" value="{{ $bodyEvaluation->muscle_mass ?? '' }}">
                                    </div>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="liquid_retention" id="liquid-retention" value="1" {{ !empty($bodyEvaluation->liquid_retention) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="liquid-retention">Retencao de liquidos</label>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="fas fa-ruler me-2"></i>Perimetria (cm)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach([
                                        'bust' => 'Busto',
                                        'waist' => 'Cintura',
                                        'hip' => 'Quadril',
                                        'thigh_r' => 'Coxa direita',
                                        'thigh_l' => 'Coxa esquerda',
                                        'calf_r' => 'Panturrilha direita',
                                        'calf_l' => 'Panturrilha esquerda',
                                        'arm_r' => 'Braco direito'
                                    ] as $key => $label)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">{{ $label }}</label>
                                            <input type="number" class="form-control js-perimetry" data-key="{{ $key }}" step="0.1" value="{{ $perimetry[$key] ?? '' }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-danger">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Celulite</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Grau</label>
                                        <select id="cellulite-grade" class="form-select">
                                            <option value="">Selecionar</option>
                                            @foreach(['I', 'II', 'III', 'IV'] as $grade)
                                                <option value="{{ $grade }}" {{ ($cellulite['grade'] ?? '') === $grade ? 'selected' : '' }}>{{ $grade }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label d-block">Tipo</label>
                                        @foreach(['fibrosa' => 'Fibrosa', 'edematosa' => 'Edematosa', 'flaccida' => 'Flaccida'] as $value => $label)
                                            <div class="form-check">
                                                <input class="form-check-input js-cellulite-type" type="checkbox" value="{{ $value }}" id="cellulite-{{ $value }}" {{ in_array($value, $cellulite['types'] ?? []) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="cellulite-{{ $value }}">{{ $label }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Flacidez</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tissular: <span id="flac-tissue-label">{{ (int)($flaccidity['tissue'] ?? 5) }}</span></label>
                                        <input type="range" class="form-range" id="flac-tissue" min="0" max="10" value="{{ (int)($flaccidity['tissue'] ?? 5) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Muscular: <span id="flac-muscle-label">{{ (int)($flaccidity['muscle'] ?? 5) }}</span></label>
                                        <input type="range" class="form-range" id="flac-muscle" min="0" max="10" value="{{ (int)($flaccidity['muscle'] ?? 5) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Mapa Corporal Feminino (pontos de medida)</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-lg-7 text-center">
                                        <svg id="body-map" width="320" height="520" viewBox="0 0 320 520" style="max-width:100%; border:1px solid #d0d0d0; border-radius:12px; background:#f8f9fa;">
                                            <image
                                                href="{{ asset('img/body-map-feminino.png') }}"
                                                x="0"
                                                y="0"
                                                width="320"
                                                height="520"
                                                preserveAspectRatio="none"
                                                class="body-map-image"
                                            />

                                            <circle cx="160" cy="128" r="8" class="body-area measure-point" id="point-bust" data-label="Busto" data-key="bust" />
                                            <text x="174" y="151" class="measure-value" id="measure-val-bust">-</text>

                                            <circle cx="160" cy="172" r="8" class="body-area measure-point" id="point-waist" data-label="Cintura" data-key="waist" />
                                            <text x="174" y="195" class="measure-value" id="measure-val-waist">-</text>

                                            <circle cx="195" cy="220" r="8" class="body-area measure-point" id="point-hip" data-label="Quadril" data-key="hip" />
                                            <text x="174" y="247" class="measure-value" id="measure-val-hip">-</text>

                                            <circle cx="132" cy="280" r="8" class="body-area measure-point" id="point-thigh-r" data-label="Coxa direita" data-key="thigh_r" />
                                            <text x="146" y="325" class="measure-value" id="measure-val-thigh_r">-</text>

                                            <circle cx="188" cy="280" r="8" class="body-area measure-point" id="point-thigh-l" data-label="Coxa esquerda" data-key="thigh_l" />
                                            <text x="202" y="325" class="measure-value" id="measure-val-thigh_l">-</text>

                                            <circle cx="126" cy="402" r="8" class="body-area measure-point" id="point-calf-r" data-label="Panturrilha direita" data-key="calf_r" />
                                            <text x="140" y="405" class="measure-value" id="measure-val-calf_r">-</text>

                                            <circle cx="194" cy="402" r="8" class="body-area measure-point" id="point-calf-l" data-label="Panturrilha esquerda" data-key="calf_l" />
                                            <text x="208" y="405" class="measure-value" id="measure-val-calf_l">-</text>

                                            <circle cx="114" cy="156" r="8" class="body-area measure-point" id="point-arm-r" data-label="Braco direito" data-key="arm_r" />
                                            <text x="118" y="179" class="measure-value" id="measure-val-arm_r">-</text>
                                        </svg>
                                    </div>

                                    <div class="col-lg-5">
                                        <div class="border rounded p-3 mb-3 bg-light-subtle">
                                            <label class="form-label mb-2">Ponto selecionado</label>
                                            <div id="point-selected-label" class="fw-semibold mb-2 text-muted">Clique em um ponto no corpo</div>
                                            <div class="input-group">
                                                <input type="number" step="0.1" id="point-measure-input" class="form-control" placeholder="Valor da medida (cm)">
                                                <button type="button" class="btn btn-primary" id="point-save-btn">Aplicar</button>
                                            </div>
                                            <small class="text-muted d-block mt-2">A medida aplicada atualiza automaticamente a tabela de Perimetria.</small>
                                        </div>

                                        <label class="form-label">Pontos marcados</label>
                                        <div id="selected-areas" class="border rounded p-2" style="min-height: 130px;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4" style="border:2px solid #6f42c1;">
                            <div class="card-header text-white" style="background:#6f42c1;">
                                <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Evolucao</h5>
                            </div>
                            <div class="card-body">
                                <button type="button" id="add-evolution" class="btn btn-sm btn-success mb-3">
                                    <i class="fas fa-plus me-1"></i>Adicionar sessao
                                </button>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Data</th>
                                                <th>Peso</th>
                                                <th>Gordura%</th>
                                                <th>Musculatura%</th>
                                                <th>Medidas</th>
                                                <th>Notas</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="evolution-tbody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-info">
                            <div class="card-header bg-info text-white"><h5 class="mb-0">Objetivos</h5></div>
                            <div class="card-body">
                                @foreach([
                                    'reduce-weight' => 'Reducao de peso',
                                    'cellulite' => 'Reducao de celulite',
                                    'firm' => 'Firmeza',
                                    'muscle' => 'Ganho de massa muscular',
                                    'drainage' => 'Drenagem',
                                    'localized' => 'Reducao localizada',
                                    'general' => 'Melhoria geral'
                                ] as $value => $label)
                                    <div class="form-check">
                                        <input class="form-check-input js-objective" type="checkbox" value="{{ $value }}" id="obj-{{ $value }}" {{ in_array($value, $objectives) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="obj-{{ $value }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-warning">
                            <div class="card-header bg-warning text-dark"><h5 class="mb-0">Historico Medico</h5></div>
                            <div class="card-body">
                                @foreach([
                                    'hypertension' => 'Hipertensao',
                                    'diabetes' => 'Diabetes',
                                    'thyroid' => 'Tireoide',
                                    'arthritis' => 'Artrite',
                                    'osteoporosis' => 'Osteoporose',
                                    'lipedema' => 'Lipedema'
                                ] as $value => $label)
                                    <div class="form-check">
                                        <input class="form-check-input js-medical" type="checkbox" value="{{ $value }}" id="med-{{ $value }}" {{ in_array($value, $medicalHistory) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="med-{{ $value }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-secondary">
                            <div class="card-header bg-secondary text-white"><h5 class="mb-0">Procedimentos Anteriores</h5></div>
                            <div class="card-body">
                                <textarea class="form-control" name="previous_procedures" rows="3">{{ $bodyEvaluation->previous_procedures ?? '' }}</textarea>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-success">
                            <div class="card-header bg-success text-white"><h5 class="mb-0">Plano de Tratamento</h5></div>
                            <div class="card-body">
                                <textarea id="treatment-plan-text" class="form-control" rows="4" placeholder="Uma linha por item do plano">{{ implode("\n", $treatmentPlanLines) }}</textarea>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-primary">
                            <div class="card-header bg-primary text-white"><h5 class="mb-0">Fotos</h5></div>
                            <div class="card-body">
                                <div class="row g-3">
                                    @foreach([
                                        'front' => ['field' => 'photo_front', 'label' => 'Frontal', 'value' => $bodyEvaluation->photo_front ?? ''],
                                        'right' => ['field' => 'photo_profile_right', 'label' => 'Perfil direito', 'value' => $bodyEvaluation->photo_profile_right ?? ''],
                                        'left' => ['field' => 'photo_profile_left', 'label' => 'Perfil esquerdo', 'value' => $bodyEvaluation->photo_profile_left ?? '']
                                    ] as $target => $config)
                                        <div class="col-md-4">
                                            <label class="form-label">{{ $config['label'] }}</label>
                                            <img id="preview-{{ $target }}" src="{{ $config['value'] ?: asset('img/placeholder.png') }}" class="img-fluid rounded mb-2" style="height:200px; width:100%; object-fit:cover;">
                                            <input type="file" class="form-control form-control-sm mb-2 js-photo-input" data-target="{{ $target }}" accept="image/*">
                                            <button type="button" class="btn btn-sm btn-info w-100 js-open-camera" data-target="{{ $target }}">
                                                <i class="fas fa-camera me-1"></i>Usar camera
                                            </button>
                                            <small id="status-{{ $target }}" class="text-muted d-block mt-1"></small>
                                            <input type="hidden" name="{{ $config['field'] }}" id="hidden-{{ $target }}" value="{{ $config['value'] }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4 border-2 border-danger">
                            <div class="card-header bg-danger text-white"><h5 class="mb-0">Consentimento</h5></div>
                            <div class="card-body">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="consent_accepted" id="consent_accepted" value="1" {{ !empty($bodyEvaluation->consent_accepted) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="consent_accepted">
                                        Autorizo o uso das informacoes e fotos para fins de avaliacao e tratamento.
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" id="save-btn" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Salvar
                            </button>
                            <a href="{{ route('panel.patient.show', ['id' => $patient->id]) }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Capturar foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body d-flex align-items-center justify-content-center" style="background:#000;">
                <video id="cameraVideo" autoplay playsinline style="width:95vw; height:78vh; max-width:100%; object-fit:contain;"></video>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="capturePhotoBtn" class="btn btn-primary">
                    <i class="fas fa-camera me-1"></i>Capturar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .body-map-image { pointer-events: none; }
    .body-area { fill: #41b3ff; stroke: #0f5f8f; stroke-width: 2; cursor: pointer; transition: transform .15s ease, fill .2s ease; }
    .body-area:hover { transform: scale(1.08); }
    .body-area.is-selected { fill: #ff6b6b; }
    .measure-value { fill: #304050; font-size: 11px; font-weight: 700; }
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('bodyEvaluationForm');
        const saveBtn = document.getElementById('save-btn');
        const csrf = document.querySelector('input[name="_token"]').value;

        const initialBodyAreas = @json($bodyMapAreas);
        const initialEvolution = @json($evolutionSessions);

        const validPointIds = new Set(Array.from(document.querySelectorAll('.measure-point')).map((el) => el.id));
        const selectedAreas = new Set(
            (Array.isArray(initialBodyAreas) ? initialBodyAreas : []).filter((id) => validPointIds.has(id))
        );

        const selectedAreasBox = document.getElementById('selected-areas');
        const bodyAreasHidden = document.getElementById('body-map-areas-hidden');
        const evolutionHidden = document.getElementById('evolution-sessions-hidden');
        const pointSelectedLabel = document.getElementById('point-selected-label');
        const pointMeasureInput = document.getElementById('point-measure-input');
        const pointSaveBtn = document.getElementById('point-save-btn');

        const perimetryHidden = document.getElementById('perimetry-hidden');
        const celluliteHidden = document.getElementById('cellulite-hidden');
        const flaccidityHidden = document.getElementById('flaccidity-hidden');
        const objectivesHidden = document.getElementById('objectives-hidden');
        const medicalHidden = document.getElementById('medical-history-hidden');
        const treatmentPlanHidden = document.getElementById('treatment-plan-hidden');

        const bmiDisplay = document.getElementById('bmi-display');
        const weightInput = document.querySelector('.js-weight');
        const heightInput = document.querySelector('.js-height');

        const flacTissue = document.getElementById('flac-tissue');
        const flacMuscle = document.getElementById('flac-muscle');
        const flacTissueLabel = document.getElementById('flac-tissue-label');
        const flacMuscleLabel = document.getElementById('flac-muscle-label');

        const pendingUploads = { count: 0 };
        let activeCameraTarget = null;
        let cameraStream = null;
        let activePointElement = null;

        function toggleSaveState() {
            const blocked = pendingUploads.count > 0;
            saveBtn.disabled = blocked;
            saveBtn.innerHTML = blocked ? '<i class="fas fa-spinner fa-spin me-1"></i>Enviando fotos...' : '<i class="fas fa-save me-1"></i>Salvar';
        }

        function calculateBMI() {
            const w = parseFloat(weightInput.value);
            const h = parseFloat(heightInput.value);
            if (!w || !h || h <= 0) {
                bmiDisplay.value = '';
                return;
            }
            const bmi = w / Math.pow(h / 100, 2);
            bmiDisplay.value = bmi.toFixed(1);
        }

        function syncPerimetry() {
            const data = {};
            document.querySelectorAll('.js-perimetry').forEach((input) => {
                const value = input.value.trim();
                if (value !== '') data[input.dataset.key] = parseFloat(value);
            });
            perimetryHidden.value = JSON.stringify(data);
            refreshMapMeasureValues();
        }

        function refreshMapMeasureValues() {
            document.querySelectorAll('.measure-point').forEach((point) => {
                const key = point.dataset.key;
                const input = document.querySelector(`.js-perimetry[data-key="${key}"]`);
                const valueLabel = document.getElementById('measure-val-' + key);
                if (!valueLabel) return;
                const value = input && input.value !== '' ? input.value : '-';
                valueLabel.textContent = value;
            });
        }

        function syncCellulite() {
            const types = Array.from(document.querySelectorAll('.js-cellulite-type:checked')).map((el) => el.value);
            celluliteHidden.value = JSON.stringify({
                grade: document.getElementById('cellulite-grade').value || null,
                types: types
            });
        }

        function syncFlaccidity() {
            flacTissueLabel.textContent = flacTissue.value;
            flacMuscleLabel.textContent = flacMuscle.value;
            flaccidityHidden.value = JSON.stringify({
                tissue: parseInt(flacTissue.value, 10),
                muscle: parseInt(flacMuscle.value, 10)
            });
        }

        function syncObjectives() {
            const values = Array.from(document.querySelectorAll('.js-objective:checked')).map((el) => el.value);
            objectivesHidden.value = JSON.stringify(values);
        }

        function syncMedicalHistory() {
            const values = Array.from(document.querySelectorAll('.js-medical:checked')).map((el) => el.value);
            medicalHidden.value = JSON.stringify(values);
        }

        function renderSelectedAreas() {
            const labels = [];
            document.querySelectorAll('.measure-point').forEach((area) => {
                const selected = selectedAreas.has(area.id);
                area.classList.toggle('is-selected', selected);
                if (selected) {
                    const key = area.dataset.key;
                    const measureInput = document.querySelector(`.js-perimetry[data-key="${key}"]`);
                    const measureValue = measureInput && measureInput.value !== '' ? measureInput.value + ' cm' : 'sem medida';
                    labels.push((area.dataset.label || area.id) + ': ' + measureValue);
                }
            });
            selectedAreasBox.innerHTML = labels.length
                ? labels.map((label) => '<span class="badge bg-danger me-1 mb-1">' + label + '</span>').join('')
                : '<small class="text-muted">Nenhum ponto marcado</small>';
            bodyAreasHidden.value = JSON.stringify(Array.from(selectedAreas));
        }

        function selectPoint(point) {
            activePointElement = point;
            const key = point.dataset.key;
            const label = point.dataset.label || key;
            const linkedInput = document.querySelector(`.js-perimetry[data-key="${key}"]`);
            pointSelectedLabel.textContent = label;
            pointSelectedLabel.classList.remove('text-muted');
            pointMeasureInput.value = linkedInput ? linkedInput.value : '';
            pointMeasureInput.focus();
        }

        function applyPointMeasure() {
            if (!activePointElement) return;
            const key = activePointElement.dataset.key;
            const linkedInput = document.querySelector(`.js-perimetry[data-key="${key}"]`);
            if (!linkedInput) return;
            linkedInput.value = pointMeasureInput.value;
            if (selectedAreas.has(activePointElement.id)) {
                // keep selected and just refresh
            } else {
                selectedAreas.add(activePointElement.id);
            }
            syncPerimetry();
            renderSelectedAreas();
        }

        function evolutionRowTemplate(data) {
            return '<tr class="js-evolution-row">'
                + '<td><input type="date" class="form-control form-control-sm js-evo-date" value="' + (data.date || '') + '"></td>'
                + '<td><input type="number" step="0.1" class="form-control form-control-sm js-evo-weight" value="' + (data.weight || '') + '"></td>'
                + '<td><input type="number" step="0.1" class="form-control form-control-sm js-evo-fat" value="' + (data.fat_percentage || '') + '"></td>'
                + '<td><input type="number" step="0.1" class="form-control form-control-sm js-evo-muscle" value="' + (data.muscle_mass || '') + '"></td>'
                + '<td><input type="text" class="form-control form-control-sm js-evo-measures" value="' + (data.measures || '') + '"></td>'
                + '<td><textarea class="form-control form-control-sm js-evo-notes" rows="1">' + (data.notes || '') + '</textarea></td>'
                + '<td><button type="button" class="btn btn-sm btn-danger js-remove-evolution"><i class="fas fa-trash"></i></button></td>'
                + '</tr>';
        }

        function syncEvolution() {
            const rows = [];
            document.querySelectorAll('.js-evolution-row').forEach((row) => {
                rows.push({
                    date: row.querySelector('.js-evo-date').value || null,
                    weight: row.querySelector('.js-evo-weight').value || null,
                    fat_percentage: row.querySelector('.js-evo-fat').value || null,
                    muscle_mass: row.querySelector('.js-evo-muscle').value || null,
                    measures: row.querySelector('.js-evo-measures').value || null,
                    notes: row.querySelector('.js-evo-notes').value || null
                });
            });
            evolutionHidden.value = JSON.stringify(rows);
        }

        function syncTreatmentPlan() {
            const lines = document.getElementById('treatment-plan-text').value
                .split('\n')
                .map((l) => l.trim())
                .filter((l) => l.length > 0);
            treatmentPlanHidden.value = JSON.stringify(lines);
        }

        async function uploadPhoto(file, target) {
            if (!file) return;

            pendingUploads.count += 1;
            toggleSaveState();

            const status = document.getElementById('status-' + target);
            status.textContent = 'Enviando...';
            status.className = 'text-warning d-block mt-1';

            try {
                const data = new FormData();
                data.append('file', file);
                data.append('folder', 'body-evaluations');
                data.append('prefix', String(Date.now()));

                const response = await fetch('{{ route("panel.uploads.store") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    body: data
                });

                const json = await response.json();
                if (!response.ok || json.error) throw new Error(json.message || 'Falha no upload');

                document.getElementById('preview-' + target).src = json.url;
                document.getElementById('hidden-' + target).value = json.url;
                status.textContent = 'Upload concluido';
                status.className = 'text-success d-block mt-1';
            } catch (error) {
                status.textContent = error.message || 'Erro no upload';
                status.className = 'text-danger d-block mt-1';
            } finally {
                pendingUploads.count -= 1;
                toggleSaveState();
            }
        }

        async function openCamera(target) {
            activeCameraTarget = target;
            const modalEl = document.getElementById('cameraModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            try {
                cameraStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
                const video = document.getElementById('cameraVideo');
                video.srcObject = cameraStream;
                video.play();
            } catch (error) {
                modal.hide();
                alert('Nao foi possivel abrir a camera.');
            }
        }

        function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach((t) => t.stop());
                cameraStream = null;
            }
        }

        document.querySelectorAll('.measure-point').forEach((area) => {
            area.addEventListener('click', function () {
                if (selectedAreas.has(area.id)) selectedAreas.delete(area.id);
                else selectedAreas.add(area.id);
                selectPoint(area);
                renderSelectedAreas();
            });
        });

        pointSaveBtn.addEventListener('click', applyPointMeasure);
        pointMeasureInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                applyPointMeasure();
            }
        });

        document.getElementById('add-evolution').addEventListener('click', function () {
            document.getElementById('evolution-tbody').insertAdjacentHTML('beforeend', evolutionRowTemplate({}));
            syncEvolution();
        });

        document.getElementById('evolution-tbody').addEventListener('click', function (e) {
            const button = e.target.closest('.js-remove-evolution');
            if (!button) return;
            button.closest('.js-evolution-row').remove();
            syncEvolution();
        });

        document.getElementById('evolution-tbody').addEventListener('input', syncEvolution);

        document.querySelectorAll('.js-photo-input').forEach((input) => {
            input.addEventListener('change', function () {
                uploadPhoto(input.files[0], input.dataset.target);
            });
        });

        document.querySelectorAll('.js-open-camera').forEach((btn) => {
            btn.addEventListener('click', function () {
                openCamera(btn.dataset.target);
            });
        });

        document.getElementById('capturePhotoBtn').addEventListener('click', function () {
            const video = document.getElementById('cameraVideo');
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0);

            canvas.toBlob(function (blob) {
                uploadPhoto(blob, activeCameraTarget);
                stopCamera();
                bootstrap.Modal.getInstance(document.getElementById('cameraModal')).hide();
            }, 'image/jpeg', 0.9);
        });

        document.getElementById('cameraModal').addEventListener('hidden.bs.modal', stopCamera);

        [weightInput, heightInput].forEach((el) => el.addEventListener('input', calculateBMI));
        document.querySelectorAll('.js-perimetry').forEach((el) => el.addEventListener('input', syncPerimetry));
        document.getElementById('cellulite-grade').addEventListener('change', syncCellulite);
        document.querySelectorAll('.js-cellulite-type').forEach((el) => el.addEventListener('change', syncCellulite));
        [flacTissue, flacMuscle].forEach((el) => el.addEventListener('input', syncFlaccidity));
        document.querySelectorAll('.js-objective').forEach((el) => el.addEventListener('change', syncObjectives));
        document.querySelectorAll('.js-medical').forEach((el) => el.addEventListener('change', syncMedicalHistory));

        form.addEventListener('submit', function (e) {
            if (pendingUploads.count > 0) {
                e.preventDefault();
                alert('Aguarde o termino do upload das fotos antes de salvar.');
                return;
            }

            syncPerimetry();
            syncCellulite();
            syncFlaccidity();
            syncObjectives();
            syncMedicalHistory();
            syncEvolution();
            syncTreatmentPlan();
            renderSelectedAreas();
        });

        if (Array.isArray(initialEvolution) && initialEvolution.length) {
            initialEvolution.forEach((item) => {
                document.getElementById('evolution-tbody').insertAdjacentHTML('beforeend', evolutionRowTemplate(item || {}));
            });
        }

        calculateBMI();
        syncPerimetry();
        syncCellulite();
        syncFlaccidity();
        syncObjectives();
        syncMedicalHistory();
        syncEvolution();
        syncTreatmentPlan();
        renderSelectedAreas();
    });
</script>
@endpush
@endsection
