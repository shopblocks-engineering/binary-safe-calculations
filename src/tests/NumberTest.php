<?php declare(strict_types=1);

namespace ShopblocksEngineering\BinarySafeCalculations\Tests;

use PHPUnit\Framework\TestCase;
use ShopblocksEngineering\BinarySafeCalculations\DivisionByZeroException;
use ShopblocksEngineering\BinarySafeCalculations\Number;

class NumberTest extends TestCase
{
    public function testMultiply()
    {
        $number = new Number("64.99");
        $multiplier = new Number("100");
        $multipliedNumber = $number->multiply($multiplier);
        $this->assertInstanceOf(Number::class, $multipliedNumber);
        $this->assertEquals("6499", $multipliedNumber->getNumber());
        $this->assertEquals(6498, (int) (64.99 * 100));
    }

    public function testAdd()
    {
        $number = new Number("0.1");
        $numberToAdd = new Number("0.7");
        $addedNumber = $number->add($numberToAdd);
        $this->assertInstanceOf(Number::class, $addedNumber);
        $this->assertEquals(8, (int) $addedNumber->multiply(new Number("10"))->getNumber());
        $this->assertEquals(7, (int) ((0.1 + 0.7) * 10));
    }

    public function testDivide()
    {
        $number = new Number("-500");
        $numberToDivide = new Number("110");
        $dividedNumber = $number->divide($numberToDivide);
        $this->assertInstanceOf(Number::class, $dividedNumber);
        $this->assertEquals('-4.5454545454545',  $dividedNumber->getNumber());
        $this->expectException(DivisionByZeroException::class);
        $number->divide(new Number("0"));
    }

    public function testSubtract()
    {
        $number = new Number("0.8");
        $numberToSubtract = new Number("0.1");
        $subtractedNumber = $number->subtract($numberToSubtract);
        $this->assertInstanceOf(Number::class, $subtractedNumber);
        $this->assertEquals(7, (int) $subtractedNumber->multiply(new Number("10"))->getNumber());
        $this->assertEquals(8, (int) ceil(((0.8 - 0.1)* 10)));
    }
}