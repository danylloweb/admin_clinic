<?php

namespace App\Console\Commands;

use App\Queues\KafkaQueue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Class KafkaConsumer
 */
class KafkaConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command kafka consumer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $kafka   = new KafkaQueue('app_clinic.group-local', 'process_message.event');
        $message = $kafka->consume();
        Log::info('Kafka to send message queue', [
            'message' => "Message sent to Kafka",
            'data'    => $message
        ]);
    }
}
