<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\ProfessionalRatingRepositoryEloquent;
use App\Entities\ProfessionalRating;
use App\Validators\ProfessionalRatingValidator;
use App\Presenters\ProfessionalRatingPresenter;
use Mockery;
use Illuminate\Container\Container;

class ProfessionalRatingRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new ProfessionalRatingRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(ProfessionalRating::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(ProfessionalRatingValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(ProfessionalRatingPresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}