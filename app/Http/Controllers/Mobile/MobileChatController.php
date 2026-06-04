<?php

namespace App\Http\Controllers\Mobile;

use App\Services\AppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class MobileChatController extends MobileController
{
    public function __construct(private readonly AppService $appService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $page = max((int) $request->query('page', 1), 1);
        $perPage = min(max((int) $request->query('per_page', 10), 1), 50);
        $search = trim((string) $request->query('search', ''));

        $rows = $this->normalizeCollection($this->appService->getAllChats())
            ->map(fn (array $chat) => $this->normalizeChat($chat))
            ->filter(function (array $chat) use ($search) {
                if ($search === '') {
                    return true;
                }

                $haystack = mb_strtolower(implode(' ', [
                    (string) ($chat['id'] ?? ''),
                    (string) ($chat['name'] ?? ''),
                    (string) ($chat['last_message'] ?? ''),
                ]));

                return str_contains($haystack, mb_strtolower($search));
            })
            ->sortByDesc('timestamp')
            ->values();

        $total = $rows->count();
        $slice = $rows->slice(($page - 1) * $perPage, $perPage)->values()->all();

        $paginator = new LengthAwarePaginator($slice, $total, $perPage, $page);

        return $this->paginatedResponse($paginator, $slice, [
            'filters_applied' => [
                'search' => $search,
            ],
        ]);
    }

    public function messages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_id' => 'required|string',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 50);

        $rows = $this->normalizeCollection($this->appService->getLastMessagesByChatId((string) $validated['chat_id']))
            ->map(fn (array $message) => $this->normalizeMessage($message))
            ->sortBy('timestamp')
            ->values();

        $total = $rows->count();
        $slice = $rows->slice(($page - 1) * $perPage, $perPage)->values()->all();
        $paginator = new LengthAwarePaginator($slice, $total, $perPage, $page);

        return $this->paginatedResponse($paginator, $slice, [
            'chat_id' => (string) $validated['chat_id'],
        ]);
    }

    public function sendText(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_id' => 'required|string',
            'body' => 'required|string|max:5000',
        ]);

        $providerResponse = $this->appService->sendMessageToWhatsApp($validated['chat_id'], $validated['body']);

        return response()->json([
            'success' => true,
            'data' => [
                'type' => 'chat',
                'chat_id' => $validated['chat_id'],
                'body' => $validated['body'],
                'provider' => $this->normalizeProviderResponse($providerResponse),
            ],
        ]);
    }

    public function sendImage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_id' => 'required|string',
            'image_url' => 'required|url',
            'caption' => 'nullable|string|max:1000',
        ]);

        $providerResponse = $this->appService->sendImageToWhatsApp(
            $validated['chat_id'],
            $validated['image_url'],
            (string) ($validated['caption'] ?? '')
        );

        return response()->json([
            'success' => true,
            'data' => [
                'type' => 'image',
                'chat_id' => $validated['chat_id'],
                'image_url' => $validated['image_url'],
                'caption' => (string) ($validated['caption'] ?? ''),
                'provider' => $this->normalizeProviderResponse($providerResponse),
            ],
        ]);
    }

    public function sendAudio(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'chat_id' => 'required|string',
            'audio_url' => 'required|url',
            'msg_id' => 'nullable|string',
        ]);

        $providerResponse = $this->appService->sendAudioToWhatsApp(
            $validated['chat_id'],
            $validated['audio_url'],
            (string) ($validated['msg_id'] ?? '')
        );

        return response()->json([
            'success' => true,
            'data' => [
                'type' => 'audio',
                'chat_id' => $validated['chat_id'],
                'audio_url' => $validated['audio_url'],
                'provider' => $this->normalizeProviderResponse($providerResponse),
            ],
        ]);
    }

    private function normalizeCollection(mixed $payload)
    {
        $arrayPayload = json_decode(json_encode($payload), true);

        if (!is_array($arrayPayload)) {
            return collect();
        }

        if (isset($arrayPayload['data']) && is_array($arrayPayload['data'])) {
            return collect($arrayPayload['data']);
        }

        if (isset($arrayPayload['chats']) && is_array($arrayPayload['chats'])) {
            return collect($arrayPayload['chats']);
        }

        return collect(array_values($arrayPayload));
    }

    private function normalizeChat(array $chat): array
    {
        return [
            'id' => (string) ($chat['id'] ?? $chat['chatId'] ?? $chat['chat_id'] ?? ''),
            'name' => (string) ($chat['name'] ?? $chat['title'] ?? $chat['contactName'] ?? $chat['phone'] ?? 'Sem nome'),
            'avatar' => (string) ($chat['avatar'] ?? $chat['profilePic'] ?? ''),
            'last_message' => (string) ($chat['last_message'] ?? $chat['lastMessage'] ?? $chat['body'] ?? ''),
            'timestamp' => (int) ($chat['timestamp'] ?? $chat['time'] ?? 0),
            'unread' => (int) ($chat['unread'] ?? $chat['unread_count'] ?? 0),
        ];
    }

    private function normalizeMessage(array $message): array
    {
        return [
            'id' => (string) ($message['id'] ?? $message['msgId'] ?? ''),
            'from_me' => (bool) ($message['fromMe'] ?? false),
            'type' => (string) ($message['type'] ?? 'chat'),
            'body' => (string) ($message['body'] ?? ''),
            'media' => (string) ($message['media'] ?? $message['url'] ?? ''),
            'timestamp' => (int) ($message['timestamp'] ?? 0),
        ];
    }

    private function normalizeProviderResponse(mixed $response): mixed
    {
        if (is_array($response) || is_object($response)) {
            return $response;
        }

        $string = (string) $response;
        $decoded = json_decode($string, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $string;
    }
}

