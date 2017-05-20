<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use Closure;
use function PhpSlang\TryMonad\TryMonad\tryCall as baseTryCall;
use Throwable;

/**
 * Companion class for Try monad. Name in progress...
 *
 * @author Szymon A. Łągiewka <phpslang@lagiewka.pl>
 *
 */
class TryMonad
{
    /**
     * Tries executing given Closure and returns Success on success
     * or Failure when an exceptions is thrown inside Closure.
     *
     * @param Closure $expression
     *
     * @return TryInterface
     */
    public static function tryM(Closure $expression): TryInterface
    {
        try {
            return new Success($expression());
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }
}
