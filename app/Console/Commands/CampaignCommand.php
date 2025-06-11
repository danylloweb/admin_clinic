<?php

namespace App\Console\Commands;

use App\Repositories\PatientRepository;
use App\Services\CampaignService;
use Illuminate\Console\Command;

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
    protected $signature = 'campaign:run';
    protected PatientRepository $patientRepository;
    protected CampaignService $campaignService;

    /**
     * @var string
     */
    protected $description = 'Cancellation of unpaid dues';

    public function __construct(PatientRepository $patientRepository,
                                CampaignService $campaignService)
    {
        $this->patientRepository = $patientRepository;
        $this->campaignService   = $campaignService;
        parent::__construct();
    }
    public function handle()
    {
        $not      = [75,549,238,251,253,412,322,426,635,180,181,217,214,230];
        $patients = $this->patientRepository->skipPresenter()->findWhereNotIn('id',$not);
        $camp     = $this->campaignService->find(11,true);
        $image    = $camp->url_image;
        foreach ($patients as $patient) {
            $this->campaignService->sendImageToWhatsApp($patient->chat_id, $image, $camp->description);
            echo "\n $patient->id - $patient->social_name";
        }
    }
}
