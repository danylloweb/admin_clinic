<?php

namespace Tests\Unit\Presenters;

use Tests\TestCase;
use App\Presenters\OrderPresenter;
use App\Transformers\OrderTransformer;
use League\Fractal\TransformerAbstract;
use Prettus\Repository\Presenter\FractalPresenter;

class OrderPresenterTest extends TestCase
{
    protected $presenter;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->presenter = new OrderPresenter();
    }

    public function test_presenter_can_be_instantiated(): void
    {
        $this->assertInstanceOf(OrderPresenter::class, $this->presenter);
        $this->assertInstanceOf(FractalPresenter::class, $this->presenter);
    }

    public function test_get_transformer_returns_correct_transformer(): void
    {
        $transformer = $this->presenter->getTransformer();
        
        $this->assertInstanceOf(TransformerAbstract::class, $transformer);
        $this->assertInstanceOf(OrderTransformer::class, $transformer);
    }

    public function test_get_transformer_returns_new_instance(): void
    {
        $transformer1 = $this->presenter->getTransformer();
        $transformer2 = $this->presenter->getTransformer();
        
        $this->assertNotSame($transformer1, $transformer2, 'getTransformer should return a new instance each time');
    }
}