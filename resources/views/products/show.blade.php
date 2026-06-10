@extends('layouts.header')
@section('content')
<div class="container-fluid py-4">
    <div id="product-container" style="display: none;">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><span id="product-name">Carregando...</span></h4>
                <div class="d-flex gap-2">
                    <button class="btn btn-light btn-sm" onclick="editProduct()">Editar</button>
                    <a href="{{ route('panel.products.index') }}" class="btn btn-light btn-sm">Voltar</a>
                </div>
            </div>
            <div class="card-body">
                <!-- Alerts -->
                <div id="alerts-container"></div>

                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab-info">Informações</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-lots">Lotes</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-movements">Movimentações</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-consumption">Consumo</a></li>
                    <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab-documents">Documentos</a></li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content">
                    <!-- TAB 1: Informações -->
                    <div id="tab-info" class="tab-pane fade show active">
                        <div class="row">
                            <div class="col-md-3">
                                <img id="product-image" class="img-fluid rounded border" src="" alt="Produto">
                            </div>
                            <div class="col-md-9">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Código Interno:</strong>
                                        <p id="info-internal_code">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>EAN:</strong>
                                        <p id="info-ean_code">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Categoria:</strong>
                                        <p id="info-category_label">-</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <strong>Marca:</strong>
                                        <p id="info-brand">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Registro ANVISA:</strong>
                                        <p id="info-anvisa_registration">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Status:</strong>
                                        <p><span id="info-status" class="badge">-</span></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Estoque Atual:</strong>
                                        <p><span id="info-current_stock" class="badge bg-info">0</span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Estoque Disponível:</strong>
                                        <p><span id="info-available_stock" class="badge bg-success">0</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Rastreabilidade:</strong>
                                <ul id="tracking-info" class="list-unstyled mt-2"></ul>
                            </div>
                            <div class="col-md-6">
                                <strong>Armazenamento:</strong>
                                <ul id="storage-info" class="list-unstyled mt-2"></ul>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Lotes -->
                    <div id="tab-lots" class="tab-pane fade">
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#lotModal">
                                <i class="ph ph-plus me-2"></i>Adicionar Lote
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="lots-table">
                                <thead>
                                    <tr>
                                        <th>Lote</th>
                                        <th>Fabricação</th>
                                        <th>Validade</th>
                                        <th>Quantidade</th>
                                        <th>Disponível</th>
                                        <th>Data Recebimento</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: Movimentações -->
                    <div id="tab-movements" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" id="movements-table">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Tipo</th>
                                        <th>Lote</th>
                                        <th>Quantidade</th>
                                        <th>Usuário</th>
                                        <th>Observação</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 4: Consumo por Paciente -->
                    <div id="tab-consumption" class="tab-pane fade">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-sm" id="consumption-table">
                                <thead>
                                    <tr>
                                        <th>Paciente</th>
                                        <th>Procedimento</th>
                                        <th>Profissional</th>
                                        <th>Lote</th>
                                        <th>Quantidade</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 5: Documentação -->
                    <div id="tab-documents" class="tab-pane fade">
                        <div class="mb-3">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#documentModal">
                                <i class="ph ph-upload me-2"></i>Enviar Documento
                            </button>
                        </div>
                        <div class="row" id="documents-container"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Lote -->
    <div class="modal fade" id="lotModal" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="lotForm">
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="lot_product_id">
                        <div class="mb-3">
                            <label class="form-label">Número do Lote *</label>
                            <input type="text" name="batch_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Fabricação</label>
                            <input type="date" name="manufacture_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Validade *</label>
                            <input type="date" name="expiration_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade Recebida *</label>
                            <input type="number" name="quantity_received" class="form-control" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data de Recebimento *</label>
                            <input type="date" name="received_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Adicionar Lote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Documento -->
    <div class="modal fade" id="documentModal" tabindex="-1" data-bs-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="documentForm">
                    <div class="modal-body">
                        <input type="hidden" name="product_id" id="doc_product_id">
                        <div class="mb-3">
                            <label class="form-label">Tipo de Documento *</label>
                            <select name="document_type" class="form-select" required>
                                <option value="">Selecione...</option>
                                <option value="package">Foto da Embalagem</option>
                                <option value="batch">Foto do Lote</option>
                                <option value="invoice">Foto da Nota Fiscal</option>
                                <option value="leaflet">Bula</option>
                                <option value="certificate">Certificado ANVISA</option>
                                <option value="manual">Manual Técnico</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Arquivo *</label>
                            <input type="file" name="document" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="description" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar Documento</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productId = {{ $productId }};

    document.addEventListener('DOMContentLoaded', function() {
        initializeModalGuards();
        loadProductDetails();
        setupLotsForm();
        setupDocumentForm();
    });

    function cleanupOrphanBackdrops() {
        const visibleModals = document.querySelectorAll('.modal.show').length;
        if (visibleModals > 0) {
            return;
        }

        document.querySelectorAll('.modal-backdrop').forEach((backdrop) => backdrop.remove());
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('padding-right');
        document.body.style.removeProperty('overflow');
    }

    function initializeModalGuards() {
        ['lotModal', 'documentModal'].forEach((modalId) => {
            const modalEl = document.getElementById(modalId);
            if (!modalEl) {
                return;
            }

            // Force no backdrop to avoid orphan overlay blocking interaction.
            bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: false });

            modalEl.addEventListener('show.bs.modal', cleanupOrphanBackdrops);
            modalEl.addEventListener('hidden.bs.modal', cleanupOrphanBackdrops);
        });
    }

    async function loadProductDetails() {
        try {
            const response = await fetch(`/products/${productId}`);
            if (!response.ok) {
                showToast('Produto não encontrado', 'danger');
                return;
            }

            const data = await response.json();
            const product = data.data ? data.data : data;

            displayProductInfo(product);
            loadAlerts();
            loadLots();
            loadMovements();
            loadConsumptions();

            document.getElementById('product-container').style.display = 'block';
        } catch (error) {
            showToast('Erro ao carregar produto: ' + error.message, 'danger');
        }
    }

    function displayProductInfo(product) {
        document.getElementById('product-name').textContent = product.name;
        const productImage = document.getElementById('product-image');
        if (product.image_url) {
            productImage.src = product.image_url;
            productImage.style.display = 'block';
        } else {
            productImage.removeAttribute('src');
            productImage.style.display = 'none';
        }
        document.getElementById('info-internal_code').textContent = product.internal_code;
        document.getElementById('info-ean_code').textContent = product.ean_code || '-';
        document.getElementById('info-category_label').textContent = product.category_label;
        document.getElementById('info-brand').textContent = product.brand || '-';
        document.getElementById('info-anvisa_registration').textContent = product.anvisa_registration || '-';
        document.getElementById('info-current_stock').textContent = product.current_stock;
        document.getElementById('info-available_stock').textContent = product.available_stock;

        const statusBadge = product.status ? 'badge bg-success' : 'badge bg-danger';
        const statusText = product.status ? 'Ativo' : 'Inativo';
        document.getElementById('info-status').className = statusBadge;
        document.getElementById('info-status').textContent = statusText;

        // Tracking info
        const trackingList = document.getElementById('tracking-info');
        trackingList.innerHTML = `
            <li><strong>Controla Lote:</strong> ${product.requires_batch_tracking ? 'Sim' : 'Não'}</li>
            <li><strong>Controla Validade:</strong> ${product.requires_expiration_tracking ? 'Sim' : 'Não'}</li>
            <li><strong>Injetável:</strong> ${product.is_injectable ? 'Sim' : 'Não'}</li>
            <li><strong>Rastreabilidade por Paciente:</strong> ${product.requires_patient_tracking ? 'Sim' : 'Não'}</li>
        `;

        // Storage info
        const storageList = document.getElementById('storage-info');
        storageList.innerHTML = `
            <li><strong>Localização:</strong> ${product.storage_location || '-'}</li>
            <li><strong>Refrigerado:</strong> ${product.requires_refrigeration ? 'Sim' : 'Não'}</li>
            <li><strong>Temperatura:</strong> ${product.min_temperature || '-'}°C a ${product.max_temperature || '-'}°C</li>
            <li><strong>Umidade Ideal:</strong> ${product.ideal_humidity || '-'}%</li>
        `;

        document.getElementById('lot_product_id').value = productId;
        document.getElementById('doc_product_id').value = productId;
    }

    async function loadAlerts() {
        try {
            const response = await fetch(`/products/${productId}/alerts`);
            const data = await response.json();
            const alertsContainer = document.getElementById('alerts-container');

            if (data.alerts && data.alerts.length > 0) {
                alertsContainer.innerHTML = data.alerts.map(alert => `
                    <div class="alert alert-${alert.severity} alert-dismissible fade show" role="alert">
                        <strong>${alert.type}:</strong> ${alert.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                `).join('');
            }
        } catch (error) {
            console.error('Erro ao carregar alertas:', error);
        }
    }

    async function loadLots() {
        try {
            const response = await fetch(`/products/${productId}/lots`);
            const data = await response.json();
            const tbody = document.querySelector('#lots-table tbody');

            const lots = Array.isArray(data.data) ? data.data : [];

            if (lots.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Nenhum lote cadastrado.</td></tr>';
                return;
            }

            tbody.innerHTML = lots.map(lot => {
                const badgeClass = lot.status === 'expired'
                    ? 'badge bg-danger'
                    : lot.status === 'near_expiration'
                        ? 'badge bg-warning text-dark'
                        : lot.status === 'low_stock'
                            ? 'badge bg-secondary'
                            : 'badge bg-success';

                const statusLabel = lot.status === 'expired'
                    ? 'Vencido'
                    : lot.status === 'near_expiration'
                        ? 'Perto do vencimento'
                        : lot.status === 'low_stock'
                            ? 'Sem estoque'
                            : 'Normal';

                return `
                    <tr>
                        <td>${lot.batch_number || '-'}</td>
                        <td>${lot.manufacture_date ? moment(lot.manufacture_date).format('DD/MM/YYYY') : '-'}</td>
                        <td>${lot.expiration_date ? moment(lot.expiration_date).format('DD/MM/YYYY') : '-'}</td>
                        <td>${lot.quantity_received ?? 0}</td>
                        <td>${lot.quantity_available ?? 0}</td>
                        <td>${lot.received_date ? moment(lot.received_date).format('DD/MM/YYYY') : '-'}</td>
                        <td><span class="${badgeClass}">${statusLabel}</span></td>
                        <td>-</td>
                    </tr>
                `;
            }).join('');
        } catch (error) {
            console.error('Erro ao carregar lotes:', error);
            const tbody = document.querySelector('#lots-table tbody');
            tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Erro ao carregar lotes.</td></tr>';
        }
    }

    async function loadMovements() {
        try {
            const response = await fetch(`/products/${productId}/movements`);
            const data = await response.json();
            const tbody = document.querySelector('#movements-table tbody');

            tbody.innerHTML = data.data.map(movement => `
                <tr>
                    <td>${moment(movement.created_at).format('DD/MM/YYYY HH:mm')}</td>
                    <td><span class="${movement.movement_type_badge}">${movement.movement_type_label}</span></td>
                    <td>${movement.product_lot_id || '-'}</td>
                    <td>${movement.quantity}</td>
                    <td>${movement.user_id}</td>
                    <td>${movement.notes || '-'}</td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Erro ao carregar movimentações:', error);
        }
    }

    async function loadConsumptions() {
        try {
            const response = await fetch(`/products/${productId}/consumption-history`);
            const data = await response.json();
            const tbody = document.querySelector('#consumption-table tbody');

            tbody.innerHTML = data.data.map(consumption => `
                <tr>
                    <td>${consumption.patient_name || '-'}</td>
                    <td>${consumption.procedure_name || '-'}</td>
                    <td>${consumption.professional_name || '-'}</td>
                    <td>${consumption.lot_batch_number || '-'}</td>
                    <td>${consumption.quantity_used}</td>
                    <td>${moment(consumption.consumption_date).format('DD/MM/YYYY HH:mm')}</td>
                </tr>
            `).join('');
        } catch (error) {
            console.error('Erro ao carregar consumo:', error);
        }
    }

    function setupLotsForm() {
        document.getElementById('lotForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            try {
                const response = await fetch('/product-lots', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    showToast('Lote adicionado com sucesso', 'success');
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('lotModal')).hide();
                    this.reset();
                    await loadProductDetails();
                } else {
                    showToast('Erro ao adicionar lote', 'danger');
                }
            } catch (error) {
                showToast('Erro: ' + error.message, 'danger');
            }
        });
    }

    function setupDocumentForm() {
        document.getElementById('documentForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            try {
                const response = await fetch('/product-documents', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    showToast('Documento enviado com sucesso', 'success');
                    bootstrap.Modal.getOrCreateInstance(document.getElementById('documentModal')).hide();
                    this.reset();
                } else {
                    showToast('Erro ao enviar documento', 'danger');
                }
            } catch (error) {
                showToast('Erro: ' + error.message, 'danger');
            }
        });
    }

    function editProduct() {
        window.location.href = '{{ route("panel.products.edit", ":id") }}'.replace(':id', productId);
    }
</script>
@endpush

