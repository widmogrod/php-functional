<?php
namespace Applicative;

use Common;

class Just implements
    ApplicativeInterface,
    Common\CreateInterface
{
    use Common\CreateTrait;

    /**
     * @param ApplicativeInterface $applicative
     * @return ApplicativeInterface
     */
    public function ap(ApplicativeInterface $applicative)
    {
        // TODO: Implement ap() method.
    }

    public function map(callable $transformation)
    {
        // TODO: Implement map() method.
    }
}
