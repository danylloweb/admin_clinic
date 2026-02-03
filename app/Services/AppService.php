<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psr\Http\Message\StreamInterface;

/**
 * Class AppService
 * @package App\Services
 */
class AppService
{
    /**
     * @var
     */
    protected $repository;

    /**
     * @param int $limit
     * @return mixed
     */
    public function all(int $limit = 20)
    {
        return $this->repository->paginate($limit);
    }

    /**
     * @param array $data
     * @param bool $skipPresenter
     * @return mixed
     */
    public function create(array $data, bool $skipPresenter = false)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $skipPresenter ? $this->repository->skipPresenter()->create($data) : $this->repository->create($data);
    }

    /**
     * @param $id
     * @param bool $skip_presenter
     * @return mixed
     */
    public function find($id, bool $skip_presenter = false)
    {
        if ($skip_presenter) {
            return $this->repository->skipPresenter()->find($id);
        }
        return $this->repository->find($id);
    }

    /**
     * @param array $data
     * @param $id
     * @param bool $skipPresenter
     * @return array|mixed
     */
    public function update(array $data, $id, bool $skipPresenter = false)
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }
        return $skipPresenter ? $this->repository->skipPresenter()->update($data, $id) : $this->repository->update(
            $data,
            $id
        );
    }

    /**
     * @param array $data
     * @param bool $first
     * @param bool $presenter
     * @return mixed
     */
    public function findWhere(array $data, bool $first = false, bool $presenter = false)
    {
        if ($first) {
            return $this->repository->skipPresenter()->findWhere($data)->first();
        }
        if ($presenter) {
            return $this->repository->findWhere($data);
        }
        return $this->repository->skipPresenter()->findWhere($data);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function findLast(array $data)
    {
        return $this->repository->skipPresenter()->findWhere($data)->last();
    }

    /**
     * Remove the specified resource from storage using softDelete.
     * =
     * @param $id
     * @return array
     */
    public function delete($id): array
    {
        return ['success' => (boolean)$this->repository->delete($id)];
    }

    /**
     * @param array $data
     * @return array|StreamInterface
     */
    protected function sendSMS(array $data): void
    {
        try {
            $endpoint = "http://gewinnspiel_notification/sendSms";
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data)
            ];
            $this->getHttpClient()->request('POST', $endpoint, $options)->getBody();
        } catch (GuzzleException $e) {
            Log::info(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
    }

    private function sendMail($data): void
    {
        try {
            $endpoint = "http://gewinnspiel_notification/sendMail";
            $options = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data)
            ];
            $this->getHttpClient()->request('POST', $endpoint, $options)->getBody();
        } catch (GuzzleException $e) {
            Log::info(json_encode(['error' => true, 'message' => $e->getMessage()]));
        }
    }

    /**
     * @param array $data
     * @return array|StreamInterface
     */
    public function sendPushNotification(array $data): void
    {
        try {
            $endpoint = "http://gewinnspiel_notification/sendPushNotification";

            $options = [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode($data)
            ];

            $this->getHttpClient()->request('POST', $endpoint, $options)->getBody();
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }


    /**
     * @return Client
     */
    protected function getHttpClient(): Client
    {
        return new Client(['verify' => false]);
    }

    /**
     * @param $phone
     * @param $message
     * @return mixed
     */
    public function sendMessageToWhatsApp($phone, $message)
    {
        $params = [
            'token' => '19kudd3ash52qthi',
            'to'    => $phone,
            'body'  => $message
        ];
        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ];

        $options = ['form_params' => $params ];
        $request = new Request('POST', 'https://api.ultramsg.com/instance33014/messages/chat', $headers);
        $res     = $this->getHttpClient()->sendAsync($request, $options)->wait();
        return $res->getBody();

    }

    /**
     * @param string $phone
     * @param string $image
     * @param string $caption
     * @return mixed
     */
    public function sendImageToWhatsApp(string $phone, string $image, string $caption): mixed
    {
        try {
            $params = [
                'token'   => '19kudd3ash52qthi',
                'to'      => $phone,
                'image'   => $image,
                'caption' => $caption
            ];
            $options = ['form_params' => $params ];
            $request = new Request('POST', 'https://api.ultramsg.com/instance33014/messages/image', $this->getHeaderUltra());
            $res     = $this->getHttpClient()->sendAsync($request, $options)->wait();
            return $res->getBody();
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
            return ['error' => true,'message' => $e->getMessage()];
        }
    }

    /**
     * @param string $phone
     * @return mixed
     */
    public function getImageToContactWhatsApp(string $phone):mixed
    {
        try {
            $params = [
                'token'  => '19kudd3ash52qthi',
                'chatId' => $phone
            ];
            $uri      = "https://api.ultramsg.com/instance33014/contacts/image?".http_build_query($params);
            $request  = new Request('GET',$uri ,  $this->getHeaderUltra());
            $response = $this->getHttpClient()->sendAsync($request)->wait();
            return json_decode($response->getBody());
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
            return ['error' => true,'message' => $e->getMessage()];
        }
    }
    /**
     * @param $phone
     * @return mixed
     */
    public function getContactWhatsApp($phone): mixed
    {
        $params = [
            'token'  => '19kudd3ash52qthi',
            'chatId' => $this->getContactIdByPhone($phone)
        ];

        $request = new Request('GET', 'https://api.ultramsg.com/instance33014/contacts/contact?'.http_build_query($params), $this->getHeaderUltra());
        $response= $this->getHttpClient()->sendAsync($request)->wait();

        return json_decode($response->getBody());
    }

    /**
     * @param $phone
     * @return mixed
     */
    public function getLastMessagesWhatsApp($chatId): mixed
    {
        $params = [
            'token'  => '19kudd3ash52qthi',
            'chatId' => $chatId,
            'limit'  => '50',
        ];

        $request = new Request('GET', 'https://api.ultramsg.com/instance33014/chats/messages?'.http_build_query($params), $this->getHeaderUltra());
        $res     = $this->getHttpClient()->sendAsync($request)->wait();

        return json_decode($res->getBody(), true);
    }

    public function getLastMessagesByChatId($chatId): mixed
    {

        try {
            $messages = $this->getLastMessagesWhatsApp($chatId);
            usort($messages, function ($a, $b) {
                $ta = isset($a['timestamp']) ? (int)$a['timestamp'] : 0;
                $tb = isset($b['timestamp']) ? (int)$b['timestamp'] : 0;
                return $ta <=> $tb;
            });
            return $messages;
        } catch (\Throwable $e) {
            Log::error('getLastMessagesByChatId error: ' . $e->getMessage());
            return [];
        }

    }

    /**
     * @return mixed
     */
    public function getChats(): mixed
    {
        $params = [
            'token'  => '19kudd3ash52qthi',
        ];

        $request = new Request('GET', 'https://api.ultramsg.com/instance33014/chats?'.http_build_query($params), $this->getHeaderUltra());
        $response= $this->getHttpClient()->sendAsync($request)->wait();

        return json_decode($response->getBody());
    }
    public function getAllChats(): mixed
    {
        $chats = $this->getChats();
        return Cache::store('redis')->tags('allChat')->remember("chat", 5, function () use ($chats) {
            return $chats;
        });
    }
    /**
     * @param $chat_id
     * @return mixed
     */
    public function getConversation($chat_id): mixed
    {
        $params = [
            'token'  => '19kudd3ash52qthi',
            'chatId' => $chat_id,
            'limit'  => '2'
        ];

        $request = new Request('GET', 'https://api.ultramsg.com/instance33014/chats/messages?'.http_build_query($params), $this->getHeaderUltra());
        $response= $this->getHttpClient()->sendAsync($request)->wait();
        return json_decode($response->getBody());
    }

    /**
     * @param $phone
     * @return void
     */
    public function sendAddressToPhone($phone):void
    {
        try {
            $params = [
                'token'   => '19kudd3ash52qthi',
                'to'      => '55'. preg_replace('/[^0-9]/', '', $phone),
                'address' => 'Av. Argentina Castelo Branco, 175 - Loja 1 - Ouro Preto, Olinda - PE, 53370-540',
                'lat'     => '-7.998172464',
                'lng'     => '-34.86067192'
            ];
            $options = ['form_params' => $params ];
            $request = new Request('POST', 'https://api.ultramsg.com/instance33014/messages/location', $this->getHeaderUltra());
            $res     = $this->getHttpClient()->sendAsync($request, $options)->wait();
            $res->getBody();
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
        }
    }

    /**
     * @param string $phone
     * @return string
     */
    public function getContactIdByPhone(string $phone): string
    {
        return "55".preg_replace('/[^0-9]/', '', $phone). '@c.us';
    }

    /**
     * @return string[]
     */
    private function getHeaderUltra(): array
    {
        return ['Content-Type' => 'application/x-www-form-urlencoded'];
    }

    /**
     * @param $phone
     * @return array|mixed
     */
    public function getlinkImageByPhone($phone):mixed
    {
        return Cache::store('redis')->tags('imageProfile')->remember($phone, 1220000, function () use ($phone) {
            return $this->getImageToContactWhatsApp($phone);
        });
    }

    /**
     * @param $var
     * @return bool
     */
    public function isEmpty($var): bool
    {
        return empty($var)
            || (is_string($var) && trim($var) == '')
            || (is_array($var) && count($var) == 0)
            || (($var instanceof Collection) && count($var) == 0)
            || blank($var);
    }

    /**
     * @param string $phone
     * @param string $audio
     * @param string $msgId
     * @return mixed
     */
    public function sendAudioToWhatsApp(string $phone, string $audio, string $msgId = ""): mixed
    {
        try {
            $params = [
                'token'       => '19kudd3ash52qthi',
                'to'          => $phone,
                'audio'       => $audio,
                'priority'    => '',
                'referenceId' => '',
                'nocache'     => '',
                'msgId'       => $msgId
            ];
            $options = ['form_params' => $params ];
            $request = new Request('POST', 'https://api.ultramsg.com/instance33014/messages/audio', $this->getHeaderUltra());
            $res     = $this->getHttpClient()->sendAsync($request, $options)->wait();
            return json_decode($res->getBody());
        } catch (GuzzleException $e) {
            Log::info($e->getMessage());
            return ['error' => true,'message' => $e->getMessage()];
        }
    }

    /**
     * @return mixed
     */
    public function getContacts(): mixed
    {
        $params = [
            'token'  => '19kudd3ash52qthi',
        ];

        $request = new Request('GET', 'https://api.ultramsg.com/instance33014/contacts?'.http_build_query($params), $this->getHeaderUltra());
        $response= $this->getHttpClient()->sendAsync($request)->wait();

        return json_decode($response->getBody());
    }

    /**
     * @param $file_name_content
     * @param $content
     * @return string
     */
    public function putFileS3($file_name_content, $content): string
    {
        $path = config('APP_NAME').$file_name_content;
        Storage::disk('s3')->put($path, $content);
        return Storage::disk('s3')->url($file_name_content);
    }

    /**
     * @param $file_name_content
     * @return void
     */
    public function deleteFileS3($file_name_content): void
    {
        try {
            $file = str_replace("https://msadmin.s3.amazonaws.com/",'',$file_name_content);
            Storage::disk('s3')->delete($file);
        }catch (\Exception $exception) {

        }

    }
}
