<?php


use PHPUnit\Framework\TestCase;

class PruebaTest extends TestCase
{
    public function testCanBeUsedAsString()
    {
        $this->assertEquals(
            'user@example.com',
            'user@example.com'
        );
    }
}
