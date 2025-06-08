<?php

namespace App\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Interface ScheduleRepository.
 *
 * @package namespace App\Repositories;
 */
interface ScheduleRepository extends RepositoryInterface
{
    public function getPatientsByLaser(): array;
}
