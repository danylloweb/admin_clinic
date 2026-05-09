@extends('layouts.header')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-checklist me-2"></i>Cadastro de Avaliação Facial
                        </h4>

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
                        <div class="card mb-4 border-2">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-user me-2"></i>Dados do Paciente</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nome Completo</label>
                                        <input type="text" class="form-control" value="{{ $patient->name ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Data de Nascimento</label>
                                        <input type="date" class="form-control" value="{{ $patient->birth_date ?? '' }}" readonly>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Telefone</label>
                                        <input type="text" class="form-control phone-mask" value="{{ $patient->phone ?? '' }}" readonly>
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
                                <textarea class="form-control" name="chief_complaint" rows="4" placeholder="Descreva a principal queixa ou motivo da avaliação..."></textarea>
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
                                            <input class="form-check-input" type="radio" name="skin_type" id="skin_{{ $value }}" value="{{ $value }}">
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
                                            <input type="range" class="form-range" name="oiliness" min="0" max="10" value="5" id="oiliness-slider">
                                            <span class="badge bg-primary" id="oiliness-value">5</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Hidratação (0-10)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="range" class="form-range" name="hydration" min="0" max="10" value="5" id="hydration-slider">
                                            <span class="badge bg-primary" id="hydration-value">5</span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Sensibilidade (0-10)</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="range" class="form-range" name="sensitivity" min="0" max="10" value="5" id="sensitivity-slider">
                                            <span class="badge bg-primary" id="sensitivity-value">5</span>
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
                                            <input class="form-check-input problem-check" type="checkbox" name="{{ $field }}" id="{{ $field }}" value="1">
                                            <label class="form-check-label" for="{{ $field }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                        <div id="{{ $field }}-notes" class="mt-2" style="display:none;">
                                            <textarea class="form-control form-control-sm" name="{{ $field }}_notes" placeholder="Observações..." rows="2"></textarea>
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
                                    <option value="{{ $type }}">
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
                                                >
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
                                <textarea class="form-control" name="allergies" rows="3" placeholder="Descreva qualquer alergia conhecida..."></textarea>
                            </div>
                        </div>

                        <!-- Seção: Medicamentos em Uso -->
                        <div class="card mb-4 border-2 border-warning">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-pills me-2"></i>Medicamentos em Uso</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="medications_in_use" rows="3" placeholder="Liste os medicamentos em uso..."></textarea>
                            </div>
                        </div>

                        <!-- Seção: Objetivo do Paciente -->
                        <div class="card mb-4 border-2 border-success">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i>Objetivo do Paciente</h5>
                            </div>
                            <div class="card-body">
                                <textarea class="form-control" name="patient_objective" rows="3" placeholder="Qual é o objetivo do paciente com o tratamento?"></textarea>
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
                                        <input type="text" class="form-control" name="treatment_procedure" placeholder="Ex: Microagulhamento">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Quantidade de Sessões</label>
                                        <input type="number" class="form-control" name="treatment_sessions" placeholder="0">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Frequência</label>
                                        <input type="text" class="form-control" name="treatment_frequency" placeholder="Ex: 1x por semana">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Observações</label>
                                        <input type="text" class="form-control" name="treatment_observations" placeholder="Observações adicionais">
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
                                        <input type="file" class="form-control photo-input" data-target="front" accept="image/*" capture="user">
                                        <input type="hidden" name="photo_front" id="photo-front-base64">
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 open-camera-btn" data-target="front">
                                            <i class="fas fa-camera me-1"></i>Abrir camera
                                        </button>
                                        <img id="preview-front" class="img-fluid mt-2 rounded" style="max-height: 200px; display: none;" src="" alt="Foto frontal">
                                        <small id="upload-status-front" class="d-block mt-2 text-muted"></small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Perfil Direito</label>
                                        <input type="file" class="form-control photo-input" data-target="right" accept="image/*" capture="user">
                                        <input type="hidden" name="photo_profile_right" id="photo-right-base64">
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 open-camera-btn" data-target="right">
                                            <i class="fas fa-camera me-1"></i>Abrir camera
                                        </button>
                                        <img id="preview-right" class="img-fluid mt-2 rounded" style="max-height: 200px; display: none;" src="" alt="Perfil direito">
                                        <small id="upload-status-right" class="d-block mt-2 text-muted"></small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Perfil Esquerdo</label>
                                        <input type="file" class="form-control photo-input" data-target="left" accept="image/*" capture="user">
                                        <input type="hidden" name="photo_profile_left" id="photo-left-base64">
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2 open-camera-btn" data-target="left">
                                            <i class="fas fa-camera me-1"></i>Abrir camera
                                        </button>
                                        <img id="preview-left" class="img-fluid mt-2 rounded" style="max-height: 200px; display: none;" src="" alt="Perfil esquerdo">
                                        <small id="upload-status-left" class="d-block mt-2 text-muted"></small>
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
                                    <input class="form-check-input" type="checkbox" name="consent_accepted" id="consent" value="1">
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
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="camera-capture-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen-lg-down" style="max-width: 95vw; width: 95vw;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-camera me-2"></i>Capturar foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body text-center">
                <video id="camera-video" autoplay playsinline class="w-100 rounded" style="height: 78vh; max-height: 78vh; object-fit: cover; background: #111;"></video>
                <canvas id="camera-canvas" class="d-none"></canvas>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="capture-photo-btn">
                    <i class="fas fa-camera me-1"></i>Capturar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.querySelector('.cpf-mask');
        const hasIMask = typeof window.IMask !== 'undefined';

        const uploadEndpoint = '{{ route("panel.uploads.store") }}';
        const saveBtn = document.getElementById('saveBtn');
        const modalEl = document.getElementById('camera-capture-modal');
        const cameraVideo = document.getElementById('camera-video');
        const cameraCanvas = document.getElementById('camera-canvas');
        const captureBtn = document.getElementById('capture-photo-btn');
        const cameraModal = (window.bootstrap && modalEl) ? new bootstrap.Modal(modalEl) : null;

        let stream = null;
        let activeTarget = null;
        let pendingUploads = 0;

        const photoConfig = {
            front: { previewId: 'preview-front', hiddenId: 'photo-front-base64', statusId: 'upload-status-front' },
            right: { previewId: 'preview-right', hiddenId: 'photo-right-base64', statusId: 'upload-status-right' },
            left: { previewId: 'preview-left', hiddenId: 'photo-left-base64', statusId: 'upload-status-left' },
        };

        function onlyDigits(value) {
            return String(value || '').replace(/\D/g, '');
        }

        function formatCpf(value) {
            const v = onlyDigits(value).slice(0, 11);
            if (v.length <= 3) return v;
            if (v.length <= 6) return `${v.slice(0, 3)}.${v.slice(3)}`;
            if (v.length <= 9) return `${v.slice(0, 3)}.${v.slice(3, 6)}.${v.slice(6)}`;
            return `${v.slice(0, 3)}.${v.slice(3, 6)}.${v.slice(6, 9)}-${v.slice(9)}`;
        }

        function formatPhone(value) {
            const v = onlyDigits(value).slice(0, 11);
            if (v.length <= 2) return `(${v}`;
            if (v.length <= 7) return `(${v.slice(0, 2)}) ${v.slice(2)}`;
            return `(${v.slice(0, 2)}) ${v.slice(2, 7)}-${v.slice(7)}`;
        }

        function normalizeModalLayering() {
            if (!modalEl) return;
            if (modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }
            modalEl.style.zIndex = '1060';
            document.querySelectorAll('.modal-backdrop').forEach((b) => {
                b.style.zIndex = '1050';
            });
        }

        function cleanupModalArtifacts() {
            document.querySelectorAll('.modal-backdrop').forEach((b) => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }

        function setPhotoPreview(target, src) {
            const config = photoConfig[target];
            if (!config) return;

            const preview = document.getElementById(config.previewId);
            if (preview) {
                preview.src = src || '';
                preview.style.display = src ? 'block' : 'none';
            }
        }

        function setPhotoUrl(target, url) {
            const config = photoConfig[target];
            if (!config) return;

            const hidden = document.getElementById(config.hiddenId);
            if (hidden) hidden.value = url || '';
        }

        function setPhotoStatus(target, text = '', type = 'muted') {
            const config = photoConfig[target];
            if (!config) return;

            const el = document.getElementById(config.statusId);
            if (!el) return;

            el.className = `d-block mt-2 text-${type}`;
            el.textContent = text;
        }

        function updateSaveState() {
            if (!saveBtn) return;

            if (pendingUploads > 0) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enviando arquivos...';
                return;
            }

            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save me-2"></i>Salvar Ficha';
        }

        async function uploadPhotoFile(target, file) {
            if (!file) {
                setPhotoUrl(target, '');
                setPhotoPreview(target, '');
                setPhotoStatus(target, '');
                return null;
            }

            const tempUrl = URL.createObjectURL(file);
            setPhotoPreview(target, tempUrl);
            setPhotoStatus(target, 'Enviando arquivo...', 'warning');

            const formData = new FormData();
            formData.append('file', file);
            formData.append('folder', 'facial-evaluations');
            formData.append('prefix', target);

            pendingUploads++;
            updateSaveState();

            try {
                const response = await fetch(uploadEndpoint, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData,
                    credentials: 'same-origin'
                });

                const result = await response.json().catch(() => ({}));
                if (!response.ok || result.error) {
                    throw new Error(result.message || 'Erro ao enviar arquivo.');
                }

                setPhotoUrl(target, result.url);
                setPhotoPreview(target, result.url);
                setPhotoStatus(target, 'Arquivo enviado com sucesso.', 'success');
                return result.url;
            } catch (error) {
                setPhotoUrl(target, '');
                setPhotoPreview(target, '');
                setPhotoStatus(target, error.message || 'Erro ao enviar arquivo.', 'danger');
                showToast(error.message || 'Erro ao enviar arquivo.', 'danger');
                return null;
            } finally {
                pendingUploads--;
                updateSaveState();
                URL.revokeObjectURL(tempUrl);
            }
        }

        normalizeModalLayering();

        if (cpfInput) {
            if (hasIMask) {
                new IMask(cpfInput, { mask: '000.000.000-00' });
            } else {
                cpfInput.addEventListener('input', (e) => {
                    e.target.value = formatCpf(e.target.value);
                });
            }
        }

        document.querySelectorAll('.phone-mask').forEach((el) => {
            if (hasIMask) {
                new IMask(el, { mask: '(00) 00000-0000' });
            } else {
                el.addEventListener('input', (e) => {
                    e.target.value = formatPhone(e.target.value);
                });
            }
        });

        document.querySelectorAll('.photo-input').forEach((input) => {
            input.addEventListener('change', async function (e) {
                const target = e.currentTarget.dataset.target;
                const file = e.currentTarget.files?.[0];

                if (!file) {
                    setPhotoUrl(target, '');
                    setPhotoPreview(target, '');
                    setPhotoStatus(target, '');
                    return;
                }

                await uploadPhotoFile(target, file);
            });
        });

        function openFilePicker(target) {
            const input = document.querySelector(`.photo-input[data-target="${target}"]`);
            if (input) input.click();
        }

        async function openCamera(target) {
            activeTarget = target;

            if (!window.isSecureContext || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                showToast('Camera indisponivel neste ambiente. Vou abrir a galeria/arquivo.', 'warning');
                openFilePicker(target);
                return;
            }

            if (!cameraModal || !cameraVideo) {
                showToast('Nao foi possivel abrir o modal da camera. Vou abrir a galeria/arquivo.', 'warning');
                openFilePicker(target);
                return;
            }

            try {
                if (stream) {
                    stream.getTracks().forEach((track) => track.stop());
                }

                cleanupModalArtifacts();
                normalizeModalLayering();

                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'user' } },
                        audio: false,
                    });
                } catch (_) {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: false,
                    });
                }

                cameraVideo.srcObject = stream;
                cameraModal.show();
                normalizeModalLayering();
            } catch (error) {
                showToast('Nao foi possivel acessar a camera. Vou abrir a galeria/arquivo.', 'danger');
                openFilePicker(target);
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach((track) => track.stop());
                stream = null;
            }
            cameraVideo.srcObject = null;
        }

        document.querySelectorAll('.open-camera-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                openCamera(this.dataset.target);
            });
        });

        captureBtn.addEventListener('click', function () {
            if (!activeTarget || !cameraVideo.videoWidth || !cameraVideo.videoHeight) {
                showToast('Camera nao esta pronta para captura.', 'warning');
                return;
            }

            cameraCanvas.width = cameraVideo.videoWidth;
            cameraCanvas.height = cameraVideo.videoHeight;

            const ctx = cameraCanvas.getContext('2d');
            ctx.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);

            cameraCanvas.toBlob(async function (blob) {
                if (!blob) {
                    showToast('Nao foi possivel capturar a imagem.', 'danger');
                    return;
                }

                const file = new File([blob], `${activeTarget}-${Date.now()}.jpg`, { type: 'image/jpeg' });
                cameraModal.hide();
                await uploadPhotoFile(activeTarget, file);
            }, 'image/jpeg', 0.9);
        });

        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', normalizeModalLayering);
            modalEl.addEventListener('hidden.bs.modal', function () {
                stopCamera();
                activeTarget = null;
                cleanupModalArtifacts();
            });
        }

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

        // Submit do formulario
        document.getElementById('facialEvaluationForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            await saveFacialEvaluation();
        });


        async function saveFacialEvaluation() {
            if (pendingUploads > 0) {
                showToast('Aguarde o envio das imagens antes de salvar.', 'warning');
                return;
            }

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
                acne: !!formData.get('acne'),
                acne_notes: formData.get('acne_notes'),
                melasma: !!formData.get('melasma'),
                melasma_notes: formData.get('melasma_notes'),
                wrinkles: !!formData.get('wrinkles'),
                wrinkles_notes: formData.get('wrinkles_notes'),
                flaccidity: !!formData.get('flaccidity'),
                flaccidity_notes: formData.get('flaccidity_notes'),
                spots: !!formData.get('spots'),
                spots_notes: formData.get('spots_notes'),
                dilated_pores: !!formData.get('dilated_pores'),
                dilated_pores_notes: formData.get('dilated_pores_notes'),
                fitzpatrick_type: formData.get('fitzpatrick_type'),
                aesthetic_history: aestheticHistory,
                allergies: formData.get('allergies'),
                medications_in_use: formData.get('medications_in_use'),
                patient_objective: formData.get('patient_objective'),
                treatment_plan: treatmentPlan,
                consent_accepted: !!formData.get('consent_accepted'),
                photo_front: formData.get('photo_front') || null,
                photo_profile_right: formData.get('photo_profile_right') || null,
                photo_profile_left: formData.get('photo_profile_left') || null,
            };

            try {
                const url = '{{ route("panel.facial-evaluations.store") }}';
                const method = 'POST';

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

