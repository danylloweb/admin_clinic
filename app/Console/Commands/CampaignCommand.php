<?php

namespace App\Console\Commands;

use App\Repositories\PatientRepository;
use App\Services\CampaignService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 *
 */
class CampaignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign:run {--campaignId= campaign Id}';
    protected PatientRepository $patientRepository;
    protected CampaignService $campaignService;

    /**
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(PatientRepository $patientRepository,
                                CampaignService $campaignService)
    {
        $this->patientRepository = $patientRepository;
        $this->campaignService   = $campaignService;
        parent::__construct();
    }
    public function handle()
    {
        $campaignId = $this->option('campaignId');
        $state      = $this->campaignService->dispatchProgress($campaignId);
        $not        = [75,549,238,251,253,412,322,426,635,180,181,217,214,230];
        $patients   = $this->patientRepository->skipPresenter()->findWhereNotIn('id',$not);
        $camp       = $this->campaignService->find($campaignId,true);
        $image      = $camp->url_image;

        $state['total'] = count($patients);
        $this->updateCache($campaignId, $state);

        $message = $camp->description;

        foreach ($patients as $patient) {
            $name    = $patient->social_name ?: $patient->name;
            $message = str_replace('{name}', $name, $message);
            $this->campaignService->sendImageToWhatsApp($patient->chat_id, $image, $message);
            $state['sent']++;
            $state['processed']++;
            $state['updated_at']      = now()->toDateTimeString();
            $state['last_patient_id'] = $patient->id;
            $this->updateCache($campaignId,$state);

        }
        $state['running']     = false;
        $state['finished']    = true;
        $state['finished_at'] = now()->toDateTimeString();
        $state['updated_at'] = now()->toDateTimeString();
        $this->updateCache($campaignId, $state);
    }

    private function updateCache(int $campaignId,$state): void
    {
        Cache::put($this->campaignService->dispatchCacheKey($campaignId), $state, now()->addHours(12));
    }
}
