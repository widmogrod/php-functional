<?php
namespace test\Monad;

use Monad\Identity;
use Helpful\MonadLaws;

class IdentityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_identity_monad_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            Identity::of,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        $addOne = function ($x) {
            return Identity::of($x + 1);
        };
        $addTwo = function ($x) {
            return Identity::of($x + 2);
        };

        return [
            'state 0' => [
                '$f' => $addOne,
                '$g' => $addTwo,
                '$x' => 10,
            ],
        ];
    }
}
