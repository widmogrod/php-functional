<?php
namespace test\Monad;

use FantasyLand\ApplicativeInterface;
use Helpful\ApplicativeLaws;
use Monad\Maybe;
use Monad\Maybe\Just;
use Monad\Maybe\Nothing;
use Helpful\MonadLaws;

class MaybeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_maybe_monad_obeys_the_laws($return, $f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            $return,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        return [
            'Just' => [
                '$return' => Just::of,
                '$f' => function ($x) {
                    return Just::of($x + 1);
                },
                '$g' => function ($x) {
                    return Just::of($x + 2);
                },
                '$x' => 10,
            ],
            'Nothing' => [
                '$return' => Nothing::of,
                '$f' => function ($x) {
                    return Nothing::of($x + 1);
                },
                '$g' => function ($x) {
                    return Nothing::of($x + 2);
                },
                '$x' => 10,
            ],
        ];
    }


    /**
     * @dataProvider provideApplicativeTestData
     */
    public function test_it_should_obey_applicative_laws(
        $pure,
        ApplicativeInterface $u,
        ApplicativeInterface $v,
        ApplicativeInterface $w,
        callable $f,
        $x
    ) {
        ApplicativeLaws::test(
            [$this, 'assertEquals'],
            $pure,
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
            'Just' => [
                '$pure' => Maybe\pure,
                '$u' => Just::of(function ($x) {
                    return 1 + $x;
                }),
                '$v' => Just::of(function ($x) {
                    return 5 + $x;
                }),
                '$w' => Just::of(function ($x) {
                    return 7 + $x;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
            'Nothing' => [
                '$pure' => Maybe\pure,
                '$u' => Nothing::of(function ($x) {
                    return 1 + $x;
                }),
                '$v' => Nothing::of(function ($x) {
                    return 5 + $x;
                }),
                '$w' => Nothing::of(function ($x) {
                    return 7 + $x;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
        ];
    }
}
