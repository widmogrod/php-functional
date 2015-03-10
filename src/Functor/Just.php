<?php
namespace Functor;

use Common;

class Just implements
    FunctorInterface,
    Common\CreateInterface
{
    use Common\CreateTrait;
    use MapTrait;
}
