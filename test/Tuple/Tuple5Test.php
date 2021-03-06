<?php

namespace PhpSlang\Tuple;

use PHPUnit_Framework_TestCase;

class Tuple5Test extends PHPUnit_Framework_TestCase
{
    public function testConstruction()
    {
        $this->assertEquals(
            ['a', 'b', 'c', 'd', 'e'],
            (new Tuple5('a', 'b', 'c', 'd', 'e'))->toArray());
        $this->assertEquals(5, (new Tuple5('a', 'b', 'c', 'd', 'e'))->size());
    }

    public function testGetters()
    {
        $example = new Tuple5('a', 'b', 'c', 'd', 'e');
        $this->assertEquals('a', $example->_1());
        $this->assertEquals('b', $example->_2());
        $this->assertEquals('c', $example->_3());
        $this->assertEquals('d', $example->_4());
        $this->assertEquals('e', $example->_5());
    }
}