<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use ArrayObject;
use DateTime;
use Exception;
use PhpSlang\Exception\NoMatchFoundException;
use PhpSlang\Exception\NoNestedElementException;
use PhpSlang\Option\Some;
use function PhpSlang\Util\tryM;
use PHPUnit_Framework_TestCase;
use TypeError;

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
     * @test
     */
    public function testGetOrElse()
    {
        $data = new ArrayObject([]);
        $success = $this->getSuccessObject($data);

        $this->assertSame($data, $success->getOrElse(null), 'Get-or-else from success should be exactly the original data.');
    }

    /**
     * @test
     */
    public function testOrElse()
    {
        $data = new ArrayObject([]);
        $orResult = new DateTime();
        $orExpression = function () use ($orResult) {
            return $orResult;
        };
        $success = $this->getSuccessObject($data);

        $result = $success->orElse($orExpression);

        $this->assertSame($success, $result, 'Or-else from success should be exactly the source Success object.');
        $this->assertNotEquals($orResult, $result->get(), 'Or-else value from success should not be equal to or expression result.');
    }

    /**
     * @test
     */
    public function testFlatten()
    {
        $data = new ArrayObject([]);
        $nestedSuccess = $this->getSuccessObject($data);
        $success = $this->getSuccessObject($nestedSuccess);

        $flattened = $success->flatten();

        $this->assertSame($nestedSuccess, $flattened, 'Flattened value should equal the nested success from source Success.');
    }

    /**
     * @test
     */
    public function testFlattenWithNoNestedElement()
    {
        $success = $this->getSuccessObject(null);

        $this->expectException(NoNestedElementException::class);
        $success->flatten();
    }

    /**
     * @test
     */
    public function testFlatMap()
    {
        $deepArgument = 'Deep argument';
        $data = new ArrayObject([$deepArgument]);
        $success = $this->getSuccessObject($data);

        $result = $success
            ->flatMap(function (ArrayObject $data): TryInterface {
                return TryMonad::tryM(
                    function () use ($data): ArrayObject { return $data; }
                )->flatMap(function (ArrayObject $data): TryInterface {
                    return new Success($data[0]);
                });
            })
        ;

        $this->assertInstanceOf(Success::class, $result, 'Deepest flatMap callback should return a Success instance.');
        $this->assertNotSame($result, $success, 'Result Success of flatMapping should be different than source Success');
        $this->assertEquals($deepArgument, $result->get(), '');
    }

    /**
     * @test
     */
    public function testUnsuccessfulFlatMap()
    {
        $data = new ArrayObject([]);
        $success = $this->getSuccessObject($data);

        $result = $success->flatMap(function (ArrayObject $data): TryInterface {
            throw new Exception();
        });

        $this->assertInstanceOf(Failure::class, $result, 'Failed flatMap result should be a Failure instance.');
    }

    /**
     * @test
     */
    public function testTryWrongExpressionReturnTypeError()
    {
        $success = $this->getSuccessObject(null);

        $this->expectException(TypeError::class);
        $success->flatMap(function (): int { return 1; });
    }

    /**
     * @test
     */
    public function testEach()
    {
        $data = new ArrayObject([]);
        $success = $this->getSuccessObject($data);

        // Performing a mutating change to see the effect of each()
        $success->each(function (ArrayObject $data) {
            $data[] = 2;
        });

        $this->assertEquals(1, $success->get()->count(), 'Data size should be equal to 1 after each on Success.');
        $this->assertEquals(2, $success->get()[0], 'First and only element in the Success data should be equal to given integer.');
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
