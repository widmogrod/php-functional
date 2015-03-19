<?php
namespace Applicative\Validator;

use Common;
use Functor;
use Applicative;

class Failure implements
    Applicative\ApplicativeInterface,
    Common\ValueOfInterface
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;
    use Functor\MapTrait;

    public function __construct($value)
    {
        $this->value = is_array($value) || $value instanceof \Traversable
            ? $value
            : [$value];
    }

    /**
     * Apply applicative on applicative.
     *
     * @param Applicative\ApplicativeInterface $applicative
     * @return Applicative\ApplicativeInterface
     */
    public function ap(Applicative\ApplicativeInterface $applicative)
    {
        if ($applicative instanceof Failure) {
            return $applicative->map(function ($v) {
                return array_merge($this->value, $v);
            });
        } else {
            return $this;
        }
    }
}
