<?php

namespace PhpSlang\Tuple\Get;

use PhpSlang\Collection\Generic\Component\CollectionWithContent;

trait Get02
{
    use CollectionWithContent;

    public function _2()
    {
        return $this->content[1];
    }
}