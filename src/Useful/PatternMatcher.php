<?php

namespace Widmogrod\Useful;

interface PatternMatcher
{
    /**
     * should be used with conjuction
     * @param callable $fn
     * @return mixed
     */
    public function patternMatched(callable $fn);
}
