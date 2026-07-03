<?php

namespace App\Console\Commands;

use App\Entities\AestheticProcedureEvolution;
use App\Services\AestheticProcedureEvolutionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReprocessAestheticProcedureEvolutionSignaturesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aesthetic-procedure-evolutions:reprocess-signatures
                            {--chunk=200 : Number of records per chunk}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocess legacy signatures for all AestheticProcedureEvolution records';

    public function __construct(private readonly AestheticProcedureEvolutionService $service)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $chunkSize = max(1, (int) $this->option('chunk'));
        $total = AestheticProcedureEvolution::query()->count();

        if ($total === 0) {
            $this->info('No AestheticProcedureEvolution records found.');
            return Command::SUCCESS;
        }

        $this->info("Reprocessing signatures for {$total} records...");

        $processed = 0;
        $updated = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        AestheticProcedureEvolution::query()
            ->orderBy('id')
            ->chunkById($chunkSize, function ($records) use (&$processed, &$updated, &$failed, $bar) {
                foreach ($records as $record) {
                    $processed++;

                    $patientSignature = is_string($record->patient_signature) ? trim($record->patient_signature) : '';
                    $professionalSignature = is_string($record->professional_signature) ? trim($record->professional_signature) : '';

                    // Ignore entries that do not contain signatures.
                    if ($patientSignature === '' && $professionalSignature === '') {
                        $bar->advance();
                        continue;
                    }

                    try {
                        $this->service->update([
                            'patient_signature' => $record->patient_signature,
                            'professional_signature' => $record->professional_signature,
                        ], $record->id, true);

                        $updated++;
                    } catch (\Throwable $exception) {
                        $failed++;

                        Log::error('Failed to reprocess aesthetic procedure evolution signatures.', [
                            'aesthetic_procedure_evolution_id' => $record->id,
                            'error' => $exception->getMessage(),
                        ]);
                    }

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine(2);

        $this->table(['Metric', 'Value'], [
            ['Total', $total],
            ['Processed', $processed],
            ['Updated', $updated],
            ['Failed', $failed],
        ]);

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

