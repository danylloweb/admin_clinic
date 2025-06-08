<?php

namespace App\Services;

use App\Models\Dashboard;

/**
 * DashboardService
 */
class DashboardService
{
    /**
     * @var Dashboard
     */
    protected Dashboard $repository;

    public function __construct()
    {
        $this->repository = new Dashboard;
    }

    /**
     * @return mixed
     */
    public function view(): mixed
    {
      return $this->repository->query()->where("uuid", 1)->first();
    }

    /**
     * @return void
     */
    public function setQtyPatient(): void
    {
        $dashboard = $this->view();
        $dashboard->qty_patients = $dashboard->qty_patients + 1;
        $dashboard->save();
    }

    /**
     * @return void
     */
    public function setQtyProcedures(): void
    {
        $dashboard = $this->view();
        if ($dashboard) {
            $dashboard->qty_procedures = $dashboard->qty_procedures + 1;
            $dashboard->save();
        }
    }

    /**
     * @return void
     */
    public function setQtySchedules(): void
    {
        $dashboard = $this->view();
        if ($dashboard){
            $dashboard->qty_schedules = $dashboard->qty_schedules + 1;
            $dashboard->save();
        }
    }

    /**
     * @return void
     */
    public function setQtyScreenings(): void
    {
        $dashboard = $this->view();
        $dashboard->qty_screenings = $dashboard->qty_screenings + 1;
        $dashboard->save();
    }

    public function firstCreate($data)
    {
        $data_create = [
            "uuid" => 1,
            "qty_patients"   => $data['qty_patients'],
            "qty_procedures" => $data['qty_procedures'],
            "qty_schedules"  => $data['qty_schedules'],
            "qty_screenings" => $data['qty_screenings'],
            "balance"        => 0,
        ];
        $this->repository->create($data_create);
    }

}
