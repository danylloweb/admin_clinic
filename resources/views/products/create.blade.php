@extends('layouts.header')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header  text-white text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Cadastro de Produto</h4>
            <a href="{{ route('panel.products.index') }}" class="btn btn-light btn-sm">Voltar</a>
        </div>
        <div class="card-body">
            <form id="productForm">
                @csrf
                <div id="form-config" data-url="{{ route('products.store') }}" data-method="POST"></div>

                <!-- CARD 1: Foto e Identificação -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white ">
                        <h5 class="mb-0"><i class="ph ph-image me-2"></i>Foto e Identificação</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Foto Principal</label>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <button type="button" id="btn-open-camera" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#cameraModal">Abrir câmera</button>
                                    <button type="button" id="btn-pick-file" class="btn btn-light btn-sm">Selecionar arquivo</button>
                                </div>

                                <img id="preview-image" class="img-fluid rounded mt-2 border" style="max-height: 200px; display: none;" src="" alt="Preview">

                                <input type="file" id="image_primary_file" class="d-none" accept="image/jpeg,image/png,image/webp,image/heic,image/heif">
                                <input type="hidden" name="image_url" id="image_url">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Código Interno *</label>
                                <input type="text" name="internal_code" id="internal_code" class="form-control" required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Código de Barras (EAN)</label>
                                <input type="text" name="ean_code" id="ean_code" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="1" selected>Ativo</option>
                                    <option value="0">Inativo</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome do Produto *</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nome Comercial</label>
                                <input type="text" name="trade_name" id="trade_name" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Marca/Fabricante</label>
                                <input type="text" name="brand" id="brand" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Registro ANVISA</label>
                                <input type="text" name="anvisa_registration" id="anvisa_registration" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Processo ANVISA</label>
                                <input type="text" name="anvisa_process" id="anvisa_process" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Subcategoria</label>
                                <input type="text" name="subcategory" id="subcategory" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição Detalhada</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: Classificação -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-tag me-2"></i>Classificação do Produto</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo de Produto *</label>
                                <select name="category_type" id="category_type" class="form-select" required>
                                    <option value="">Selecione...</option>
                                    <option value="cosmetic">Cosmético</option>
                                    <option value="dermocosmetic">Dermocosmético</option>
                                    <option value="medicine">Medicamento</option>
                                    <option value="botulinum_toxin">Toxina Botulínica</option>
                                    <option value="filler">Preenchedor</option>
                                    <option value="biostimulator">Bioestimulador</option>
                                    <option value="enzyme">Enzimas</option>
                                    <option value="equipment">Equipamento</option>
                                    <option value="disposable_material">Material Descartável</option>
                                    <option value="consumable_material">Material de Consumo</option>
                                    <option value="input">Insumo</option>
                                    <option value="other">Outro</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unidade de Medida *</label>
                                <select name="unit_measure" id="unit_measure" class="form-select" required>
                                    <option value="unit">Unidade</option>
                                    <option value="box">Caixa</option>
                                    <option value="ampule">Ampola</option>
                                    <option value="flask">Frasco</option>
                                    <option value="syringe">Seringa</option>
                                    <option value="ml">ml</option>
                                    <option value="mg">mg</option>
                                    <option value="g">g</option>
                                    <option value="kg">kg</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="requires_batch_tracking" name="requires_batch_tracking" value="1" checked>
                                    <label class="form-check-label" for="requires_batch_tracking">Controla Lote</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="requires_expiration_tracking" name="requires_expiration_tracking" value="1" checked>
                                    <label class="form-check-label" for="requires_expiration_tracking">Controla Validade</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_injectable" name="is_injectable" value="1">
                                    <label class="form-check-label" for="is_injectable">Produto Injetável</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="requires_refrigeration" name="requires_refrigeration" value="1">
                                    <label class="form-check-label" for="requires_refrigeration">Produto Refrigerado</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="requires_patient_tracking" name="requires_patient_tracking" value="1">
                                    <label class="form-check-label" for="requires_patient_tracking">Necessita Rastreabilidade por Paciente</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: Controle de Estoque -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-warehouse me-2"></i>Controle de Estoque</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Estoque Mínimo</label>
                                <input type="number" name="minimum_stock" id="minimum_stock" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Estoque Ideal</label>
                                <input type="number" name="ideal_stock" id="ideal_stock" class="form-control" value="0" min="0">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Estoque Atual (somente leitura)</label>
                                <input type="number" name="current_stock" id="current_stock" class="form-control" value="0" readonly>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Estoque Reservado</label>
                                <input type="number" name="reserved_stock" id="reserved_stock" class="form-control" value="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 4: Dados de Compra -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-credit-card me-2"></i>Dados de Compra</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fornecedor</label>
                                <select name="supplier_id" id="supplier_id" class="form-select">
                                    <option value="">Selecione...</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Número da Nota Fiscal</label>
                                <input type="text" name="invoice_number" id="invoice_number" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Data de Compra</label>
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Data de Recebimento</label>
                                <input type="date" name="receipt_date" id="receipt_date" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Valor Unitário (R$)</label>
                                <input type="number" step="0.01" name="unit_value" id="unit_value" class="form-control" value="0.00">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Valor de Venda (R$)</label>
                                <input type="number" step="0.01" name="sale_value" id="sale_value" class="form-control" value="0.00">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Margem de Lucro (%)</label>
                                <input type="number" step="0.01" name="profit_margin" id="profit_margin" class="form-control" value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 5: Armazenamento -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-building me-2"></i>Armazenamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Localização Física</label>
                                <input type="text" name="storage_location" id="storage_location" class="form-control" placeholder="ex: Geladeira 01">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Corredor</label>
                                <input type="text" name="aisle" id="aisle" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Armário</label>
                                <input type="text" name="cabinet" id="cabinet" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Prateleira</label>
                                <input type="text" name="shelf" id="shelf" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Temperatura Mínima (°C)</label>
                                <input type="number" step="0.1" name="min_temperature" id="min_temperature" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Temperatura Máxima (°C)</label>
                                <input type="number" step="0.1" name="max_temperature" id="max_temperature" class="form-control">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Umidade Ideal (%)</label>
                                <input type="number" name="ideal_humidity" id="ideal_humidity" class="form-control" min="0" max="100">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 6: Lotes (será dinâmico) -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-list me-2"></i>Lotes (será preenchido após criar o produto)</h5>
                    </div>
                    <div class="card-body text-muted">
                        Lotes podem ser adicionados após a criação do produto.
                    </div>
                </div>

                <!-- CARD 7: Documentação (será dinâmico) -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-file me-2"></i>Documentação (será preenchido após criar o produto)</h5>
                    </div>
                    <div class="card-body text-muted">
                        Documentos podem ser adicionados após a criação do produto.
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="ph ph-floppy-disk me-2"></i>Criar Produto
                    </button>
                    <a href="{{ route('panel.products.index') }}" class="btn btn-secondary btn-lg">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cameraModalLabel">Capturar Foto Principal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <button type="button" id="btn-switch-camera" class="btn btn-outline-secondary btn-sm">Trocar câmera</button>
                    <button type="button" id="btn-capture-photo" class="btn btn-success btn-sm" disabled>Capturar</button>
                    <button type="button" id="btn-retake-photo" class="btn btn-warning btn-sm" disabled>Refazer</button>
                </div>

                <video id="camera-preview" class="w-100 rounded border d-none" playsinline autoplay muted></video>
                <canvas id="camera-canvas" class="d-none"></canvas>

                <div class="alert alert-info mt-3 mb-0">
                    Você pode usar a câmera traseira ou frontal. Após capturar, a imagem será enviada e a URL pública será salva no produto.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('productForm');
        const cameraModalEl = document.getElementById('cameraModal');
        const openCameraBtn = document.getElementById('btn-open-camera');
        const switchCameraBtn = document.getElementById('btn-switch-camera');
        const capturePhotoBtn = document.getElementById('btn-capture-photo');
        const retakePhotoBtn = document.getElementById('btn-retake-photo');
        const pickFileBtn = document.getElementById('btn-pick-file');
        const fileInput = document.getElementById('image_primary_file');
        const video = document.getElementById('camera-preview');
        const canvas = document.getElementById('camera-canvas');
        const imagePreview = document.getElementById('preview-image');
        const imageUrlInput = document.getElementById('image_url');
        const cameraModal = cameraModalEl ? bootstrap.Modal.getOrCreateInstance(cameraModalEl) : null;
        let cameraStream = null;
        let currentFacingMode = 'environment';

        // Load suppliers
        loadSuppliers();

        async function stopCamera() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(track => track.stop());
                cameraStream = null;
            }

            video.srcObject = null;
            video.classList.add('d-none');
            capturePhotoBtn.disabled = true;
            switchCameraBtn.disabled = true;
        }

        async function startCamera() {
            try {
                await stopCamera();

                cameraStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: currentFacingMode },
                    audio: false
                });

                video.srcObject = cameraStream;
                video.classList.remove('d-none');
                capturePhotoBtn.disabled = false;
                switchCameraBtn.disabled = false;
                retakePhotoBtn.disabled = true;
            } catch (error) {
                console.error(error);
                showToast('Não foi possível acessar a câmera. Use o seletor de arquivo.', 'danger');
            }
        }

        async function uploadImageFile(file) {
            const formData = new FormData();
            formData.append('file', file, file.name || 'product-image.jpg');
            formData.append('folder', 'products');
            formData.append('prefix', 'primary-image');

            const response = await fetch('{{ route("panel.uploads.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();
            if (!response.ok || data.error) {
                throw new Error(data.message || 'Erro ao enviar imagem.');
            }

            return data.url;
        }

        async function setUploadedPreview(file) {
            const url = await uploadImageFile(file);
            imageUrlInput.value = url;
            imagePreview.src = url;
            imagePreview.style.display = 'block';
            retakePhotoBtn.disabled = false;
            return url;
        }

        function cleanupBackdrops() {
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        }

        if (cameraModalEl) {
            openCameraBtn.addEventListener('click', function () {
                cleanupBackdrops();
                cameraModal?.show();
            });

            cameraModalEl.addEventListener('shown.bs.modal', async function () {
                cleanupBackdrops();
                await startCamera();
            });

            cameraModalEl.addEventListener('hidden.bs.modal', async function () {
                await stopCamera();
                cleanupBackdrops();
            });
        }

        switchCameraBtn.addEventListener('click', async function () {
            currentFacingMode = currentFacingMode === 'environment' ? 'user' : 'environment';
            await startCamera();
        });

        pickFileBtn.addEventListener('click', function () {
            fileInput.click();
        });

        fileInput.addEventListener('change', async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            try {
                await stopCamera();
                await setUploadedPreview(file);
                showToast('Foto enviada com sucesso.', 'success');
            } catch (error) {
                console.error(error);
                showToast(error.message || 'Erro ao enviar a foto.', 'danger');
            }
        });

        capturePhotoBtn.addEventListener('click', function () {
            if (!video.srcObject) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            const context = canvas.getContext('2d');
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            canvas.toBlob(async function (blob) {
                if (!blob) {
                    showToast('Erro ao capturar imagem.', 'danger');
                    return;
                }

                try {
                    const file = new File([blob], 'product-image.jpg', { type: 'image/jpeg' });
                    await stopCamera();
                    await setUploadedPreview(file);
                    showToast('Foto capturada e enviada com sucesso.', 'success');
                    cameraModal?.hide();
                } catch (error) {
                    console.error(error);
                    showToast(error.message || 'Erro ao enviar a foto.', 'danger');
                }
            }, 'image/jpeg', 0.92);
        });

        retakePhotoBtn.addEventListener('click', async function () {
            imageUrlInput.value = '';
            imagePreview.src = '';
            imagePreview.style.display = 'none';
            fileInput.value = '';
            retakePhotoBtn.disabled = true;
            await startCamera();
        });

        // Form submission
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!imageUrlInput.value) {
                showToast('Envie ou capture a foto principal antes de salvar.', 'danger');
                return;
            }

            const formData = new FormData();
            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                if (input.type === 'checkbox') {
                    formData.append(input.name, input.checked ? 1 : 0);
                } else if (input.type !== 'file' && input.name) {
                    formData.append(input.name, input.value);
                }
            });

            try {
                const response = await fetch('/products', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    showToast('Produto criado com sucesso', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("panel.products.show", ":id") }}'.replace(':id', data.id)
                    }, 1000);
                } else if (response.status === 422) {
                    const errors = await response.json();
                    let message = 'Erro ao criar produto:\n';
                    if (errors.errors) {
                        Object.keys(errors.errors).forEach(key => {
                            message += errors.errors[key][0] + '\n';
                        });
                    }
                    showToast(message, 'danger');
                } else {
                    showToast('Erro ao criar produto', 'danger');
                }
            } catch (error) {
                showToast('Erro de rede: ' + error.message, 'danger');
            }
        });

        function loadSuppliers() {
            fetch('/suppliers?limit=1000', { credentials: 'same-origin' })
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('supplier_id');
                    let items = data.data || [];
                    if (!Array.isArray(items)) items = [];

                    items.forEach(supplier => {
                        const option = document.createElement('option');
                        option.value = supplier.id;
                        option.textContent = supplier.name;
                        select.appendChild(option);
                    });
                })
                .catch(err => console.error('Erro ao carregar fornecedores:', err));
        }
    });
</script>
@endpush

