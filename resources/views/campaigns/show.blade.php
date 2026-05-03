@extends('layouts.header')
@section('content')
        <div class="card text-white">
            <div class="card-header">
                <h4>Editar Campanha -> {{ $campaign['id'] }}</h4>
            </div>
            <div class="card-body">
                <form id="formEditCampaign">
                    @csrf
                    <input type="hidden" name="id" value="{{ $campaign['id'] }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nome da campanha</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $campaign['name'] }}" required>
                        </div>
                        @php
                            $formattedDate = \Carbon\Carbon::parse($campaign['date'])->format('Y-m-d');
                        @endphp
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Data da campanha</label>
                            <input type="date" id="date" class="form-control"  name="date" value="{{ $formattedDate }}" required>
                        </div>
                    </div>

                    <div class="row align-items-end">
                        <div class="col-md-6 mb-lg-20">
                            <label for="image_file" class="form-label">Imagem da campanha</label>
                            <input type="file" class="form-control" id="image_file" accept="image/*">
                            <input type="hidden" id="url_image" name="url_image" value="{{ $campaign['url_image'] }}">
                            <small class="text-muted">Selecione um arquivo para substituir a imagem atual.</small>
                        </div>
                        <div class="col-md-6 mb-2 mt-3 text-center">
                            <img id="imagePreview" src="{{ $campaign['url_image'] }}" alt="Imagem atual" class="rounded img-fluid" style="max-height: 300px;">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <div class="border quill">
                                <div class="quill-inner" id="description-editor">{{ $campaign['description'] }}</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="description" id="description" value="{{ $campaign['description'] }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Salvar alterações
                    </button>
                    <a href="{{ route('panel.campaigns.index') }}" class="btn btn-secondary ms-2">Voltar</a>
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

            const form = document.getElementById('formEditCampaign');
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                disableForm(form, "Salvando...");
                if (!validateForm(this)){
                    enableForm(form,"Salvar alterações");
                    return false;
                }

                const formData = {
                    name: form.name.value,
                    date: form.date.value,
                    url_image: form.url_image.value || null,
                    description: form.description.value
                };

                fetch('{{ route("campaigns.update",$campaign['id']) }}', {
                    method: 'PUT',
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
                    showToast('Campanha salva com sucesso!','success');
                    setTimeout(() => {
                        window.location.href = '{{ route("panel.campaigns.index") }}';
                    }, 2000);
                })
                    .catch(err => {
                        showToast(err.message || 'Erro ao salvar campanha', 'danger');
                    })
                    .finally(() => {
                        enableForm(form,"Salvar alterações");
                    });
            });
        });

        function validateForm(form) {
            const isValid = form.checkValidity();
            if (!isValid) {
                form.reportValidity();
                enableForm(form, "Salvar alterações");
                return false;
            }
            const quillContent = $('#description-editor').html().trim().replace(/<(.|\n)*?>/g, '').trim();
            $('#description').val(quillContent);
            return true;
        }
    </script>
@endpush
