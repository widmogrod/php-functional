<?php
namespace test\Monad;

use FantasyLand\ApplicativeInterface;
use Helpful\ApplicativeLaws;
use Monad\IO;
use Helpful\MonadLaws;

class IOTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_identity_monad_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            function (IO $f, IO $g, $message) {
                $this->assertEquals(
                    $f->run(),
                    $g->run(),
                    $message
                );
            },
            function ($x) {
                return IO::of(function () use ($x) {
                    return $x;
                });
            },
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        $addOne = function ($x) {
            return IO::of(function () use ($x) {
                return $x + time() + 1;
            });
        };
        $addTwo = function ($x) {
            return IO::of(function () use ($x) {
                return $x + time() + 2;
            });
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
        $pure,
        ApplicativeInterface $u,
        ApplicativeInterface $v,
        ApplicativeInterface $w,
        callable $f,
        $x
    ) {
        ApplicativeLaws::test(
            function (IO $a, IO $b, $message) {
                $this->assertEquals(
                    $a->run(),
                    $b->run(),
                    $message
                );
            },
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
            'IO' => [
                '$pure' => IO\pure,
                '$u' => IO\pure(function ($x) {
                    return 1 + $x;
                }),
                '$v' => IO\pure(function ($x) {
                    return 5 + $x;
                }),
                '$w' => IO\pure(function ($x) {
                    return 7 + $x;
                }),
                '$f' => function ($x) {
                    return 400 + $x;
                },
                '$x' => 33
            ],
        ];
    }
}
