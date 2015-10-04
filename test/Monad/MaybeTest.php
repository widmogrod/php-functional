<?php
namespace test\Monad;

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
}
