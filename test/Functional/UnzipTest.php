<?php

declare(strict_types=1);

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\eql;
use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\repeat;
use function Widmogrod\Functional\take;
use function Widmogrod\Functional\unzip;

class UnzipTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

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

    public function test_it_should_work_on_infinite_lists()
    {
        $this->forAll(
            Generator\choose(1, 100),
            Generator\string(),
            Generator\string()
        )->then(function ($n, $x, $y) {
            [$xs, $ys] = unzip(repeat([$x, $y]));

            $this->assertEquals($n, length(filter(eql($x), take($n, $xs))));
            $this->assertEquals($n, length(filter(eql($y), take($n, $ys))));
        });
    }
}
