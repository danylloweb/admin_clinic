<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ProfessionalScoresRepositoryEloquent;
use App\Entities\ProfessionalScores;
use App\Validators\ProfessionalScoresValidator;
use App\Presenters\ProfessionalScoresPresenter;
use Mockery;
use Illuminate\Container\Container;

class ProfessionalScoresRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ProfessionalScoresRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(ProfessionalScores::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ProfessionalScoresValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(ProfessionalScoresPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}