<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class HealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'health:run';

    /**
     * @var string
     */
    protected $description = 'health check';


    public function handle()
    {
        echo "schedule health check\n";
        Log::info('schedule', [
            'message' => "Message sent to schedule health check",
            'data'    => "schedule health check"
        ]);
    }
}
