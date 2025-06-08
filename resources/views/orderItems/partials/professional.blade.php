@if($professional)
<section class="content-header">
        <div class="box box-primary collapsed-box">
            <div class="box-header with-border">
                <h3 class="box-title">Profissional #{{ $professional->id }}</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    {{-- Dados do Profissional --}}
                    <div class="col-md-9">
                        <dl class="dl-horizontal">
                            <dt>Nome</dt>
                            <dd>{{ $professional->name }}</dd>
                            <dt>Provider ID</dt>
                            <dd>{{ $professional->provider_id }}</dd>
                            <dt>Documento</dt>
                            <dd>{{ $professional->document }}</dd>
                        </dl>
                    </div>

                    {{-- Avatar do Profissional --}}
                    <div class="col-md-3 text-center">
                        @if($professional->avatar_url)
                            <a href="{{ $professional->avatar_url }}" target="_blank">
                                <img src="{{ $professional->avatar_url }}" alt="Avatar" class="img-thumbnail" style="max-width: 100px;">
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
</section>
@endif

{{-- Abaixo virá a parte do schedule e seus logs --}}
