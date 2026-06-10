@extends('layouts.header')
@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Editar Produto</h4>
            <a href="{{ route('panel.products.show', ['id' => $productId]) }}" class="btn btn-light btn-sm">Voltar</a>
        </div>
        <div class="card-body">
            <form id="productForm">
                @csrf
                <input type="hidden" name="image_url" id="image_url">

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-image me-2"></i>Foto e Identificação</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Foto Principal</label>
                                <div class="d-flex gap-2 mb-2">
                                    <button type="button" id="btn-pick-file" class="btn btn-light btn-sm">Trocar foto</button>
                                </div>
                                <img id="preview-image" class="img-fluid rounded mt-2 border" style="max-height: 200px; display: none;" src="" alt="Preview">
                                <input type="file" id="image_primary_file" class="d-none" accept="image/jpeg,image/png,image/webp,image/heic,image/heif">
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
                                    <option value="1">Ativo</option>
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
                                    <input class="form-check-input" type="checkbox" id="requires_batch_tracking" name="requires_batch_tracking" value="1">
                                    <label class="form-check-label" for="requires_batch_tracking">Controla Lote</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="requires_expiration_tracking" name="requires_expiration_tracking" value="1">
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

                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="ph ph-warehouse me-2"></i>Controle de Estoque / Compra / Armazenamento</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">Estoque Mínimo</label><input type="number" name="minimum_stock" id="minimum_stock" class="form-control" min="0"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Estoque Ideal</label><input type="number" name="ideal_stock" id="ideal_stock" class="form-control" min="0"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Estoque Atual</label><input type="number" name="current_stock" id="current_stock" class="form-control" readonly></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Estoque Reservado</label><input type="number" name="reserved_stock" id="reserved_stock" class="form-control" min="0"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3"><label class="form-label">Fornecedor</label><select name="supplier_id" id="supplier_id" class="form-select"><option value="">Selecione...</option></select></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Número da Nota Fiscal</label><input type="text" name="invoice_number" id="invoice_number" class="form-control"></div>
                            <div class="col-md-4 mb-3"><label class="form-label">Data de Compra</label><input type="date" name="purchase_date" id="purchase_date" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">Data de Recebimento</label><input type="date" name="receipt_date" id="receipt_date" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Valor Unitário (R$)</label><input type="number" step="0.01" name="unit_value" id="unit_value" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Valor de Venda (R$)</label><input type="number" step="0.01" name="sale_value" id="sale_value" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Margem de Lucro (%)</label><input type="number" step="0.01" name="profit_margin" id="profit_margin" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">Localização Física</label><input type="text" name="storage_location" id="storage_location" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Corredor</label><input type="text" name="aisle" id="aisle" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Armário</label><input type="text" name="cabinet" id="cabinet" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Prateleira</label><input type="text" name="shelf" id="shelf" class="form-control"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3"><label class="form-label">Temperatura Mínima (°C)</label><input type="number" step="0.1" name="min_temperature" id="min_temperature" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Temperatura Máxima (°C)</label><input type="number" step="0.1" name="max_temperature" id="max_temperature" class="form-control"></div>
                            <div class="col-md-3 mb-3"><label class="form-label">Umidade Ideal (%)</label><input type="number" name="ideal_humidity" id="ideal_humidity" class="form-control" min="0" max="100"></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-lg"><i class="ph ph-floppy-disk me-2"></i>Salvar Alterações</button>
                    <a href="{{ route('panel.products.show', ['id' => $productId]) }}" class="btn btn-secondary btn-lg">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const productId = {{ $productId }};

    document.addEventListener('DOMContentLoaded', async function() {
        const form = document.getElementById('productForm');
        const fileInput = document.getElementById('image_primary_file');
        const pickFileBtn = document.getElementById('btn-pick-file');
        const imagePreview = document.getElementById('preview-image');
        const imageUrlInput = document.getElementById('image_url');

        await loadSuppliers();
        await loadProduct();

        pickFileBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            try {
                const url = await uploadImageFile(file);
                imageUrlInput.value = url;
                imagePreview.src = url;
                imagePreview.style.display = 'block';
                showToast('Foto atualizada com sucesso.', 'success');
            } catch (error) {
                showToast(error.message || 'Erro ao enviar foto.', 'danger');
            }
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData();
            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(input => {
                if (!input.name || input.type === 'file') return;
                if (input.type === 'checkbox') {
                    formData.append(input.name, input.checked ? 1 : 0);
                } else {
                    formData.append(input.name, input.value);
                }
            });

            formData.append('_method', 'PUT');

            try {
                const response = await fetch(`/products/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    showToast('Produto atualizado com sucesso', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("panel.products.show", ":id") }}'.replace(':id', productId);
                    }, 800);
                    return;
                }

                if (data.errors) {
                    const firstKey = Object.keys(data.errors)[0];
                    showToast(data.errors[firstKey][0], 'danger');
                } else {
                    showToast(data.message || 'Erro ao atualizar produto', 'danger');
                }
            } catch (error) {
                showToast('Erro de rede: ' + error.message, 'danger');
            }
        });

        async function uploadImageFile(file) {
            const uploadData = new FormData();
            uploadData.append('file', file, file.name || 'product-image.jpg');
            uploadData.append('folder', 'products');
            uploadData.append('prefix', 'primary-image');

            const response = await fetch('{{ route("panel.uploads.store") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: uploadData
            });

            const data = await response.json();
            if (!response.ok || data.error) {
                throw new Error(data.message || 'Erro ao enviar imagem.');
            }
            return data.url;
        }

        async function loadSuppliers() {
            const response = await fetch('/suppliers?limit=1000', { credentials: 'same-origin' });
            const data = await response.json();
            const select = document.getElementById('supplier_id');
            const items = Array.isArray(data.data) ? data.data : [];

            items.forEach(supplier => {
                const option = document.createElement('option');
                option.value = supplier.id;
                option.textContent = supplier.name;
                select.appendChild(option);
            });
        }

        async function loadProduct() {
            const response = await fetch(`/products/${productId}`);
            if (!response.ok) {
                showToast('Produto não encontrado', 'danger');
                setTimeout(() => {
                    window.location.href = '{{ route("panel.products.index") }}';
                }, 1000);
                return;
            }

            const data = await response.json();
            const product = data.data ? data.data : data;

            const setValue = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.value = value ?? '';
            };

            setValue('internal_code', product.internal_code);
            setValue('ean_code', product.ean_code);
            setValue('name', product.name);
            setValue('trade_name', product.trade_name);
            setValue('description', product.description);
            setValue('image_url', product.image_url);
            setValue('category_type', product.category_type);
            setValue('unit_measure', product.unit_measure);
            setValue('subcategory', product.subcategory);
            setValue('brand', product.brand);
            setValue('anvisa_registration', product.anvisa_registration);
            setValue('anvisa_process', product.anvisa_process);
            setValue('minimum_stock', product.minimum_stock);
            setValue('ideal_stock', product.ideal_stock);
            setValue('current_stock', product.current_stock);
            setValue('reserved_stock', product.reserved_stock);
            setValue('storage_location', product.storage_location);
            setValue('aisle', product.aisle);
            setValue('cabinet', product.cabinet);
            setValue('shelf', product.shelf);
            setValue('min_temperature', product.min_temperature);
            setValue('max_temperature', product.max_temperature);
            setValue('ideal_humidity', product.ideal_humidity);
            setValue('supplier_id', product.supplier_id);
            setValue('invoice_number', product.invoice_number);
            setValue('purchase_date', product.purchase_date);
            setValue('receipt_date', product.receipt_date);
            setValue('unit_value', product.unit_value);
            setValue('sale_value', product.sale_value);
            setValue('profit_margin', product.profit_margin);
            setValue('status', product.status ? 1 : 0);

            document.getElementById('requires_batch_tracking').checked = !!product.requires_batch_tracking;
            document.getElementById('requires_expiration_tracking').checked = !!product.requires_expiration_tracking;
            document.getElementById('requires_refrigeration').checked = !!product.requires_refrigeration;
            document.getElementById('is_injectable').checked = !!product.is_injectable;
            document.getElementById('requires_patient_tracking').checked = !!product.requires_patient_tracking;

            if (product.image_url) {
                imagePreview.src = product.image_url;
                imagePreview.style.display = 'block';
            }
        }
    });
</script>
@endpush

