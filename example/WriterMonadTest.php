<?php

namespace example;

use Widmogrod\Monad\Writer as W;
use Widmogrod\Functional as f;
use Widmogrod\Primitive\Stringg as S;


class WriterMonadTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_filter_with_logs()
    {
        $data = [1, 10, 15, 20, 25];

        $filter = function($i) {
            if ($i % 2 == 1) {
                return W::of(false, S::of("Reject odd number $i.\n"));
            } else if($i > 15) {
              return W::of(false, S::of("Reject $i because it is bigger than 15\n"));
            }

            return W::of(true);
        };

        list($result, $log) = f\filterM($filter, $data)->runWriter();

        $this->assertEquals(
            [10],
            $result
        );
        $this->assertEquals(
            'Reject odd number 1.
Reject odd number 15.
Reject 20 because it is bigger than 15
Reject odd number 25.
',
            $log->extract()
        );
    }
}