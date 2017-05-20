<?php
declare(strict_types = 1);

namespace PhpSlang\Util;

class functionsTest extends \PHPUnit_Framework_TestCase
{
    public function testTry()
    {
        $result = tryM(function () {return [];});
    }
}
