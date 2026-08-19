<?php

namespace App\Jobs;

use App\Repositories\CampaignRepository;
use App\Repositories\PatientRepository;
use App\Services\AppService;
use App\Services\CampaignProgressService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendCampaignMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas antes de falhar
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Número de segundos até a job ser liberada novamente após exceção
     *
     * @var int
     */
    public $backoff = 5;

    /**
     * @param int $campaignId
     * @param int $patientId
     */
    public function __construct(
        private int $campaignId,
        private int $patientId,
    ) {
        $this->queue = config('campaign.queue.name', 'campaigns');
        $this->tries = config('campaign.queue.tries', 3);
        $this->timeout = config('campaign.queue.timeout', 30);
    }

    /**
     * Executa a job
     *
     * @param CampaignRepository $campaignRepository
     * @param PatientRepository $patientRepository
     * @param AppService $appService
     * @param CampaignProgressService $progressService
     * @return void
     * @throws Throwable
     */
    public function handle(
        CampaignRepository $campaignRepository,
        PatientRepository $patientRepository,
        AppService $appService,
        CampaignProgressService $progressService,
    ): void {
        try {
            // Buscar campanha e paciente
            $campaign = $campaignRepository->skipPresenter()->find($this->campaignId);
            $patient = $patientRepository->skipPresenter()->find($this->patientId);

            // Validações
            if (!$campaign) {
                throw new \Exception("Campanha {$this->campaignId} não encontrada");
            }

            if (!$patient) {
                throw new \Exception("Paciente {$this->patientId} não encontrado");
            }

            if (empty($patient->chat_id)) {
                throw new \Exception("Paciente {$this->patientId} sem chat_id configurado");
            }

            // Montar mensagem substituindo placeholders
            $name = $patient->social_name ?: $patient->name;
            $message = str_replace('{name}', $name, (string) $campaign->description);

            // Enviar imagem via WhatsApp
            $response = $appService->sendImageToWhatsApp(
                (string) $patient->chat_id,
                (string) $campaign->url_image,
                $message
            );

            // Verificar se a resposta indica sucesso
            if (is_array($response) && !empty($response['error'])) {
                throw new \Exception("Erro ao enviar para WhatsApp: " . json_encode($response));
            }

            // Registrar sucesso
            $progressService->recordSent($this->campaignId, $this->patientId);

            Log::info("Campaign message sent", [
                'campaign_id' => $this->campaignId,
                'patient_id' => $this->patientId,
                'chat_id' => $patient->chat_id,
            ]);

        } catch (Throwable $exception) {
            Log::error("Campaign message failed", [
                'campaign_id' => $this->campaignId,
                'patient_id' => $this->patientId,
                'error' => $exception->getMessage(),
                'attempt' => $this->attempts(),
            ]);

            // Registrar falha no progresso
            $progressService->recordFailed(
                $this->campaignId,
                $this->patientId,
                $exception->getMessage()
            );

            // Não relançar exceção para não retentar indefinidamente
            // A falha já foi registrada no cache de progresso
        }
    }

    /**
     * Determina o backoff para retentativas (exponencial)
     *
     * @return int[]
     */
    public function backoff(): array
    {
        return [5, 10, 30]; // 5s, 10s, 30s
    }
}

