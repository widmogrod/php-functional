<?php

namespace test\Functional;

use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\head;
use function Widmogrod\Functional\repeat;
use function Widmogrod\Functional\tail;

class RepeatTest extends \PHPUnit_Framework_TestCase
{
    public function test_it($value = 'ab')
    {
        $result = repeat($value);
        $this->assertInstanceOf(Listt::class, $result);

        $this->assertSame($value, head($result));
        $this->assertSame($value, head(tail($result)));

        $it = $result->getIterator();
        $this->assertInstanceOf(\Generator::class, $it);
        $this->assertSame($value, $it->current());
        $it->next();
        $this->assertSame($value, $it->current());
    }
}
