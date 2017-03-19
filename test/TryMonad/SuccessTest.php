<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use ArrayObject;
use PhpSlang\Exception\NoMatchFoundException;
use PhpSlang\Option\Some;
use PHPUnit_Framework_TestCase;

class SuccessTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     *
     * @dataProvider sampleSuccessData
     *
     * @param mixed $data
     */
    public function testCreateSuccess($data)
    {
        $success = $this->getSuccessObject($data);

        $this->assertSame(
            $data,
            $success->get(),
            'Success data should be the same as given class constructor argument.'
        );
    }

    /**
     * @return mixed[]
     */
    public function sampleSuccessData(): array
    {
        return [
            'int data'    => [1],
            'float data'  => [1.0],
            'string data' => ['string data'],
            'object data' => [new ArrayObject()],
        ];
    }

    /**
     * @test
     *
     * @dataProvider mapSuccessData
     *
     * @param int|float $initialData
     */
    public function testMapSuccess($initialData)
    {
        $success = $this->getSuccessObject($initialData);

        $mapFunction = function ($data) {
            return $data + 2;
        };

        $mappedSuccess = $success->map($mapFunction);

        $this->assertInstanceOf(Success::class, $mappedSuccess, 'Mapping of Success should result in another Success.');
        $this->assertNotSame($success, $mappedSuccess, 'Original and mapped Success\' should not be the same instance.');

        $this->assertEquals(
            $mapFunction($initialData),
            $mappedSuccess->get(),
            'Mapped data should be equal to result of mapping function.'
        );
    }

    /**
     * @return int[]|float[]
     */
    public function mapSuccessData(): array
    {
        return [
            'int data'   => [1],
            'float data' => [1.67],
        ];
    }

    /**
     * @test
     */
    public function testIsSuccessOrFailureResult()
    {
        $success = $this->getSuccessObject([]);

        $this->assertTrue($success->isSuccess(), 'Success object should always return true for isSuccess call.');
        $this->assertFalse($success->isFailure(), 'Success object should always return false for isFailure call.');
    }

    /**
     * @test
     */
    public function testOptionConversion()
    {
        $data = new ArrayObject();
        $success = $this->getSuccessObject($data);

        $result = $success->toOption();

        $this->assertInstanceOf(Some::class, $result, 'toOption on Success should return Some.');
        $this->assertSame($data, $result->get(), 'toOption result should hold the exact same data as the source Success.');
    }

    /**
     * @test
     */
    public function testFilterSuccessData()
    {
        $data = new ArrayObject([]);
        $success = $this->getSuccessObject($data);

        $filtered = $success->filter(function (ArrayObject $data) {
            return 0 === $data->count();
        });

        $this->assertInstanceOf(Success::class, $filtered, 'Successful filter result should be a Success instance.');
        $this->assertSame($success, $filtered, 'Successful filter result should be the same Success instance.');
    }

    /**
     * @test
     */
    public function testUnmatchedFilterSuccessData()
    {
        $data = new ArrayObject([]);

        $success = $this->getSuccessObject($data);

        /** @var Failure $filtered */
        $filtered = $success->filter(function (ArrayObject $data) {
            return 0 !== $data->count();
        });

        $this->assertInstanceOf(Failure::class, $filtered, 'Unsuccessful filter result should be a Failure instance.');
        $this->assertInstanceOf(
            NoMatchFoundException::class,
            $filtered->getThrowable(),
            sprintf('Failure exception should be a %s instance', NoMatchFoundException::class)
        );
    }

    /**
     * @param mixed $data
     *
     * @return Success
     */
    private function getSuccessObject($data): Success
    {
        return new Success($data);
    }
}
