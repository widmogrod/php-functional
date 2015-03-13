<?php
namespace Applicative\Validator;

use Applicative\ApplicativeInterface;
use Common\CreateTrait;
use Functor\MapTrait;

class Success implements ApplicativeInterface
{
    use CreateTrait;
    use MapTrait;

    public function ap(ApplicativeInterface $applicative)
    {
        if ($applicative instanceof Failure) {
            return $applicative;
        } else {
            return $applicative->map($this->value);
        }
    }
}
