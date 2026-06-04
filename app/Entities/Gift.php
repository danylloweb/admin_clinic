<?php

namespace App\Entities;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Class Gift.
 *
 * @package namespace App\Entities;
 */
class Gift extends Model
{
    protected $connection = 'mongodb';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'phoneFormatted',
        'partnerName',
        'partnerPhone',
        'partnerPhoneFormatted',
        'procedureId',
        'procedureName',
        'procedureValue',
        'source',
        'page',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'procedureValue' => 'decimal:2',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'source' => 'gift-page',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}

