<?php

namespace App\Console\Commands;

use App\Entities\Patient;
use App\Services\AppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdatePatientPhotosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'patient-photos:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update patient photos from WhatsApp contacts every 15 days';

    protected AppService $appService;

    public function __construct(AppService $appService)
    {
        parent::__construct();
        $this->appService = $appService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $this->info('Starting patient photo update...');

            $patients = Patient::all();
            $updated = 0;
            $failed = 0;

            foreach ($patients as $patient) {
                try {
                    $this->line("Processing patient: {$patient->name} ({$patient->phone})");

                    $photoResponse = $this->appService->getImageToContactWhatsApp($patient->chat_id);

                    if ($photoResponse && isset($photoResponse->success)) {
                        // Store the response data
                        $patient->update([
                            'photo' => $photoResponse->success
                        ]);
                        $updated++;
                        $this->line("<info>✓ Photo updated for {$patient->name}</info>");
                    } else {
                        $this->line("<comment>✗ No photo found for {$patient->name}</comment>");
                    }

                    // Add small delay to avoid API rate limiting
                    sleep(1);

                } catch (\Exception $e) {
                    $failed++;
                    $this->error("Error processing patient {$patient->name}: {$e->getMessage()}");
                    Log::error("Patient photo update error for {$patient->phone}", [
                        'error'      => $e->getMessage(),
                        'patient_id' => $patient->id
                    ]);
                }
            }

            $this->info("Patient photo update completed!");
            $this->info("Updated: {$updated} | Failed: {$failed} | Total: {$patients->count()}");

            Log::info("Patient photos updated", [
                'updated' => $updated,
                'failed' => $failed,
                'total' => $patients->count()
            ]);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Fatal error: {$e->getMessage()}");
            Log::error("Patient photos update command failed", ['error' => $e->getMessage()]);
            return Command::FAILURE;
        }
    }
}

