<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PasswordResetRepository::class, \App\Repositories\PasswordResetRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserTypeRepository::class, \App\Repositories\UserTypeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentOrderRepository::class, \App\Repositories\PaymentOrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentRepository::class, \App\Repositories\PaymentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentMethodRepository::class, \App\Repositories\PaymentMethodRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentGatewayRepository::class, \App\Repositories\PaymentGatewayRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PaymentStatusRepository::class, \App\Repositories\PaymentStatusRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProcedureRepository::class, \App\Repositories\ProcedureRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ProcedureTypeRepository::class, \App\Repositories\ProcedureTypeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PatientRepository::class, \App\Repositories\PatientRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ScheduleRepository::class, \App\Repositories\ScheduleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ScreeningRepository::class, \App\Repositories\ScreeningRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ClinicalHistoryRepository::class, \App\Repositories\ClinicalHistoryRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SalesOrderRepository::class, \App\Repositories\SalesOrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\SalesOrderItemRepository::class, \App\Repositories\SalesOrderItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CampaignRepository::class, \App\Repositories\CampaignRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AdvertRepository::class, \App\Repositories\AdvertRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\LeadRepository::class, \App\Repositories\LeadRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FollowUpMessageRepository::class, \App\Repositories\FollowUpMessageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FollowUpScheduleRepository::class, \App\Repositories\FollowUpScheduleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FollowUpScheduleMessageRepository::class, \App\Repositories\FollowUpScheduleMessageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PatientMedicalRecordRepository::class, \App\Repositories\PatientMedicalRecordRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FacialEvaluationRepository::class, \App\Repositories\FacialEvaluationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BodyEvaluationRepository::class, \App\Repositories\BodyEvaluationRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AestheticProcedureEvolutionRepository::class, \App\Repositories\AestheticProcedureEvolutionRepositoryEloquent::class);
        //:end-bindings:
    }
}
