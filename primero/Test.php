<?php


use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            'user@example.com'
        );
    }
}
