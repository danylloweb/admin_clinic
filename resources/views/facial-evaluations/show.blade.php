@extends('layouts.header')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Visualizar Ficha de Avaliação Facial
                        </h4>
                        <small>ID: #{{ $facialEvaluation->id }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light" onclick="window.print()">
                            <i class="fas fa-print me-1"></i>Imprimir
                        </button>
                        <a href="{{ route('panel.patient.show', ['id' => $facialEvaluation->patient->id]) }}" class="btn btn-light">
                            <i class="fas fa-times"></i> Fechar
                        </a>
                    </div>
                </div>

                <div class="card-body" style="font-size: 14px;">
                    <!-- Cabeçalho com Logo -->
                    <div class="text-center mb-4 print-visible">
                        <img src="https://renovarestetica.com.br/Original-01.png" alt="Renovar Estética" style="max-width: 200px;">
                        <h3 class="mt-3">FICHA DE AVALIAÇÃO FACIAL</h3>
                        <p class="text-muted">{{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
                    </div>

                    <hr>

                    <!-- Dados do Paciente -->
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user me-2"></i>DADOS DO PACIENTE</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Nome:</strong> {{ $facialEvaluation->patient->name }}</p>
                            <p><strong>CPF:</strong> {{ $facialEvaluation->cpf ?? 'Não informado' }}</p>
                            <p><strong>Data Nascimento:</strong> {{ $facialEvaluation->patient->birth_date ? $facialEvaluation->patient->birth_date->format('d/m/Y') : 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Telefone:</strong> {{ $facialEvaluation->patient->phone ?? 'Não informado' }}</p>
                            <p><strong>E-mail:</strong> {{ $facialEvaluation->email ?? 'Não informado' }}</p>
                            <p><strong>WhatsApp:</strong> {{ $facialEvaluation->whatsapp ?? 'Não informado' }}</p>
                        </div>
                    </div>

                    <!-- Queixa Principal -->
                    @if($facialEvaluation->chief_complaint)
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-comment-dots me-2"></i>QUEIXA PRINCIPAL</h5>
                    <p class="mb-4">{{ $facialEvaluation->chief_complaint }}</p>
                    @endif

                    <!-- Tipo de Pele e Fototipo -->
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-droplet me-2"></i>CARACTERÍSTICAS DA PELE</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Tipo de Pele:</strong> {{ ucfirst($facialEvaluation->skin_type ?? 'Não informado') }}</p>
                            <p><strong>Fototipo Fitzpatrick:</strong> Tipo {{ $facialEvaluation->fitzpatrick_type ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Oleosidade:</strong> {{ $facialEvaluation->oiliness ?? 'N/A' }}/10</p>
                            <p><strong>Hidratação:</strong> {{ $facialEvaluation->hydration ?? 'N/A' }}/10</p>
                            <p><strong>Sensibilidade:</strong> {{ $facialEvaluation->sensitivity ?? 'N/A' }}/10</p>
                        </div>
                    </div>

                    <!-- Avaliação de Problemas -->
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-magnifying-glass me-2"></i>AVALIAÇÃO DE PROBLEMAS</h5>
                    <div class="row mb-4">
                        @foreach([
                            'acne' => 'Acne',
                            'melasma' => 'Melasma',
                            'wrinkles' => 'Rugas',
                            'flaccidity' => 'Flacidez',
                            'spots' => 'Manchas',
                            'dilated_pores' => 'Poros Dilatados'
                        ] as $field => $label)
                            @if($facialEvaluation->$field)
                            <div class="col-md-6">
                                <p class="mb-1"><strong>{{ $label }}:</strong> <span class="badge bg-warning">Presente</span></p>
                                @if($facialEvaluation->{"${field}_notes"})
                                <p class="text-muted small ms-3">{{ $facialEvaluation->{"${field}_notes"} }}</p>
                                @endif
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Histórico Estético -->
                    @if($facialEvaluation->aesthetic_history && count($facialEvaluation->aesthetic_history) > 0)
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-history me-2"></i>HISTÓRICO ESTÉTICO</h5>
                    <p class="mb-4">
                        @foreach($facialEvaluation->aesthetic_history as $procedure)
                            <span class="badge bg-info me-2">{{ ucfirst($procedure) }}</span>
                        @endforeach
                    </p>
                    @endif

                    <!-- Alergias -->
                    @if($facialEvaluation->allergies)
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-exclamation-triangle me-2"></i>ALERGIAS</h5>
                    <p class="mb-4">{{ $facialEvaluation->allergies }}</p>
                    @endif

                    <!-- Medicamentos -->
                    @if($facialEvaluation->medications_in_use)
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-pills me-2"></i>MEDICAMENTOS EM USO</h5>
                    <p class="mb-4">{{ $facialEvaluation->medications_in_use }}</p>
                    @endif

                    <!-- Objetivo do Paciente -->
                    @if($facialEvaluation->patient_objective)
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-bullseye me-2"></i>OBJETIVO DO PACIENTE</h5>
                    <p class="mb-4">{{ $facialEvaluation->patient_objective }}</p>
                    @endif

                    <!-- Plano de Tratamento -->
                    @if($facialEvaluation->treatment_plan && !empty($facialEvaluation->treatment_plan))
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-stethoscope me-2"></i>PLANO DE TRATAMENTO</h5>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Procedimento:</strong> {{ $facialEvaluation->treatment_plan['procedure'] ?? 'N/A' }}</p>
                            <p><strong>Quantidade de Sessões:</strong> {{ $facialEvaluation->treatment_plan['sessions'] ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Frequência:</strong> {{ $facialEvaluation->treatment_plan['frequency'] ?? 'N/A' }}</p>
                            @if($facialEvaluation->treatment_plan['observations'])
                            <p><strong>Observações:</strong> {{ $facialEvaluation->treatment_plan['observations'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Registro Fotográfico -->
                    @if($facialEvaluation->photo_front || $facialEvaluation->photo_profile_right || $facialEvaluation->photo_profile_left)
                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-camera me-2"></i>REGISTRO FOTOGRÁFICO</h5>
                    <div class="row mb-4">
                        @if($facialEvaluation->photo_front)
                        <div class="col-md-4 text-center">
                            <img src="{{ $facialEvaluation->photo_front }}" alt="Foto Frontal" class="img-fluid rounded" style="max-height: 300px;">
                            <p class="mt-2"><strong>Frontal</strong></p>
                        </div>
                        @endif
                        @if($facialEvaluation->photo_profile_right)
                        <div class="col-md-4 text-center">
                            <img src="{{ $facialEvaluation->photo_profile_right }}" alt="Perfil Direito" class="img-fluid rounded" style="max-height: 300px;">
                            <p class="mt-2"><strong>Perfil Direito</strong></p>
                        </div>
                        @endif
                        @if($facialEvaluation->photo_profile_left)
                        <div class="col-md-4 text-center">
                            <img src="{{ $facialEvaluation->photo_profile_left }}" alt="Perfil Esquerdo" class="img-fluid rounded" style="max-height: 300px;">
                            <p class="mt-2"><strong>Perfil Esquerdo</strong></p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <hr class="my-4">

                    <!-- Status de Assinatura -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5><i class="fas fa-signature me-2"></i>Status de Assinatura</h5>
                            @if($facialEvaluation->isSigned())
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i> <strong>Assinado em:</strong>
                                    {{ $facialEvaluation->signed_at->format('d/m/Y H:i:s') }}
                                </div>
                                @if($facialEvaluation->patient_signature)
                                <div class="mt-3">
                                    <p><strong>Assinatura do Paciente:</strong></p>
                                    <img src="{{ $facialEvaluation->patient_signature }}" alt="Assinatura" class="img-fluid" style="max-width: 300px; border: 1px solid #ddd; padding: 10px;">
                                </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-hourglass-half"></i> <strong>Pendente de Assinatura</strong>
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" onclick="generateAndSendToken()">
                                    <i class="fas fa-link me-1"></i>Gerar Link de Assinatura
                                </button>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-calendar me-2"></i>Informações de Datas</h5>
                            <p><strong>Criado em:</strong> {{ $facialEvaluation->created_at->format('d/m/Y H:i:s') }}</p>
                            <p><strong>Última atualização:</strong> {{ $facialEvaluation->updated_at->format('d/m/Y H:i:s') }}</p>
                            @if($facialEvaluation->professional)
                            <p><strong>Profissional:</strong> {{ $facialEvaluation->professional->name }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Botões de Ação -->
                    <hr>
                    <div class="d-flex justify-content-between gap-2 no-print">
                        <div>
                            <a href="{{ route('panel.facial-evaluations.edit', ['id' => $facialEvaluation->id]) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i>Editar
                            </a>
                            <button type="button" class="btn btn-secondary" onclick="window.print()">
                                <i class="fas fa-print me-1"></i>Imprimir
                            </button>
                        </div>
                        <div>
                            @if(!$facialEvaluation->isSigned())
                            <button type="button" class="btn btn-info" onclick="generateAndSendToken()">
                                <i class="fab fa-whatsapp me-1"></i>Enviar Link
                            </button>
                            @endif
                            <a href="{{ route('panel.patient.show', ['id' => $facialEvaluation->patient->id]) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
    }
</style>

@push('scripts')
<script>
    async function generateAndSendToken() {
        const id = {{ $facialEvaluation->id }};

        try {
            const response = await fetch(`/panel-facial-evaluations-send-link/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();
            if (!data.error) {
                window.open(data.whatsapp_url, '_blank');
                showToast('Link enviado para WhatsApp!', 'success');
            } else {
                showToast(data.message, 'danger');
            }
        } catch (error) {
            showToast('Erro: ' + error.message, 'danger');
        }
    }
</script>
@endpush
@endsection

