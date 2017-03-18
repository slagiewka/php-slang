<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use Closure;
use Throwable;

/**
 * Companion class for Try monad. Name in progress...
 */
class TryCatch
{
    /**
     * Tries executing given Closure and returns Success on success
     * or Failure when an exceptions is thrown inside Closure.
     *
     * @param Closure $closure
     *
     * @return TryInterface
     */
    public static function tryCatch(Closure $closure): TryInterface
    {
        try {
            return new Success($closure());
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
