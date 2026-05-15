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
class ScheduleMessageConfirmationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'message-confirmation:run';
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

    public function handle(): void
    {
        $patients = $this->scheduleRepository->skipPresenter()->getPatientsToday();
        $camp     = $this->campaignService->find(4,true);

        foreach ($patients as $patient) {
            $message = "Bom dia! ☀️ $patient->social_name ✨ " .$camp->description;
            $this->campaignService->sendMessageToWhatsApp($patient->chat_id, $message);
        }
    }

}
