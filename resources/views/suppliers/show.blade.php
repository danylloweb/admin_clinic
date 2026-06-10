@extends('layouts.header')
@section('content')
<div class="">
    <!-- Supplier Details -->
    <div class="card mb-3">
        <div class="card-header  border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="ph ph-building me-2"></i>Detalhes do Fornecedor</h5>
                <div>
                    <a href="{{ route('panel.suppliers.edit', ':id') }}" class="btn btn-sm btn-warning me-2" onclick="this.href = this.href.replace(':id', {{ $supplierId }});">
                        <i class="ph ph-pencil me-1"></i>Editar
                    </a>
                    <a href="{{ route('panel.suppliers.index') }}" class="btn btn-sm btn-secondary">
                        <i class="ph ph-arrow-left me-1"></i>Voltar
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body" id="supplier-details">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Carregando...</span>
            </div>
        </div>
    </div>

    <!-- Products from this Supplier -->
    <div class="card">
        <div class="card-header  border-0">
            <h5 class="mb-0"><i class="ph ph-package me-2"></i>Produtos do Fornecedor</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable-products" class="table table-bordered table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Código</th>
                            <th>Categoria</th>
                            <th>Estoque Atual</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="products-list">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        const supplierId = {{ $supplierId }};
        loadSupplierDetails(supplierId);
        loadSupplierProducts(supplierId);
    });

    function loadSupplierDetails(supplierId) {
        $.ajax({
            url: '/suppliers/' + supplierId,
            method: 'GET',
            success: function(response) {
                const supplier = response.data || response;
                let detailsHtml = `
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">ID</label>
                            <p class="h6">${supplier.id}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nome</label>
                            <p class="h6">${supplier.name}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">CNPJ</label>
                            <p class="h6">${supplier.cnpj || '-'}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <p class="h6">${supplier.email ? '<a href="mailto:' + supplier.email + '">' + supplier.email + '</a>' : '-'}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Pessoa de Contato</label>
                            <p class="h6">${supplier.contact_name || '-'}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Telefone</label>
                            <p class="h6">${supplier.contact_phone ? '<a href="tel:' + supplier.contact_phone + '">' + supplier.contact_phone + '</a>' : '-'}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-muted">Endereço</label>
                            <p class="h6">${supplier.address || '-'}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Status</label>
                            <p class="h6">
                                <span class="badge ${supplier.active ? 'bg-success' : 'bg-danger'}">
                                    ${supplier.active ? 'Ativo' : 'Inativo'}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Atualizado em</label>
                            <p class="h6">${moment(supplier.updated_at).format('DD/MM/YYYY HH:mm')}</p>
                        </div>
                    </div>
                `;
                $('#supplier-details').html(detailsHtml);
            },
            error: function() {
                $('#supplier-details').html('<div class="alert alert-danger">Erro ao carregar dados do fornecedor</div>');
            }
        });
    }

    function loadSupplierProducts(supplierId) {
        $.ajax({
            url: '/products',
            method: 'GET',
            data: {
                supplier_id: supplierId,
                limit: 100
            },
            success: function(response) {
                const products = response.data || [];
                let html = '';

                if (products.length === 0) {
                    html = '<tr><td colspan="7" class="text-center text-muted">Nenhum produto cadastrado para este fornecedor</td></tr>';
                } else {
                    products.forEach(product => {
                        const status = product.status ? '<span class="badge bg-success">Ativo</span>' : '<span class="badge bg-danger">Inativo</span>';
                        const category = product.category_label || product.category_type || '-';

                        html += `
                            <tr>
                                <td>${product.id}</td>
                                <td>${product.name}</td>
                                <td>${product.internal_code || '-'}</td>
                                <td>${category}</td>
                                <td><span class="badge bg-info">${product.current_stock}</span></td>
                                <td>${status}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="viewProduct(${product.id})" title="Visualizar">
                                        <i class="ph ph-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }

                $('#products-list').html(html);
            },
            error: function() {
                $('#products-list').html('<tr><td colspan="7" class="text-center text-danger">Erro ao carregar produtos</td></tr>');
            }
        });
    }

    function viewProduct(id) {
        window.location.href = '{{ route("panel.products.show", ":id") }}'.replace(':id', id);
    }
</script>
@endpush

