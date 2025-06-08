<?php

namespace Tests\Enums;

use App\Enums\OrderItemStatusEnum;
use PHPUnit\Framework\TestCase;

class OrderItemStatusEnumTest extends TestCase
{
    public function testAllValuesAreStrings()
    {
        foreach (OrderItemStatusEnum::cases() as $item) {
            $this->assertIsString($item->translate());
        }
    }

    public function testTranslateFromInt()
    {
        foreach (OrderItemStatusEnum::cases() as $item) {
            $this->assertSame($item->translate(), OrderItemStatusEnum::translateFromInt($item->value));
        }
    }
} 