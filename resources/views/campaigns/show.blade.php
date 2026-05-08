@extends('layouts.header')
@section('content')
        <div class="card text-white">
            <div class="card-body">
                <form id="formEditCampaign">
                    @csrf
                    <input type="hidden" name="id" value="{{ $campaign['id'] }}">
                    @php
                        $formattedDate = \Carbon\Carbon::parse($campaign['date'])->format('Y-m-d');
                    @endphp

                    <div class="row g-4">
                        {{-- Coluna esquerda --}}
                        <div class="col-md-6 d-flex flex-column gap-3">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label for="name" class="form-label">Nome da campanha</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ $campaign['name'] }}" required>
                                </div>
                                <div class="col-6">
                                    <label for="date" class="form-label">Data da campanha</label>
                                    <input type="date" id="date" class="form-control" name="date" value="{{ $formattedDate }}" required>
                                </div>
                            </div>

                            <div>
                                <label for="image_file" class="form-label">Imagem da campanha</label>
                                <input type="file" class="form-control" id="image_file" accept="image/*">
                                <input type="hidden" id="url_image" name="url_image" value="{{ $campaign['url_image'] }}">
                                <small class="text-muted">Selecione um arquivo para substituir a imagem atual.</small>
                            </div>

                            <div class="flex-grow-1 d-flex flex-column">
                                <label for="description" class="form-label">Descrição</label>
                                <textarea class="form-control flex-grow-1" id="description" name="description" rows="8" style="resize: vertical;">{{ $campaign['description'] }}</textarea>
                            </div>
                        </div>

                        {{-- Coluna direita: preview da imagem --}}
                        <div class="col-md-6 d-flex align-items-start justify-content-center">
                            <img id="imagePreview" src="{{ $campaign['url_image'] }}" alt="Imagem atual"
                                 class="rounded img-fluid w-100" style="max-height: 650px; object-fit: contain;">
                        </div>
                    </div>

                    <input type="hidden" name="description" id="description-hidden" value="{{ $campaign['description'] }}">

                    <div class="mt-8">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Salvar alterações
                        </button>
                        <a href="{{ route('panel.campaign.send', ['id' => $campaign['id']]) }}" class="btn btn-primary ms-2">
                            Enviar campanha
                        </a>
                        <a href="{{ route('panel.campaigns.index') }}" class="btn btn-secondary ms-2">Voltar</a>
                    </div>
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
                    description: document.getElementById('description').value
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
            return true;
        }
    </script>
@endpush
