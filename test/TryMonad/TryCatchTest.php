<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use Exception;
use PHPUnit_Framework_TestCase;

class TryCatchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testSuccessfulTry()
    {
        $data = [1, 2, 3];
        $result = TryCatch::tryCatch(function () use ($data) {
            return $data;
        });

        $this->assertInstanceOf(
            Success::class,
            $result,
            sprintf('Result of TryCatch attempt should be an instance of %s for successful method apply.', Success::class)
        );

        $this->assertSame(
            $data,
            $result->get(),
            'Values returned from Success::get should be equal to source function result data.'
        );
    }

    /**
     * @test
     */
    public function testFailedTry()
    {
        $exceptionMessage = 'Custom exception message.';
        $exception = new Exception($exceptionMessage);
        $result = TryCatch::tryCatch(function () use ($exception) {
            throw $exception;
        });

        $this->assertInstanceOf(
            Failure::class,
            $result,
            sprintf('Result of TryCatch attempt should be an instance of %s for unsuccessful method apply.', Failure::class)
        );

        $this->expectException(Exception::class);
        $this->expectExceptionMessage($exceptionMessage);
        $result->get();
    }
}
