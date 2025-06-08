<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ProfessionalRepositoryEloquent;
use App\Entities\Professional;
use App\Validators\ProfessionalValidator;
use App\Presenters\ProfessionalPresenter;
use Mockery;
use Illuminate\Container\Container;
use Illuminate\Support\Facades\Cache;

class ProfessionalRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ProfessionalRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(Professional::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ProfessionalValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(ProfessionalPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}