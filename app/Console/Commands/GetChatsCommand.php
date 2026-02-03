<?php

namespace App\Console\Commands;

use App\Entities\AvatarChat;
use App\Models\Chat;
use App\Services\LeadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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
        $output = new ConsoleOutput();
        $chats = $this->leadService->getChats();
        $progress = new ProgressBar($output, count($chats));
        Log::info("Starting caching chats with avatars...");
        $progress->start();
        $chats = Cache::store('redis')->tags('allChat')->remember("chat", 1220000, function () use ($chats, $progress) {
            foreach ($chats as $key => $chat) {
                $chatId = null;
                $progress->advance();
                if (is_object($chat) && property_exists($chat, 'id')) {
                    $chatId = $chat->id;
                } elseif (is_array($chat) && array_key_exists('id', $chat)) {
                    $chatId = $chat['id'];
                }

                $avatar = null;
                if (!empty($chatId)) {
                    $avatar = $this->getAvatar($chatId);
                }

                // attach avatar to the element in the original collection/array
                if (is_object($chat)) {
                    $chats[$key]->avatar = $avatar;
                } elseif (is_array($chat)) {
                    $chats[$key]['avatar'] = $avatar;
                }

            }
            return $chats;
        });
        $progress->finish();
    }

    private function getAvatar($chatId)
    {
        $avatar     = $this->leadService->getlinkImageByPhone($chatId);
        $avatarChat = AvatarChat::create([
            'chat_id' => $chatId,
            'avatar'  => $avatar->success??"https://static.vecteezy.com/ti/vetor-gratis/p1/26434417-padrao-avatar-perfil-icone-do-social-meios-de-comunicacao-do-utilizador-foto-vetor.jpg",
        ]);
        return $avatarChat->avatar;
    }
}

