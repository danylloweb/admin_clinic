<?php

namespace App\Services;

use App\Entities\Patient;
use App\Criterias\AppRequestCriteria;
use App\Repositories\CampaignRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * CampaignService
 */
class CampaignService extends AppService
{
    /**
     * @var CampaignRepository
     */
    protected $repository;

    /**
     * @param CampaignRepository $repository
     */
    public function __construct(CampaignRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function all(int $limit = 20): mixed
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

    public function create(array $data, bool $skipPresenter = false): mixed
    {
        if (!empty($data['url_image']) && str_starts_with((string) $data['url_image'], 'data:image/')) {
            $data['url_image'] = $this->uploadCampaignImage((string) $data['url_image']);
        }

        return parent::create($data, $skipPresenter);
    }

    public function update(array $data, $id, bool $skipPresenter = false): mixed
    {
        if (!empty($data['url_image']) && str_starts_with((string) $data['url_image'], 'data:image/')) {
            $campaign = $this->repository->skipPresenter()->find($id);
            if (!empty($campaign?->url_image)) {
                $this->deleteFileS3($campaign->url_image);
            }
            $data['url_image'] = $this->uploadCampaignImage((string) $data['url_image']);
        }

        return parent::update($data, $id, $skipPresenter);
    }

    private function uploadCampaignImage(string $base64Image): string
    {
        $content = base64_decode((string) preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        $fileName = 'campaigns/' . date('dmYHis') . uniqid('', true) . '.jpeg';
        return $this->putFileS3($fileName, $content);
    }

    public function startDispatch(int $campaignId): array
    {
        $campaign = $this->repository->skipPresenter()->find($campaignId);
        $total = Patient::query()->count();

        $state = [
            'campaign_id' => $campaignId,
            'campaign_name' => $campaign->name,
            'total' => $total,
            'sent' => 0,
            'failed' => 0,
            'processed' => 0,
            'last_patient_id' => 0,
            'running' => true,
            'finished' => false,
            'started_at' => now()->toDateTimeString(),
            'finished_at' => null,
            'updated_at' => now()->toDateTimeString(),
        ];

        Cache::put($this->dispatchCacheKey($campaignId), $state, now()->addHours(12));

        return $state;
    }

    public function dispatchProgress(int $campaignId): array
    {
        return Cache::get($this->dispatchCacheKey($campaignId), [
            'campaign_id' => $campaignId,
            'total' => 0,
            'sent' => 0,
            'failed' => 0,
            'processed' => 0,
            'last_patient_id' => 0,
            'running' => false,
            'finished' => false,
            'started_at' => null,
            'finished_at' => null,
            'updated_at' => null,
        ]);
    }

    public function processDispatchPage(int $campaignId, int $page = 1, int $perPage = 5): array
    {
        $state = $this->dispatchProgress($campaignId);
        if ($state['finished'] ?? false) {
            return $state;
        }

        $campaign = $this->repository->skipPresenter()->find($campaignId);
        if (empty($state['started_at'])) {
            $state['campaign_name'] = $campaign->name;
            $state['total'] = Patient::query()->count();
            $state['started_at'] = now()->toDateTimeString();
            $state['running'] = true;
        }

        $perPage = max(1, min($perPage, 100));
        $offset = ($page - 1) * $perPage;

        $patients = Patient::query()
            ->orderBy('id')
            ->offset($offset)
            ->limit($perPage)
            ->get(['id', 'name', 'social_name', 'phone', 'chat_id']);

        if ($patients->isEmpty()) {
            $state['running'] = false;
            $state['finished'] = true;
            $state['finished_at'] = now()->toDateTimeString();
            $state['updated_at'] = now()->toDateTimeString();
            Cache::put($this->dispatchCacheKey($campaignId), $state, now()->addHours(12));
            return $state;
        }

        foreach ($patients as $patient) {
            try {
                $name    = $patient->social_name ?: $patient->name;
                $chatId  = $patient->chat_id;
                $message = $campaign->description;
                $message = str_replace('{name}', $name, $message);

                if (!empty($campaign->url_image)) {
                    $this->sendImageToWhatsApp("558185879004@c.us", (string) $campaign->url_image, $message);
                } else {
                    $this->sendMessageToWhatsApp("558185879004@c.us", $message);
                }

                $state['sent'] = (int) ($state['sent'] ?? 0) + 1;
            } catch (\Throwable $exception) {
                $state['failed'] = (int) ($state['failed'] ?? 0) + 1;
                Log::warning('Campaign dispatch error campaign='.$campaignId.' patient='.$patient->id.' '.$exception->getMessage());
            }

            $state['processed'] = (int) ($state['processed'] ?? 0) + 1;
        }

        $state['running'] = true;
        $state['updated_at'] = now()->toDateTimeString();

        if (count($patients) < $perPage) {
            $state['running'] = false;
            $state['finished'] = true;
            $state['finished_at'] = now()->toDateTimeString();
        }

        Cache::put($this->dispatchCacheKey($campaignId), $state, now()->addHours(12));
        return $state;
    }

    public function processNextDispatchBatch(int $campaignId, int $batchSize = 20): array
    {
        $state = $this->dispatchProgress($campaignId);
        if ($state['finished'] ?? false) {
            return $state;
        }

        $campaign = $this->repository->skipPresenter()->find($campaignId);
        if (empty($state['started_at'])) {
            $state['campaign_name'] = $campaign->name;
            $state['total'] = Patient::query()->count();
            $state['started_at'] = now()->toDateTimeString();
            $state['running'] = true;
        }
        $lastPatientId = (int) ($state['last_patient_id'] ?? 0);
        $patients = Patient::query()
            ->where('id', '>', $lastPatientId)
            ->orderBy('id')
            ->limit(max(1, min($batchSize, 100)))
            ->get(['id', 'name', 'social_name', 'phone', 'chat_id']);

        if ($patients->isEmpty()) {
            $state['running'] = false;
            $state['finished'] = true;
            $state['finished_at'] = now()->toDateTimeString();
            $state['updated_at'] = now()->toDateTimeString();
            Cache::put($this->dispatchCacheKey($campaignId), $state, now()->addHours(12));
            return $state;
        }

        foreach ($patients as $patient) {
            try {
                $name = $patient->social_name ?: $patient->name;
                $chatId = $patient->chat_id ?: $this->getContactIdByPhone((string) $patient->phone);
                $message = "Ola {$name}, tudo bem? {$campaign->description}";

                if (!empty($campaign->url_image)) {
                    $this->sendImageToWhatsApp("558185879004@c.us", (string) $campaign->url_image, $message);
                } else {
                    $this->sendMessageToWhatsApp("558185879004@c.us", $message);
                }

                $state['sent'] = (int) ($state['sent'] ?? 0) + 1;
            } catch (\Throwable $exception) {
                $state['failed'] = (int) ($state['failed'] ?? 0) + 1;
                Log::warning('Campaign dispatch error campaign='.$campaignId.' patient='.$patient->id.' '.$exception->getMessage());
            }

            $state['processed'] = (int) ($state['processed'] ?? 0) + 1;
            $state['last_patient_id'] = $patient->id;
        }

        $state['running'] = true;
        $state['updated_at'] = now()->toDateTimeString();

        if (count($patients) < $batchSize) {
            $state['running'] = false;
            $state['finished'] = true;
            $state['finished_at'] = now()->toDateTimeString();
        }

        Cache::put($this->dispatchCacheKey($campaignId), $state, now()->addHours(12));
        return $state;
    }

    private function dispatchCacheKey(int $campaignId): string
    {
        return 'campaign_dispatch_'.$campaignId;
    }

}
