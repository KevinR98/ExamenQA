<?php


use PHPUnit\Framework\TestCase;

class GetFieldsValueTest extends TestCase
{
    public function testPrimeraPrueba()
    {
        require_once ('src/Prueba.php');
        $objeto = new Prueba();
        $this->assertEquals(
            100,
            $objeto->funcion()
        );
    }

    public function testSegundaPrueba()
    {
        require_once ('src/Prueba.php');
        $objeto = new Prueba();
        $this->assertEquals(
            100,
            $objeto->funcion()
        );
    }
}
