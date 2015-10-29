<?php
namespace test\Monad;

use FantasyLand\ApplicativeInterface;
use Helpful\ApplicativeLaws;
use Monad\Collection;
use Helpful\MonadLaws;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_collection_monad_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            Collection::of,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        $addOne = function ($x) {
            return Collection::of($x + 1);
        };
        $addTwo = function ($x) {
            return Collection::of($x + 2);
        };

        return [
            'Collection' => [
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
            'Collection' => [
                '$pure' => Collection::of,
                '$u' => Collection::of(function ($x) {
                    return 1 + $x;
                }),
                '$v' => Collection::of(function ($x) {
                    return 5 + $x;
                }),
                '$w' => Collection::of(function ($x) {
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
