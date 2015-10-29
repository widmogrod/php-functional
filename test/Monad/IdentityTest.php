<?php
namespace test\Monad;

use FantasyLand\ApplicativeInterface;
use Helpful\ApplicativeLaws;
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
            'Identity' => [
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
        ApplicativeInterface $u,
        ApplicativeInterface $v,
        ApplicativeInterface $w,
        callable $f,
        $x
    ) {
        ApplicativeLaws::test(
            [$this, 'assertEquals'],
            Identity::of,
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
            'default' => [
                '$u' => Identity::of(function($x) { return 1 + $x; }),
                '$v' => Identity::of(function($x) { return 5 + $x; }),
                '$w' => Identity::of(function($x) { return 7 + $x; }),
                '$f' => function($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
        ];
    }
}
