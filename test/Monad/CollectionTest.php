<?php
namespace test\Monad;

use FantasyLand\Applicative;
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
        Applicative $u,
        Applicative $v,
        Applicative $w,
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
                '$u' => Collection::of(function () {
                    return 1;
                }),
                '$v' => Collection::of(function () {
                    return 5;
                }),
                '$w' => Collection::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
        ];
    }
}
