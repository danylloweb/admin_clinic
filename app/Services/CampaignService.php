<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\CampaignRepository;
use App\Repositories\PatientRepository;
use Illuminate\Support\Facades\Cache;
use Prettus\Repository\Exceptions\RepositoryException;

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
     * @param PatientRepository $patientRepository
     */
    public function __construct(CampaignRepository                 $repository,
                                private readonly PatientRepository $patientRepository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws RepositoryException
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
        $not        = [75,549,238,251,253,412,322,426,635,180,181,217,214,230];
        $patients   = $this->patientRepository->skipPresenter()->findWhereNotIn('id',$not);
        $state = [
            'campaign_id'     => $campaignId,
            'campaign_name'   => $campaign->name,
            'total'           => count($patients),
            'sent'            => 0,
            'failed'          => 0,
            'processed'       => 0,
            'last_patient_id' => 0,
            'running'         => true,
            'finished'        => false,
            'started_at'      => now()->toDateTimeString(),
            'finished_at'     => null,
            'updated_at'      => now()->toDateTimeString(),
        ];

        Cache::put($this->dispatchCacheKey($campaignId), $state, now()->addHours(12));

        return $state;
    }

    public function dispatchProgress(int $campaignId): array
    {
        return Cache::get($this->dispatchCacheKey($campaignId), [
            'campaign_id'     => $campaignId,
            'total'           => 0,
            'sent'            => 0,
            'failed'          => 0,
            'processed'       => 0,
            'last_patient_id' => 0,
            'running'         => false,
            'finished'        => false,
            'started_at'      => null,
            'finished_at'     => null,
            'updated_at'      => null,
        ]);
    }

    public function dispatchCacheKey(int $campaignId): string
    {
        return 'campaign_dispatch_'.$campaignId;
    }

    public function sendTestToPatient(int $campaignId, int $patientId = 1): array
    {
        try {
            $campaign = $this->repository->skipPresenter()->find($campaignId);
            $patient = $this->patientRepository->skipPresenter()->find($patientId);

            if (empty($patient?->chat_id)) {
                return ['error' => true, 'message' => 'Paciente de teste sem chat_id configurado.'];
            }

            $name = $patient->social_name ?: $patient->name;
            $message = str_replace('{name}', $name, (string) $campaign->description);

            $providerResponse = $this->sendImageToWhatsApp((string) $patient->chat_id, (string) $campaign->url_image, $message);

            return [
                'error' => false,
                'message' => 'Mensagem de teste enviada com sucesso.',
                'data' => [
                    'campaign_id' => $campaignId,
                    'patient_id' => $patientId,
                    'chat_id' => $patient->chat_id,
                    'provider_response' => $providerResponse,
                ],
            ];
        } catch (\Throwable $exception) {
            return ['error' => true, 'message' => $exception->getMessage()];
        }
    }

}
