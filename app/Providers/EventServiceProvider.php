<?php

namespace App\Providers;

use App\Entities\Patient;
use App\Entities\Procedure;
use App\Entities\SalesOrder;
use App\Entities\Schedule;
use App\Entities\Screening;
use App\Entities\User;
use App\Models\UserGateway;
use App\Services\DashboardService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        $this->afterCreatedModels();
    }

    /**
     * afterCreatedModels
     */
    private function afterCreatedModels()
    {


        Screening::created(function () {
            Cache::store('redis')->tags('screenings')->flush();
        });
        Screening::updated(function () {
            Cache::store('redis')->tags('screenings')->flush();
        });
        Screening::deleted(function () {
            Cache::store('redis')->tags('screenings')->flush();
        });

        Schedule::created(function () {
            Cache::store('redis')->tags('schedules')->flush();
        });
        Schedule::updated(function () {
            Cache::store('redis')->tags('schedules')->flush();
        });
        Schedule::deleted(function () {
            Cache::store('redis')->tags('schedules')->flush();
        });

        Procedure::created(function () {

            Cache::store('redis')->tags('procedures')->flush();
        });
        Procedure::updated(function () {
            Cache::store('redis')->tags('procedures')->flush();
        });
        Procedure::deleted(function () {
            Cache::store('redis')->tags('procedures')->flush();
        });

        Patient::created(function () {
            Cache::store('redis')->tags('patients')->flush();
        });
        Patient::updated(function () {
            Cache::store('redis')->tags('patients')->flush();
        });
        Patient::deleted(function () {
            Cache::store('redis')->tags('patients')->flush();
        });
        SalesOrder::updated(function () {
            Cache::store('redis')->tags('schedules')->flush();
        });

    }
    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }

}
