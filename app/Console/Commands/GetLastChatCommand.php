<?php

namespace App\Console\Commands;

use App\Models\Chat;
use App\Services\LeadService;
use Illuminate\Console\Command;

/**
 *
 */
class GetLastChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'conversaions:run';
    // 'php artisan conversaions:run';
    protected Chat $chatRepository;
    protected LeadService $leadService;

    /**
     * @var string
     */
    protected $description = 'get conversaions';

    public function __construct(Chat $chatRepository, LeadService $leadService)
    {
        $this->chatRepository = $chatRepository;
        $this->leadService    = $leadService;
        parent::__construct();
    }

    public function handle()
    {
        $this->processMessage();
        sleep(10);
        $this->processMessage();
    }

    public function getTextFromMessage($message)
    {
        $adverts = config('message_reply');
        foreach ($adverts as $advert) {
            if ($advert['message'] === $message) {
                return $advert['text'];
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getMessageHourWorker(): string
    {
        return "Nosso horario de atendimento é de segunda a sexta das 8h as 20h, Sábado das 8h as 14h.";
    }

    /**
     * @return void
     */
    public function processMessage(): void
    {
        $chats = $this->leadService->getChats();

        for ($item = 0;$item < 2; $item++){
            $chat          = $this->chatRepository->where("chat_id",$chats[$item]->id)->first();
            if ($chats[$item]->id == "120363240782560471@g.us"){
                continue;
            }
            $conversations = $this->leadService->getConversation($chats[$item]->id);
            $last_message  = 'first';
            foreach ($conversations as $conversation) {
                if($this->leadService->isEmpty($conversation->fromMe) && $conversation->type == "chat") $last_message = $conversation->body;
            }
            if($this->leadService->isEmpty($chat)){
                $data_create = [
                    "chat_id"      => $chats[$item]->id,
                    "last_time"    => date('d/m/Y H:i:s', $chats[$item]->last_time),
                    "name"         => $chats[$item]->name,
                    "last_message" => $last_message,
                ];
                $this->chatRepository->create($data_create);
                $reply_message = $this->getTextFromMessage($last_message);
                if ($reply_message){
                    $this->leadService->sendMessageToWhatsApp($chats[$item]->id,$reply_message);
                    echo "mensagem enviada para ". $chats[$item]->id."\n";
                }
            }else{
                if ($chat->last_message !== $last_message){
                    $chat->last_message = $last_message;
                    $chat->name         = $chats[$item]->name;
                    $chat->save();
                    $reply_message = $this->getTextFromMessage($last_message);
                    if ($reply_message){
                        $this->leadService->sendMessageToWhatsApp($chats[$item]->id,$reply_message);
                        echo "mensagem enviada para ". $chats[$item]->id."\n";
                    }
                }
            }
        }
    }
}
