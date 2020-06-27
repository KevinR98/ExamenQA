<?php


use PHPUnit\Framework\TestCase;

class PruebaTest extends TestCase
{
    public function testCanBeUsedAsString()
    {
        require_once ('src/Prueba.php');
        $objeto = new Prueba();
        $this->assertEquals(
            100,
            $objeto->funcion()
        );
    }
}
