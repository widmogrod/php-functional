<?php
namespace test\Monad;

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
}
