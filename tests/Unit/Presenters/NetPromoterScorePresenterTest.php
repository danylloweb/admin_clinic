<?php

namespace Tests\Unit\Presenters;

use Tests\TestCase;
use App\Presenters\NetPromoterScorePresenter;
use App\Transformers\NetPromoterScoreTransformer;
use League\Fractal\TransformerAbstract;
use Prettus\Repository\Presenter\FractalPresenter;

class NetPromoterScorePresenterTest extends TestCase
{
    protected $presenter;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->presenter = new NetPromoterScorePresenter();
    }

    public function test_presenter_can_be_instantiated(): void
    {
        $this->assertInstanceOf(NetPromoterScorePresenter::class, $this->presenter);
        $this->assertInstanceOf(FractalPresenter::class, $this->presenter);
    }

    public function test_get_transformer_returns_correct_transformer(): void
    {
        $transformer = $this->presenter->getTransformer();
        
        $this->assertInstanceOf(TransformerAbstract::class, $transformer);
        $this->assertInstanceOf(NetPromoterScoreTransformer::class, $transformer);
    }

    public function test_get_transformer_returns_new_instance(): void
    {
        $transformer1 = $this->presenter->getTransformer();
        $transformer2 = $this->presenter->getTransformer();
        
        $this->assertNotSame($transformer1, $transformer2, 'getTransformer should return a new instance each time');
    }
}