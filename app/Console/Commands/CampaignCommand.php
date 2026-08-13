<?php

namespace App\Console\Commands;

use App\Repositories\PatientRepository;
use App\Services\CampaignService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class CampaignCommand extends Command
{
    protected $signature = 'campaign:run
                            {--campaignId= : Campaign Id}
                            {--batch=15 : Qtde de mensagens a enviar nesta execução}
                            {--minDelay=20 : Delay mínimo (s) entre envios}
                            {--maxDelay=40 : Delay máximo (s) entre envios}
                            {--startHour=8 : Hora inicial permitida}
                            {--endHour=20 : Hora final permitida}';

    protected PatientRepository $patientRepository;
    protected CampaignService $campaignService;

    protected $description = 'Envia campanha em lotes, com delay aleatório, dentro do horário comercial, evitando bloqueio do WhatsApp.';

    public function __construct(PatientRepository $patientRepository, CampaignService $campaignService)
    {
        $this->patientRepository = $patientRepository;
        $this->campaignService   = $campaignService;
        parent::__construct();
    }

    public function handle()
    {
//        $campaignId = (int) $this->option('campaignId');
        $batchSize  = (int) $this->option('batch');
        $minDelay   = (int) $this->option('minDelay');
        $maxDelay   = (int) $this->option('maxDelay');
        $startHour  = (int) $this->option('startHour');
        $endHour    = (int) $this->option('endHour');

        if (!$this->withinBusinessHours($startHour, $endHour)) {
            $this->info('Fora do horário permitido. Encerrando esta execução.');
            return;
        }
        $campaignId = $this->campaignService->findWhere(["date" => Carbon::now()->format('Y-m-d')],true)->id;
        if (empty($campaignId)) {
            $this->info('Nenhuma campanha ativa para hoje.');
            return;
        }
        $cacheKey = $this->campaignService->dispatchCacheKey($campaignId);
        $state    = Cache::get($cacheKey) ?? $this->campaignService->dispatchProgress($campaignId);

        if (!empty($state['finished'])) {
            $this->info('Campanha já finalizada.');
            return;
        }

        $not    = [75, 549, 251, 412, 230, 448];
        $cursor = $state['last_patient_id'] ?? 0;

        // ajuste o filtro conforme o que o seu repository suportar
        $allPatients = $this->patientRepository->skipPresenter()->findWhereNotIn('id', $not);

        if (!isset($state['total'])) {
            $state['total']     = count($allPatients);
            $state['sent']      = $state['sent'] ?? 0;
            $state['processed'] = $state['processed'] ?? 0;
        }

        $patients = collect($allPatients)
            ->where('id', '>', $cursor)
            ->sortBy('id')
            ->take($batchSize);

        $camp  = $this->campaignService->find($campaignId, true);
        $image = $camp->url_image;

        if ($patients->isEmpty()) {
            $state['running']     = false;
            $state['finished']    = true;
            $state['finished_at'] = now()->toDateTimeString();
            $state['updated_at']  = now()->toDateTimeString();
            $this->updateCache($campaignId, $state);
            $this->info('Todos os pacientes já foram processados.');
            return;
        }

        $last = $patients->last();

        foreach ($patients as $patient) {
            if (!$this->withinBusinessHours($startHour, $endHour)) {
                $this->info('Horário limite atingido. Pausando até a próxima execução.');
                break;
            }

            $name    = $patient->social_name ?: $patient->name;
            $message = str_replace('{name}', $name, $camp->description);

            $this->campaignService->sendImageToWhatsApp($patient->chat_id, $image, $message);

            $state['sent']++;
            $state['processed']++;
            $state['updated_at']      = now()->toDateTimeString();
            $state['last_patient_id'] = $patient->id;
            $this->updateCache($campaignId, $state);

            if ($patient->id !== $last->id) {
                $delay = rand($minDelay, $maxDelay);
                sleep($delay);
            }
        }

        $this->info("Lote concluído. Total enviado até agora: {$state['sent']}/{$state['total']}");
    }

    private function withinBusinessHours(int $start, int $end): bool
    {
        $hour = (int) now()->format('G');
        return $hour >= $start && $hour < $end;
    }

    private function updateCache(int $campaignId, $state): void
    {
        Cache::put($this->campaignService->dispatchCacheKey($campaignId), $state, now()->addHours(24));
    }
}
