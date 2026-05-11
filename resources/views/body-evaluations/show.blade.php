@extends("layouts.header")
@section("content")
<div class="container mt-4">
    @component("components/alert") @endcomponent
    <div class="row">
        <div class="col-md-8">
            <a href="{{ route("panel.patient.show", ["id" => $bodyEvaluation->patient_id]) }}" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Avaliação Corporal - {{ $bodyEvaluation->patient->name }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Peso:</strong> {{ $bodyEvaluation->weight ?? "-" }} kg</p>
                    <p><strong>Altura:</strong> {{ $bodyEvaluation->height ?? "-" }} cm</p>
                    <p><strong>IMC:</strong> {{ $bodyEvaluation->weight && $bodyEvaluation->height ? number_format($bodyEvaluation->weight / (($bodyEvaluation->height/100)**2), 1) : "-" }}</p>
                    <p><strong>Gordur               $body                    <p><st ?? "-" }}%</p>
                    <p><strong>Musculatura:</strong> {{ $bodyEvaluation->muscle_mass ?? "-" }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <a href="{{ route("panel.body-evaluations.edit", ["id" => $bodyEvaluation->id]) }}" class="btn btn-warning btn-sm w-100 mb-2">
                <i class="fas fa-edit"></i> Editar
            </a>
            <form method="POST" action="{{ route("panel.body-evaluations.destroy", ["id" => $bodyEvaluation->id]) }}" style="display:inline;">
                                                                                                    bt                                                                                        s fa-trash"></i> Deletar
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
