<?php
namespace Applicative;

use Functor;

class Collection extends Functor\Collection implements ApplicativeInterface
{
    /**
     * Apply applicative on applicative.
     *
     * @param ApplicativeInterface $applicative
     * @return ApplicativeInterface
     */
    public function ap(ApplicativeInterface $applicative)
    {
        // Sine in php List comprehension is not available, then I doing it like this
        $result = [];
        $isCollection = $applicative instanceof Collection;

        foreach ($this->valueOf() as $value) {
            $partial = $applicative->map($value)->valueOf();
            if ($isCollection) {
                $result = \Functional\push($result, $partial);
            } else {
                $result[] = $partial;
            }
        }

        return $applicative::create($result);
    }
}