<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\LeadRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * LeadService
 */
class LeadService extends AppService
{
    /**
     * @var LeadRepository
     */
    protected $repository;

    /**
     * @param LeadRepository $repository
     */
    public function __construct(LeadRepository $repository)
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

    public function create(array $data, bool $skipPresenter = false)
    {
        $lead = parent::create($data, $skipPresenter);
        $this->sendMessageAdvert($lead['data']['id']);
        return $lead;
    }

    /**
     * @param $id
     * @return array
     */
    public function sendMessageAdvert($id):array
    {
        $lead = $this->repository->skipPresenter()->find($id);
        $message = "Olá $lead->name, ".$lead->advert->message_to_lead;
        $this->sendMessageToWhatsApp('55'.$lead->phone, $message);
        return ['error'=> false,'message'=> 'Enviado!'];
    }

}
