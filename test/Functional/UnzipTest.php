<?php

namespace test\Functional;

use function Widmogrod\Functional\fromNil;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\unzip;

class UnzipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_zipped_list(
        Listt $a,
        array $expected
    ) {
        [$a, $b] = unzip($a);
        [$ea, $eb] = $expected;


        $this->assertTrue($a->equals($ea));
        $this->assertTrue($b->equals($eb));
    }

    public function provideData()
    {
        return [
            'unzipping of empty lists should be an tuple of empty lists' => [
                '$a' => fromNil(),
                '$expected' => [fromNil(), fromNil()],
            ],
            'unzipping of lists should be an tuple of lists' => [
                '$a' => fromIterable([
                    [1, 'a'],
                    [2, 'b'],
                    [3, 'c'],
                ]),
                '$expected' => [fromIterable([1, 2, 3]), fromIterable(['a', 'b', 'c'])],
            ],
        ];
    }
}
