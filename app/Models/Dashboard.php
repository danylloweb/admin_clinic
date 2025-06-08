<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Dashboard extends Model
{
    /**
     * @var string
     */
    protected $connection = 'mongodb';
    /**
     * @var string
     */
    protected $collection = 'dashboards';
    /**
     * @var string[]
     */
    protected $fillable = [
        'uuid',
        'qty_patients',
        'qty_procedures',
        'qty_schedules',
        'qty_screenings',
        'balance',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
