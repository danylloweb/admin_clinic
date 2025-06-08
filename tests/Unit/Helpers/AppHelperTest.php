<?php

namespace Tests\Unit\Helpers;

use App\AppHelper;
use PHPUnit\Framework\TestCase;

class AppHelperTest extends TestCase
{
    /**
     * Test formatPrice function
     */
    public function testFormatPrice()
    {
        $this->assertEquals('123', AppHelper::formatPrice('123.'));
        $this->assertEquals('1234500', AppHelper::formatPrice('12345'));
    }

    /**
     * Test insertSpace function
     */
    public function testInsertSpace()
    {
        $this->assertEquals('    ', AppHelper::insertSpace(4));
        $this->assertEquals('', AppHelper::insertSpace(0));
    }

    /**
     * Test insertChar function
     */
    public function testInsertChar()
    {
        $this->assertEquals('00test', AppHelper::insertChar('test', 6, '0'));
        $this->assertEquals('test', AppHelper::insertChar('test', 4, '0'));
        $this->assertEquals('te', AppHelper::insertChar('te', 2, '0'));
    }

    /**
     * Test getValues function
     */
    public function testGetValues()
    {
        $this->assertEquals('test1test2', AppHelper::getValues(['test1', 'test2']));
        $this->assertEquals('', AppHelper::getValues([]));
    }
} 