<?php

namespace Tests\Enums;

use App\Enums\ScheduleStatusEnum;
use PHPUnit\Framework\TestCase;

class ScheduleStatusEnumTest extends TestCase
{
    public function testAllValuesAreStrings()
    {
        foreach (ScheduleStatusEnum::cases() as $item) {
            $this->assertIsString($item->translate());
        }
    }

    public function testTranslateFromInt()
    {
        foreach (ScheduleStatusEnum::cases() as $item) {
            $this->assertSame($item->translate(), ScheduleStatusEnum::translateFromInt($item->value));
        }
    }
} 