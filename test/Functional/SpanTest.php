<?php

declare(strict_types=1);

namespace test\Functional;

use Eris\TestTrait;
use function Widmogrod\Functional\constt;
use function Widmogrod\Functional\fromNil;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\span;

class SpanTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_spanned_list(
        callable $predicate,
        Listt $xs,
        array $expected
    ) {
        [$left, $right] = span($predicate, $xs);
        [$eleft, $eright] = $expected;

        $l = print_r($left->extract(), true);
        $r = print_r($right->extract(), true);
        $el = print_r($eleft->extract(), true);
        $er = print_r($eright->extract(), true);

        $this->assertTrue(
            $left->equals($eleft),
            "left $l != $el"
        );
        $this->assertTrue(
            $right->equals($eright),
            "right $r != $er"
        );
    }

    public function provideData()
    {
        $lessThanTwo = function ($x) {
            return $x < 2;
        };

        return [
            'span on empty list should be tuple of empty lists' => [
                '$predicate' => $lessThanTwo,
                '$xs' => fromNil(),
                '$expected' => [fromNil(), fromNil()],
            ],
            'span on finite list should be tuple of lists' => [
                '$predicate' => $lessThanTwo,
                '$xs' => fromIterable([0, 1, 2, 3, 4]),
                '$expected' => [fromIterable([0, 1]), fromIterable([2, 3, 4])],
            ],
            'span on finite list when predicate is always false should be:' => [
                '$predicate' => constt(false),
                '$xs' => fromIterable([0, 1, 2, 3, 4]),
                '$expected' => [fromNil(), fromIterable([0, 1, 2, 3, 4])],
            ],
            'span on finite list when predicate is always true should be:' => [
                '$predicate' => constt(true),
                '$xs' => fromIterable([0, 1, 2, 3, 4]),
                '$expected' => [fromIterable([0, 1, 2, 3, 4]), fromNil()],
            ],
        ];
    }
//
//    public function test_it_should_work_on_infinite_lists()
//    {
//        $this->forAll(
//            Generator\choose(1, 100),
//            Generator\string(),
//            Generator\string()
//        )->then(function ($n, $a, $b) {
//            $list = take($n, zip(repeat($a), repeat($b)));
//
//            $this->assertEquals($n, length(filter(eql([$a, $b]), $list)));
//        });
//    }
}
