<?php

namespace test\Monad;

use Eris\TestTrait;
use Widmogrod\FantasyLand\Applicative;
use Widmogrod\FantasyLand\Functor;
use Widmogrod\FantasyLand\Monoid;
use Widmogrod\Functional as f;
use function Widmogrod\Functional\fromNil;
use const Widmogrod\Functional\fromValue;
use Widmogrod\Helpful\ApplicativeLaws;
use Widmogrod\Helpful\FunctorLaws;
use Widmogrod\Helpful\MonadLaws;
use Widmogrod\Helpful\MonoidLaws;
use Widmogrod\Primitive\Listt;
use function Eris\Generator\choose;
use function Eris\Generator\vector;

class ListtTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    /**
     * @dataProvider provideData
     */
    public function test_if_list_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            f\curryN(1, fromValue),
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        $addOne = function ($x) {
            return f\fromIterable([$x + 1]);
        };
        $addTwo = function ($x) {
            return f\fromIterable([$x + 2]);
        };

        return [
            'Listt' => [
                '$f' => $addOne,
                '$g' => $addTwo,
                '$x' => 10,
            ],
        ];
    }

    /**
     * @dataProvider provideApplicativeTestData
     */
    public function test_it_should_obey_applicative_laws(
        $pure,
        Applicative $u,
        Applicative $v,
        Applicative $w,
        callable $f,
        $x
    ) {
        ApplicativeLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            f\curryN(1, $pure),
            $u,
            $v,
            $w,
            $f,
            $x
        );
    }

    public function provideApplicativeTestData()
    {
        return [
            'Listt' => [
                '$pure' => fromValue,
                '$u'    => f\fromIterable([function () {
                    return 1;
                }]),
                '$v' => f\fromIterable([function () {
                    return 5;
                }]),
                '$w' => f\fromIterable([function () {
                    return 7;
                }]),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
        ];
    }

    /**
     * @dataProvider provideFunctorTestData
     */
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor $x
    ) {
        FunctorLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $f,
            $g,
            $x
        );
    }

    public function provideFunctorTestData()
    {
        return [
            'Listt' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => f\fromIterable([1, 2, 3]),
            ],
        ];
    }

    /**
     * @dataProvider provideRandomizedData
     */
    public function test_it_should_obey_monoid_laws(Monoid $x, Monoid $y, Monoid $z)
    {
        MonoidLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $x,
            $y,
            $z
        );
    }

    private function randomize()
    {
        return f\fromIterable(array_keys(array_fill(0, random_int(20, 100), null)));
    }

    public function provideRandomizedData()
    {
        return array_map(function () {
            return [
                $this->randomize(),
                $this->randomize(),
                $this->randomize(),
            ];
        }, array_fill(0, 50, null));
    }

    public function test_head_extracts_first_element()
    {
        $this->forAll(
            vector(10, choose(1, 1000))
        )(
            function ($sequence) {
                $list = f\fromIterable($sequence);
                $current = current($sequence);

                $this->assertSame($current, $list->head());
                $this->assertSame($current, $list->head());
            }
        );
    }

    public function test_tail_with_single_element_Listt()
    {
        $this->forAll(
            vector(1, choose(1, 1000))
        )(
            function ($sequence) {
                $this->assertTrue(f\fromIterable($sequence)->tail()->equals(fromNil()));
            }
        );
    }

    public function test_tail_with_multiple_element_Listt()
    {
        $this->forAll(
            vector(10, choose(1, 1000))
        )(
            function ($sequence) {
                $list = f\fromIterable($sequence);
                array_shift($sequence);

                $this->assertEquals($sequence, $list->tail()->extract());
            }
        );
    }
}
