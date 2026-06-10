@extends('layouts.header')
@section('content')
<div class="">
    <div class="card mb-3">
        <div class="card-header  border-0">
            <h5 class="mb-0"><i class="ph ph-pencil me-2"></i>Editar Fornecedor</h5>
        </div>
        <div class="card-body">
            <form id="form-supplier" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">ID</label>
                        <input type="text" class="form-control" id="id" disabled>
                    </div>
                    <div class="col-md-10 mb-3">
                        <label class="form-label">Nome do Fornecedor <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">CNPJ</label>
                        <input type="text" name="cnpj" id="cnpj" class="form-control" placeholder="00.000.000/0000-00">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pessoa de Contato</label>
                        <input type="text" name="contact_name" id="contact_name" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Telefone de Contato</label>
                        <input type="tel" name="contact_phone" id="contact_phone" class="form-control" placeholder="(00) 90000-0000">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Endereço</label>
                        <textarea name="address" id="address" class="form-control" rows="3"></textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="active" name="active" value="1">
                            <label class="form-check-label" for="active">
                                Ativo
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-success">
                            <i class="ph ph-floppy-disk me-2"></i>Atualizar
                        </button>
                        <a href="{{ route('panel.suppliers.index') }}" class="btn btn-secondary">
                            <i class="ph ph-arrow-left me-2"></i>Voltar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        const supplierId = {{ $supplierId }};
        loadSupplierData(supplierId);

        // Format CNPJ input
        $('#cnpj').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 14) value = value.slice(0, 14);
            if (value.length > 12) {
                value = value.slice(0, 8) + '/' + value.slice(8, 12) + '-' + value.slice(12);
            } else if (value.length > 8) {
                value = value.slice(0, 8) + '/' + value.slice(8);
            } else if (value.length > 5) {
                value = value.slice(0, 5) + '.' + value.slice(5);
            } else if (value.length > 2) {
                value = value.slice(0, 2) + '.' + value.slice(2);
            }
            $(this).val(value);
        });

        // Format phone input
        $('#contact_phone').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length > 7) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2, 7) + '-' + value.slice(7);
            } else if (value.length > 2) {
                value = '(' + value.slice(0, 2) + ') ' + value.slice(2);
            }
            $(this).val(value);
        });

        // Form submission
        $('#form-supplier').on('submit', function(e) {
            e.preventDefault();

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const formData = {
                name: $('#name').val(),
                cnpj: $('#cnpj').val(),
                email: $('#email').val(),
                contact_name: $('#contact_name').val(),
                contact_phone: $('#contact_phone').val(),
                address: $('#address').val(),
                active: $('#active').is(':checked') ? 1 : 0
            };

            $.ajax({
                url: '/suppliers/' + supplierId,
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(formData),
                success: function(response) {
                    showToast('Fornecedor atualizado com sucesso', 'success');
                    setTimeout(() => {
                        window.location.href = '{{ route("panel.suppliers.index") }}';
                    }, 1500);
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showToast(xhr.responseJSON.message, 'danger');
                    } else {
                        showToast('Erro ao atualizar fornecedor', 'danger');
                    }
                }
            });
        });
    });

    function loadSupplierData(supplierId) {
        $.ajax({
            url: '/suppliers/' + supplierId,
            method: 'GET',
            success: function(response) {
                const supplier = response.data || response;
                $('#id').val(supplier.id);
                $('#name').val(supplier.name);
                $('#cnpj').val(supplier.cnpj || '');
                $('#email').val(supplier.email || '');
                $('#contact_name').val(supplier.contact_name || '');
                $('#contact_phone').val(supplier.contact_phone || '');
                $('#address').val(supplier.address || '');

                if (supplier.active) {
                    $('#active').prop('checked', true);
                }
            },
            error: function() {
                showToast('Erro ao carregar dados do fornecedor', 'danger');
                setTimeout(() => {
                    window.location.href = '{{ route("panel.suppliers.index") }}';
                }, 1500);
            }
        });
    }
</script>
@endpush

