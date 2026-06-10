<?php

namespace App\Services;

use App\Entities\ProcedureConsumption;
use App\Repositories\ProductLotRepository;

class ProcedureConsumptionService
{
    protected $lotRepository;

    public function __construct(ProductLotRepository $lotRepository)
    {
        $this->lotRepository = $lotRepository;
    }

    public function recordConsumption(array $data): ProcedureConsumption
    {
        $consumption = ProcedureConsumption::create([
            'product_id' => $data['product_id'],
            'product_lot_id' => $data['product_lot_id'] ?? null,
            'patient_id' => $data['patient_id'],
            'aesthetic_procedure_evolution_id' => $data['aesthetic_procedure_evolution_id'],
            'professional_id' => auth()->id(),
            'quantity_used' => $data['quantity_used'],
            'consumption_date' => now(),
            'notes' => $data['notes'] ?? null,
        ]);

        // Consumir quantidade do lote
        if ($data['product_lot_id']) {
            $lot = $this->lotRepository->skipPresenter()->find($data['product_lot_id']);
            if ($lot) {
                $lot->quantity_available -= $data['quantity_used'];
                $lot->updateStatus();
                $lot->save();
            }
        }

        return $consumption;
    }

    public function getPatientHistory($patientId, $limit = 50)
    {
        return ProcedureConsumption::where('patient_id', $patientId)
            ->orderBy('consumption_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getProductHistory($productId, $limit = 50)
    {
        return ProcedureConsumption::where('product_id', $productId)
            ->orderBy('consumption_date', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getConsumptionByEvolution($evolutionId)
    {
        return ProcedureConsumption::where('aesthetic_procedure_evolution_id', $evolutionId)->get();
    }
}

