<?php

use PHPUnit\Framework\TestCase;

class AnnotationTest extends TestCase
{
    protected $value;

    protected function setUp(): void
    {
        $this->value = 0; // قبل از هر تست مقداردهی مجدد
    }

    public function testCorrectValue()
    {
        $this->value++;
        $this->assertEquals(1, $this->value);
    }

    public function testCorrectValue2()
    {
        $this->value++;
        $this->assertEquals(1, $this->value);
    }
}
