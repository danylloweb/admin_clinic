<?php

namespace App\Services;

use App\Repositories\ProductLotRepository;
use App\Repositories\ProductRepository;

class ProductLotService extends AppService
{
    protected $repository;
    protected $productRepository;

    public function __construct(
        ProductLotRepository $repository,
        ProductRepository $productRepository
    ) {
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function create(array $data, bool $skipPresenter = false)
    {
        if (!isset($data['quantity_available']) || $data['quantity_available'] === null || $data['quantity_available'] === '') {
            $data['quantity_available'] = (int) ($data['quantity_received'] ?? 0);
        }

        $lot = $this->repository->create($data);
        $lot->updateStatus();
        $lot->save();

        // Atualizar estoque do produto
        $this->updateProductStock($lot->product_id);

        return $skipPresenter ? $this->repository->skipPresenter()->find($lot->id) : $lot;
    }

    public function getNextByFEFO($productId)
    {
        // First Expire First Out: pega lote com vencimento mais próximo
        return $this->repository->skipPresenter()
            ->findWhere(['product_id' => $productId])
            ->where('quantity_available', '>', 0)
            ->where('status', '!=', 'expired')
            ->sortBy('expiration_date')
            ->first();
    }

    public function consumeQuantity($lotId, $quantity): bool
    {
        $lot = $this->repository->skipPresenter()->find($lotId);
        if (!$lot || $lot->quantity_available < $quantity) {
            return false;
        }

        $lot->quantity_available -= $quantity;
        $lot->save();
        $lot->updateStatus();
        $lot->save();

        $this->updateProductStock($lot->product_id);
        return true;
    }

    private function updateProductStock($productId): void
    {
        $product = $this->productRepository->skipPresenter()->find($productId);
        if (!$product) {
            return;
        }

        $totalStock = $this->repository->skipPresenter()
            ->findWhere(['product_id' => $productId])
            ->sum('quantity_available');

        $product->current_stock = $totalStock;
        $product->save();
    }

    public function updateAllStatuses(): void
    {
        $lots = $this->repository->skipPresenter()->all();
        foreach ($lots as $lot) {
            $lot->updateStatus();
            $lot->save();
        }
    }

    public function getLotsNearExpiration($daysThreshold = 30)
    {
        return $this->repository->skipPresenter()->findWhere([
            ['status', 'near_expiration']
        ])->get();
    }

    public function getExpiredLots()
    {
        return $this->repository->skipPresenter()->findWhere([
            ['status', 'expired']
        ])->get();
    }

    public function getByProduct(int $productId)
    {
        return $this->repository
            ->skipPresenter()
            ->findWhere(['product_id' => $productId])
            ->sortByDesc('created_at')
            ->values();
    }
}

