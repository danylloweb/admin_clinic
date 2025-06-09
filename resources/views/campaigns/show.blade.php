@extends('layouts.header')
@section('content')
    <div class="container">
        <div class="card bg-dark text-white">
            <div class="card-header">
                <h4>Editar Campanha</h4>
            </div>
            <div class="card-body">
                <form id="formEditCampaign">
                    @csrf
                    <input type="hidden" name="id" value="{{ $campaign['id'] }}">

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome da campanha</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $campaign['name'] }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="5" required>{{ $campaign['description'] }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="url_image" class="form-label">URL da imagem</label>
                        <input type="text" class="form-control" id="url_image" name="url_image" value="{{ $campaign['url_image'] }}">
                        <img src="{{ $campaign['url_image'] }}" alt="Imagem atual" class="mt-2 rounded" style="width: 120px;">
                    </div>

                    <div class="mb-3">
                        <label for="date" class="form-label">Data da campanha</label>
                        <input type="date" class="form-control" id="date" name="date" value="{{ $campaign['date'] }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Criado" {{ $campaign['status'] == 'Criado' ? 'selected' : '' }}>Criado</option>
                            <option value="Ativo" {{ $campaign['status'] == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="Encerrado" {{ $campaign['status'] == 'Encerrado' ? 'selected' : '' }}>Encerrado</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Salvar alterações
                    </button>
                    <a href="{{ route('campaigns.index') }}" class="btn btn-secondary ms-2">Voltar</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const form = document.getElementById('formEditCampaign');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            disableForm(form);

            const formData = new FormData(form);

            fetch('{{ route("campaigns.update") }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json'
                },
                body: new URLSearchParams(formData) + '&_method=PUT'
            })
                .then(async res => {
                    const data = await res.json();

                    if (!res.ok) {
                        throw new Error(data.message || 'Erro ao atualizar campanha.');
                    }

                    showToast('success', 'Campanha atualizada com sucesso!');
                    setTimeout(() => {
                        window.location.href = '{{ route("campaigns.index") }}';
                    }, 1500);
                })
                .catch(err => {
                    showToast('error', err.message);
                })
                .finally(() => {
                    enableForm(form);
                });
        });
    </script>
@endpush
