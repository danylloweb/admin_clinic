<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Criterias\FilterByProcedureStatusActiveCriteria;
use App\Criterias\FilterByProcedureTypeScheduleCriteria;
use App\Criterias\FilterByTypePackageScheduleCriteria;
use App\Criterias\FilterByTypeProcedureScheduleCriteria;
use App\Repositories\ProcedureRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * ProcedureService
 */
class ProcedureService extends AppService
{
    /**
     * @var ProcedureRepository
     */
    protected $repository;

    /**
     * @param ProcedureRepository $repository
     */
    public function __construct(ProcedureRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param int $limit
     * @return mixed
     * @throws RepositoryException
     */
    public function all($limit = 20)
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(FilterByTypePackageScheduleCriteria::class))
            ->pushCriteria(app(FilterByProcedureTypeScheduleCriteria::class))
            ->pushCriteria(app(FilterByProcedureStatusActiveCriteria::class))
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

    /**
     * @param array $data
     * @param $skipPresenter
     * @return mixed
     */
    public function create(array $data, $skipPresenter = false)
    {
        $data['cost_price'] = floatval( $data['cost_price']);
        $data['price'] = $data['cost_price'] + ($data['cost_price'] / 100 * $data['percentage_on_sale']);
        return $this->repository->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param bool $skipPresenter
     * @return array|mixed
     */
    public function update(array $data, $id, bool $skipPresenter = false)
    {
        if (isset($data['cost_price'])){
            $data['cost_price'] = floatval( $data['cost_price']);
            $data['price'] = $data['cost_price'] + ($data['cost_price'] / 100 * $data['percentage_on_sale']);
        }
        return parent::update($data, $id, $skipPresenter);
    }

    /**
     * @param $id
     * @return array
     */
    public function delete($id): array
    {
        try {
            $this->repository->delete($id);
            return ['message' => 'deleted'];
        }catch (\Exception $exception){
            return parent::update(['status' => 0], $id);
        }
    }

    public function replicate(int $id)
    {
        $procedure     = $this->repository->skipPresenter()->find($id);
        $precedure_new = $procedure->replicate();
        $precedure_new->save();
        $procedure->status = 0;
        $procedure->save();
        return $precedure_new;
    }

}
