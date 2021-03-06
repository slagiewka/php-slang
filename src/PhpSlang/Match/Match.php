<?php

namespace PhpSlang\Match;

use PhpSlang\Collection\ListCollection;
use PhpSlang\Exception\NoMatchFoundException;
use PhpSlang\Match\When\AbstractWhen;

class Match
{
    /**
     * @var
     */
    protected $matched;

    /**
     * Match constructor.
     *
     * @param $matched
     */
    public function __construct($matched)
    {
        $this->matched = $matched;
    }

    /**
     * @param $matched
     *
     * @return Match
     */
    public static function val($matched)
    {
        return new Match($matched);
    }

    /**
     * @param array ...$cases
     *
     * @return mixed
     */
    public function of(...$cases)
    {
        return (new ListCollection($cases))
            ->any(function (AbstractWhen $case) {
                return $case->matches($this->matched);
            })
            ->map(function (AbstractWhen $case) {
                return $case->getResult($this->matched);
            })
            ->getOrCall(function () {
                throw new NoMatchFoundException();
            });
    }
}
