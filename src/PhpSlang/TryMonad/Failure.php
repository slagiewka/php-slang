<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use Closure;
use PhpSlang\Match\Match;
use PhpSlang\Match\When\AbstractWhen;
use PhpSlang\Option\None;
use PhpSlang\Option\Option;
use Throwable;

/**
 * Class Failure
 *
 * @package PhpSlang\TryMonad
 */
final class Failure implements TryInterface
{
    /** @var Throwable */
    private $throwable;

    /**
     * @param Throwable $throwable
     */
    public function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    /**
     * @return Throwable
     */
    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccess(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isFailure(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        throw $this->throwable;
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(): Option
    {
        return new None();
    }

    /**
     * {@inheritdoc}
     */
    public function map(Closure $closure): TryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Closure $closure): TryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrElse($default)
    {
        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function orElse(Closure $closure): TryInterface
    {
        try {
            return $closure();
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function flatten(): TryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function each(Closure $closure)
    {
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function flatMap(Closure $closure): TryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function recover(AbstractWhen ...$cases): TryInterface
    {
        return new Success(Match::val($this)->of($cases));
    }

    /**
     * {@inheritdoc}
     */
    public function recoverWith(AbstractWhen ...$cases): TryInterface
    {
        return Match::val($this)->of($cases);
    }
}
