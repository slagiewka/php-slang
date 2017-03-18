<?php
declare(strict_types = 1);
namespace PhpSlang\TryMonad;

use Closure;
use PhpSlang\Exception\NoMatchFoundException;
use PhpSlang\Match\When\AbstractWhen;
use PhpSlang\Option\Option;
use PhpSlang\Option\Some;
use Throwable;

final class Success implements TryInterface
{
    /** @var mixed */
    private $result;

    /**
     * @param mixed $result
     */
    public function __construct($result)
    {
        $this->result = $result;
    }

    /**
     * {@inheritdoc}
     */
    public function map(Closure $closure): TryInterface
    {
        return new Success($closure($this->result));
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccess(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isFailure(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function toOption(): Option
    {
        return new Some($this->result);
    }

    /**
     * {@inheritdoc}
     */
    public function filter(Closure $closure): TryInterface
    {
        return $closure($this->result)
            ? new Success($this->result)
            : new Failure(new NoMatchFoundException(sprintf('No match found for %s', $this->result)))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrElse($default)
    {
        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function orElse(Closure $closure): TryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function flatten(): TryInterface
    {
        return $this->flatMap(function ($data) {
            return $data;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function each(Closure $closure)
    {
        $closure($this->result);
    }

    /**
     * {@inheritdoc}
     */
    public function flatMap(Closure $closure): TryInterface
    {
        try {
            return $closure($this->result);
        } catch (Throwable $throwable) {
            return new Failure($throwable);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function recover(AbstractWhen ...$cases): TryInterface
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function recoverWith(AbstractWhen ...$cases): TryInterface
    {
        return $this;
    }
}
