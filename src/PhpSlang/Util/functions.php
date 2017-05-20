<?php
declare(strict_types = 1);

namespace PhpSlang\Util;

use Closure;
use PhpSlang\TryMonad\TryInterface;
use PhpSlang\TryMonad\TryMonad;

/**
 * @param Closure $expression
 *
 * @return TryInterface
 */
function tryM(Closure $expression): TryInterface
{
    return TryMonad::tryM($expression);
}
