<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Repositories\NetPromoterScoreRepositoryEloquent;
use App\Entities\NetPromoterScore;
use App\Validators\NetPromoterScoreValidator;
use App\Presenters\NetPromoterScorePresenter;
use Mockery;
use Illuminate\Container\Container;

class NetPromoterScoreRepositoryEloquentTest extends TestCase
{
    protected $repository;
    
    protected function setUp(): void
    {
        parent::setUp();
        $app = Container::getInstance();
        $this->repository = new NetPromoterScoreRepositoryEloquent($app);
    }

    public function test_model()
    {
        $result = $this->repository->model();
        $this->assertEquals(NetPromoterScore::class, $result);
    }

    public function test_validator()
    {
        $result = $this->repository->validator();
        $this->assertEquals(NetPromoterScoreValidator::class, $result);
    }

    public function test_presenter()
    {
        $result = $this->repository->presenter();
        $this->assertEquals(NetPromoterScorePresenter::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}