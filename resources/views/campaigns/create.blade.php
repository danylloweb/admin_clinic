@extends('layouts.header')
@section('content')

        <div class="card text-white">
            <div class="card-header">
                <h4>Criar Campanha</h4>
            </div>
            <div class="card-body">
                <form id="formCreateCampaign">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome da campanha</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Data da campanha</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                    </div>

                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3">
                            <label for="image_file" class="form-label">Imagem da campanha</label>
                            <input type="file" class="form-control" id="image_file" accept="image/*">
                            <input type="hidden" id="url_image" name="url_image">
                            <small class="text-muted">Selecione um arquivo de imagem.</small>
                        </div>
                        <div class="col-md-6 mb-2 mt-3 text-center">
                            <img id="imagePreview" src="" alt="Preview da imagem" class="rounded img-fluid d-none" style="max-height: 300px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <div class="border quill">
                                <div class="quill-inner" id="description-editor" contenteditable="true"></div>
                            </div>
                            <input type="hidden" name="description" id="description">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Criar campanha
                    </button>
                    <a href="{{ route('panel.campaigns.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                </form>
            </div>
        </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {

        $('#image_file').on('change', function () {
            const file = this.files[0];
            const $image = $('#imagePreview');

            if (!file) {
                $('#url_image').val('');
                $image.fadeOut(300, function () {
                    $image.addClass('d-none');
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                const base64 = e.target.result;
                $('#url_image').val(base64);
                $image.fadeOut(300, function () {
                    $image.attr('src', base64).removeClass('d-none').fadeIn(300);
                });
            };
            reader.readAsDataURL(file);
        });

        const form = document.getElementById('formCreateCampaign');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            disableForm(form, "Criando Campanha...");
            if (!validateForm(this)){
               enableForm(form,"Criar Campanha");
                return false;
            }

            const formData = {
                name: form.name.value,
                date: form.date.value,
                url_image: form.url_image.value || null,
                description: form.description.value
            };

            fetch('{{ route("campaigns.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(formData)
            }).then(async res => {
                    const data = await res.json();

                    if (data.error) {
                        showToast(data.message,'error');
                    }
                    showToast('Campanha Cadastrada com sucesso!','success');
                    setTimeout(() => {
                        window.location.href = '{{ route("panel.campaigns.index") }}';
                    }, 2000);
                })
                .catch(err => {
                    showToast(err.message || 'Erro ao criar campanha', 'danger');
                })
                .finally(() => {
                    enableForm(form,"Criar Campanha");
                });
        });
    });

    function validateForm(form) {
        const isValid = form.checkValidity();
        if (!isValid) {
            form.reportValidity();
            enableForm(form, "Criar Campanha");
            return false;
        }
        const quillContent = $('#description-editor').html().trim().replace(/<(.|\n)*?>/g, '').trim();
        $('#description').val(quillContent);
        return true;
    }
    </script>
@endpush
