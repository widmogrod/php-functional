<?php
namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Collection
 */
class CollectionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith([1, 2, 3]);
        $this->shouldHaveType('Monad\Collection');
        $this->shouldHaveType('Monad\MonadInterface');
        $this->shouldHaveType('Monad\LiftInterface');
    }

    public function it_should_obey_first_monad_law()
    {
        $mAddOne = function ($value) {
            return $value + 1;
        };

        $this->beConstructedWith([1, 2, 3]);
        /** @var \PhpSpec\Wrapper\Subject $right */
        $left = $this->bind($mAddOne);
        $right = array_map($mAddOne, [1, 2, 3]);

        $left->shouldReturn($right);
    }

    public function it_should_obey_second_monad_law()
    {
        $this->beConstructedWith([[1, 2, 3]]);
        $left = $this->bind(\Monad\Collection::create);
        $right = [\Monad\Collection::create([1, 2, 3])];

        $left->shouldHaveSameLike($right);
    }

    public function it_should_obey_third_monad_law()
    {
        $addOne = function ($value) {
            return $value + 1;
        };
        $multiplyTwo = function ($value) {
            return $value * 2;
        };

        $this->beConstructedWith([1, 2, 3]);
        $right = $this->lift($addOne);
        $right = $right->lift($multiplyTwo);

        $left = $this->lift(function($x) use($addOne, $multiplyTwo){
            return $multiplyTwo($addOne($x));
        });

        $right->valueOf()->shouldReturn($left->valueOf());
    }

    public function it_should_extract_value_from_each_array_item()
    {
        $ref = ['a' => 2];
        $this->beConstructedWith([
            null,
            ['id' => 1, 'name' => 'A'],
            ['id' => 2, 'nick' => 'B', 'meta' => ['ref' => $ref]],
        ]);

        $get = function ($key) {
            return function (array $array) use ($key) {
                return isset($array[$key]) ? $array[$key] : null;
            };
        };

        $value = $this
            ->lift(\Monad\Maybe::create)
            ->lift($get('meta'))
            ->lift($get('ref'))
            ->valueOf();

        $value->shouldhaveKeyValue(2, $ref);
    }

    public function getMatchers()
    {
        $unWrap = function ($x) {
            return $x;
        };

        return [
            'haveSameLike' => function ($left, $right) use ($unWrap) {
                return $left[0]->bind($unWrap) === $right[0]->bind($unWrap);
            },
            'haveKeyValue' => function($array, $key, $value) {
                return $array[$key] === $value;
            }
        ];
    }
}
