<?php

namespace App\Services;

use App\Criterias\AppRequestCriteria;
use App\Repositories\ProductRepository;
use App\Repositories\ProductLotRepository;

class ProductService extends AppService
{
    protected $repository;
    protected $lotRepository;

    public function __construct(ProductRepository $repository, ProductLotRepository $lotRepository
    ) {
        $this->repository = $repository;
        $this->lotRepository = $lotRepository;
    }

    /**
     * @param $limit
     * @return mixed
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function all($limit = 20)
    {
        return $this->repository
            ->resetCriteria()
            ->pushCriteria(app(AppRequestCriteria::class))
            ->paginate($limit);
    }

    public function create(array $data, bool $skipPresenter = false)
    {
        $data['created_by'] = (string) auth()->id();
        $data['current_stock'] = 0;
        return $skipPresenter ? $this->repository->skipPresenter()->create($data) : $this->repository->create($data);
    }

    public function update(array $data, $id, bool $skipPresenter = false)
    {
        $product = $this->repository->skipPresenter()->find($id);
        if (!$product) {
            throw new \Exception('Produto não encontrado.');
        }

        $data['updated_by'] = (string) auth()->id();

        // Registrar mudanças de campos críticos
        foreach (['minimum_stock', 'ideal_stock', 'storage_location', 'image_url'] as $field) {
            if (isset($data[$field]) && $product->{$field} !== $data[$field]) {
                $product->recordChange($field, $product->{$field}, $data[$field], (string) auth()->id());
            }
        }
        $product->save();

        return $skipPresenter ? $this->repository->skipPresenter()->update($data, $id) : $this->repository->update($data, $id);
    }

    public function getPrimaryImage($productId): ?string
    {
        $product = $this->repository->skipPresenter()->find($productId);
        if ($product) {
            $image = $product->images()->where('is_primary', true)->first();
            return $image?->url;
        }
        return null;
    }

    public function getAlertsByProduct($productId): array
    {
        $product = $this->repository->skipPresenter()->find($productId);
        if (!$product) {
            return [];
        }

        $alerts = [];

        // Alertas de estoque mínimo
        if ($product->current_stock <= $product->minimum_stock) {
            $alerts[] = [
                'type' => 'Estoque baixo',
                'message' => 'Estoque abaixo do mínimo permitido',
                'severity' => 'danger',
            ];
        }

        // Alertas de lotes vencidos
        $expiredLots = $product->lots()
            ->where('status', 'expired')
            ->count();
        if ($expiredLots > 0) {
            $alerts[] = [
                'type' => 'expired_lots',
                'message' => "Existem $expiredLots lotes vencidos",
                'severity' => 'danger',
            ];
        }

        // Alertas de vencimento próximo
        $nearExpirationLots = $product->lots()
            ->where('status', 'near_expiration')
            ->count();
        if ($nearExpirationLots > 0) {
            $alerts[] = [
                'type' => 'near_expiration',
                'message' => "$nearExpirationLots lotes vencerão em até 30 dias",
                'severity' => 'warning',
            ];
        }

        return $alerts;
    }

    public function getProductsByCategory($categoryType, int $limit = 20)
    {
        return $this->repository->findWhere(['category_type' => $categoryType], false);
    }
}

