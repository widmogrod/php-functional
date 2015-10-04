<?php
namespace test\Monad;

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
}
