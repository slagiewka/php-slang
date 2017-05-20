<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use Exception;
use PHPUnit_Framework_TestCase;

class TryCallTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testSuccessfulTry()
    {
        $data = [1, 2, 3];
        $expression = function () use ($data) {
            return $data;
        };

        $result = TryMonad::tryM($expression);

        $this->assertSuccessInstance($result);
        $this->assertSuccessValue($result, $data);
    }

    /**
     * @test
     */
    public function testFailedTry()
    {
        $exceptionMessage = 'Custom exception message.';
        $exception = new Exception($exceptionMessage);
        $result = TryMonad::tryM(function () use ($exception) {
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

    private function assertSuccessInstance(TryInterface $result)
    {
        $this->assertInstanceOf(
            Success::class,
            $result,
            sprintf('Result of TryCatch attempt should be an instance of %s for successful method apply.', Success::class)
        );
    }

    private function assertSuccessValue(TryInterface $result, array $expected)
    {
        $this->assertSame(
            $expected,
            $result->get(),
            'Values returned from Success::get should be equal to source function result data.'
        );
    }
}
