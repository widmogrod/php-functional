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
        $this->shouldHaveType('FantasyLand\MonadInterface');
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

    public function it_should_obey_identity_law_applicative()
    {
        $this->beConstructedWith(function($x) { return $x; });
        $result = $this->ap($this::create([1,2]));

        $result->valueOf()->shouldReturn([1,2]);
    }

    public function it_should_obey_homomorphism_law_applicative()
    {
        $id = function($x) { return $x; };
        $this->beConstructedWith($id);
        $result = $this->ap($this::create([1,2]));

        $result->valueOf()->shouldReturn(
            $this::create($id([1,2]))->valueOf()
        );
    }

    public function it_should_obey_interchange_law_applicative()
    {
        $y = 1;
        $f = function($x) { return $x / 2; };

        $this->beConstructedWith($f);
        $result = $this->ap($this::create($y));

        $result->valueOf()->shouldReturn(
            $this::create(function($f) use ($y) {
                return $f($y);
            })->ap($this)->valueOf()
        );
    }

    public function it_should_obey_identity_law_functor()
    {
        $this->beConstructedWith([1, 2]);
        $result = $this->map(function ($x) {
            return $x;
        });

        $result->valueOf()->shouldReturn([1, 2]);
    }

    public function it_should_obey_composition_law_functor()
    {
        $a = function($x) { return $x + 1; };
        $b = function($x) { return $x + 2; };
        $this->beConstructedWith([1, 2]);

        $result = $this->map($a)->map($b);
        $result->valueOf()->shouldReturn([$b($a(1)), $b($a(2))]);
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
