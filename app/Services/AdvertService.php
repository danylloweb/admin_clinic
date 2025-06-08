<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\AdvertRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * AdvertService
 */
class AdvertService extends AppService
{
    /**
     * @var AdvertRepository
     */
    protected $repository;

    /**
     * @param AdvertRepository $repository
     */
    public function __construct(AdvertRepository $repository)
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

    /**
     * @param array $data
     * @return string[]
     */
    public function registerClick(array $data): array
    {
        $advert = $this->repository->skipPresenter()->findWhere(['code'=> $data['code']])->first();
        $advert->qty_click_confirmed++;
        $advert->save();
        return ['message' => 'registrado com Sucesso'];
    }

    /**
     * @param array $data
     * @return string[]
     */
    public function registerClickCheckout(array $data): array
    {
        $advert = $this->repository->skipPresenter()->findWhere(['code'=> $data['code']])->first();
        $advert->qty_click_checkout++;
        $advert->save();
        return ['message' => 'checkout registrado com Sucesso'];
    }
}
