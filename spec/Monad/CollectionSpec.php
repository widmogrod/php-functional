<?php
namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Functional as f;

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
        $this->shouldHaveType('Common\ConcatInterface');
    }

    public function it_should_obey_first_monad_law()
    {
        $this->beConstructedWith([1, 2, 3]);
        /** @var \PhpSpec\Wrapper\Subject $right */
        $left = $this->bind(\Monad\Collection::create);
        $right = $this::create([1, 2, 3]);

        $left->shouldHaveSameLike($right);
    }

    public function it_should_obey_second_monad_law()
    {
        $mAddOne = function ($value) {
            return $value + 1;
        };

        $this->beConstructedWith([1, 2, 3]);
        /** @var \PhpSpec\Wrapper\Subject $right */
        $left = $this->bind($mAddOne);
        $right = array_map($mAddOne, [1, 2, 3]);
        $right = $this::create($right);

        $left->shouldHaveSameLike($right);
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Identity::create($value + 1);
        };
        $mMultiplyTwo = function ($value) {
            return \Monad\Identity::create($value * 2);
        };

        $this->beConstructedWith([1, 2, 3]);
        $right = $this->bind($mAddOne);
        $right = $right->bind($mMultiplyTwo);

        $left = $this->bind(function ($x) use ($mAddOne, $mMultiplyTwo) {
            return $mAddOne($x)->bind($mMultiplyTwo);
        });

        $right->valueOf()->shouldReturn($left->valueOf());
    }

    public function getMatchers()
    {
        return [
            'haveSameLike' => function ($left, $right) {
                return f\valueOf($left) === f\valueOf($right);
            },
            'haveKeyValue' => function ($array, $key, $value) {
                return $array[$key] === $value;
            }
        ];
    }
}
