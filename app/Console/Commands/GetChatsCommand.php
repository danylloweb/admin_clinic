<?php

namespace App\Console\Commands;

use App\Models\Chat;
use App\Services\LeadService;
use Illuminate\Console\Command;

/**
 *
 */
class GetChatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:run';
    protected Chat $chatRepository;
    protected LeadService $leadService;

    /**
     * @var string
     */
    protected $description = 'get chats';

    public function __construct(Chat $chatRepository, LeadService $leadService)
    {
        $this->chatRepository = $chatRepository;
        $this->leadService    = $leadService;
        parent::__construct();
    }
    public function handle()
    {
       $chats = $this->leadService->getChats();

        foreach ($chats as $chat) {
           $data_create = [
               "chat_id"      => $chat->id,
               "last_time"    => date('d/m/Y H:i:s', $chat->last_time),
               "timestamp"    => $chat->last_time,
               "name"         => $chat->name,
               "last_message" => "FirstMessage",
           ];
            $this->chatRepository->create($data_create);
       }
    }
}
