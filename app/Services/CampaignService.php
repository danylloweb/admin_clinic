<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\CampaignRepository;

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

}
