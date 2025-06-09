<?php

namespace App\Console\Commands;

use App\Repositories\PatientRepository;
use App\Repositories\ScheduleRepository;
use App\Services\CampaignService;
use App\Services\DashboardService;
use Illuminate\Console\Command;

/**
 *
 */
class CampaignLaserWaxingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaignlaser:run';
    protected PatientRepository $patientRepository;
    protected CampaignService $campaignService;
    protected ScheduleRepository $scheduleRepository;

    /**
     * @var string
     */
    protected $description = 'Cancellation of unpaid dues';

    public function __construct(CampaignService    $campaignService,
                                ScheduleRepository $scheduleRepository)
    {
        $this->campaignService    = $campaignService;
        $this->scheduleRepository = $scheduleRepository;
        parent::__construct();
    }

    public function handle()
    {
        $patients = $this->scheduleRepository->skipPresenter()->getPatientsByLaser();
        $camp     = $this->campaignService->find(2,true);
        $image    = $camp->url_image;
        $caption  = $camp->description;
        foreach ($patients as $patient) {
            $this->campaignService->sendImageToWhatsApp($patient, $image, $caption);
            echo "\n $patient";
        }
    }


}
