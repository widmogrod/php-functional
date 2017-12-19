<?php

namespace test\Monad;

use Eris\TestTrait;
use Widmogrod\FantasyLand\Applicative;
use Widmogrod\FantasyLand\Functor;
use Widmogrod\FantasyLand\Monoid;
use Widmogrod\Functional as f;
use const Widmogrod\Functional\fromValue;
use Widmogrod\Helpful\ApplicativeLaws;
use Widmogrod\Helpful\FunctorLaws;
use Widmogrod\Helpful\MonadLaws;
use Widmogrod\Helpful\MonoidLaws;
use Widmogrod\Primitive\Listt;
use function Eris\Generator\choose;
use function Eris\Generator\vector;

class ListtTest extends \PHPUnit_Framework_TestCase
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
            return Listt::of([$x + 1]);
        };
        $addTwo = function ($x) {
            return Listt::of([$x + 2]);
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
                '$u'    => Listt::of([function () {
                    return 1;
                }]),
                '$v' => Listt::of([function () {
                    return 5;
                }]),
                '$w' => Listt::of([function () {
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
                '$x' => Listt::of([1, 2, 3]),
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
        return Listt::of(array_keys(array_fill(0, random_int(20, 100), null)));
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

    public function test_head_on_empty_list_is_undefined()
    {
        $this->setExpectedException(\BadMethodCallException::class, 'head of empty Listt');

        Listt::mempty()->head();
    }

    public function test_head_extracts_first_element()
    {
        $this->forAll(
            vector(10, choose(1, 1000))
        )(
            function ($sequence) {
                $list = Listt::of($sequence);
                $current = current($sequence);

                $this->assertSame($current, $list->head());
                $this->assertSame($current, $list->head());
            }
        );
    }

    public function test_tail_on_empty_list()
    {
        $this->setExpectedException(\BadMethodCallException::class, 'tail of empty Listt');

        Listt::mempty()->tail();
    }

    public function test_tail_with_single_element_Listt()
    {
        $this->forAll(
            vector(1, choose(1, 1000))
        )(
            function ($sequence) {
                $this->assertTrue(Listt::of($sequence)->tail()->equals(Listt::mempty()));
            }
        );
    }

    public function test_tail_with_multiple_element_Listt()
    {
        $this->forAll(
            vector(10, choose(1, 1000))
        )(
            function ($sequence) {
                $list = Listt::of($sequence);
                array_shift($sequence);

                $this->assertEquals($sequence, $list->tail()->extract());
            }
        );
    }
}
