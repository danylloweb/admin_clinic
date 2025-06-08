<?php

namespace Tests\Enums;

use App\Enums\OrderStatusEnum;
use PHPUnit\Framework\TestCase;

class OrderStatusEnumTest extends TestCase
{
    public function testAllValuesAreStrings()
    {
        foreach (OrderStatusEnum::cases() as $item) {
            $this->assertIsString($item->translate());
        }
    }

    public function testTranslateFromInt()
    {
        foreach (OrderStatusEnum::cases() as $item) {
            $this->assertSame($item->translate(), OrderStatusEnum::translateFromInt($item->value));
        }
    }
} 