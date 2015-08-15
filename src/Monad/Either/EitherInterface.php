<?php
namespace Monad\Either;

use Monad;
use Functor;
use Common;

interface EitherInterface extends
    Monad\MonadInterface,
    Monad\Feature\OrElseInterface,
    Functor\FunctorInterface,
    Common\ValueOfInterface
{

}
