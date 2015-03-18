<?php
namespace Functor;

use Common;

class Just implements FunctorInterface, Common\ValueOfInterface
{
    use Common\CreateTrait;
    use Common\ValueOfTrait;
    use MapTrait;
}
