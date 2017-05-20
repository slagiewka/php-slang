<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use Closure;
use PhpSlang\Exception\NoNestedElementException;
use PhpSlang\Match\When\AbstractWhen;
use PhpSlang\Option\Option;

/**
 * @author Szymon A. Łągiewka <phpslang@lagiewka.pl>
 */
interface TryInterface
{
    /**
     * Perform mapping on Success data or return chained Failure
     *
     * @param Closure $closure
     *
     * @return TryInterface
     */
    public function map(Closure $closure): TryInterface;

    /**
     * Returns true on Success, false otherwise.
     *
     * @return bool
     */
    public function isSuccess(): bool;

    /**
     * Returns true on Failure, false otherwise.
     *
     * @return bool
     */
    public function isFailure(): bool;

    /**
     * Get the value of source operation on Success
     * or re-throw caught \Throwable on Failure.
     *
     * @return mixed
     *
     * @throws \Throwable
     */
    public function get();

    /**
     * Returns None on on Failure or Some on Success.
     *
     * @return Option
     */
    public function toOption(): Option;

    /**
     * Returns Success if condition is satisfied, false otherwise
     *
     * @param Closure $closure
     *
     * @return TryInterface
     */
    public function filter(Closure $closure): TryInterface;

    /**
     * Unpack result from Success or return default on Failure
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOrElse($default);

    /**
     * Return Success itself or call Closure when initial result is Failure.
     *
     * Note: Closure should return TryInterface instance at all times.
     *
     * @param Closure $closure
     *
     * @return TryInterface
     */
    public function orElse(Closure $closure): TryInterface;

    /**
     * Converts a nested TryInterfaces (one level) into a single TryInterface.
     *
     * Example: TryInterface<TryInterface<T>> => TryInterface<T>
     *
     * @return TryInterface
     */
    public function flatten(): TryInterface;

    /**
     * Applies given Closure to result on Success or returns void on Failure.
     *
     * @param Closure $closure
     */
    public function each(Closure $closure);

    /**
     * Returns result of Closure applied to Success result or Failure otherwise.
     *
     * Note: Closure should return TryInterface instance at all times.
     *
     * @param Closure $closure
     *
     * @return TryInterface
     */
    public function flatMap(Closure $closure): TryInterface;

    /**
     * Returns self on Success or matches Failure and returns Success with defined fallback value.
     *
     * @param AbstractWhen[] ...$cases
     *
     * @return TryInterface
     */
    public function recover(AbstractWhen ...$cases): TryInterface;

    /**
     * Return self on Success or matches Failure and returns defined fallback Failure.
     *
     * @param AbstractWhen[] ...$cases
     *
     * @return TryInterface
     */
    public function recoverWith(AbstractWhen ...$cases): TryInterface;
}
