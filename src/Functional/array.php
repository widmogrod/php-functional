<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

/**
 * @var callable
 */
const push_ = 'Widmogrod\Functional\push_';

/**
 * push_ :: array[a] -> array[a] -> array[a]
 *
 * Append array with values.
 * Mutation on the road! watch out!!
 *
 * @param array $array
 * @param array $values
 *
 * @return array
 */
function push_(array $array, array $values): array
{
    foreach ($values as $value) {
        $array[] = $value;
    }

    return $array;
}
