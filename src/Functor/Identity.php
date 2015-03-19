<?php
namespace Functor;

use Common;

class Identity implements FunctorInterface, Common\ValueOfInterface
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;
    use MapTrait;

    const create = 'Functor\Identity::create';
}
