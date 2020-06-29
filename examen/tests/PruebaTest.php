<?php

use PHPUnit\Framework\TestCase;
include "../config.php";

class PruebaTest extends TestCase
{
    public function testSameInt()
    {
        require_once(ROOT.'/src/Prueba.php');
        $objeto = new Prueba();
        $this->assertEquals(
            100,
            $objeto->funcion()
        );
    }
}
