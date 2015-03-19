<?php
require_once 'vendor/autoload.php';

use Functional as f;
use Applicative\Validator;

function isPasswordLongEnough($password)
{
    return strlen($password) > 6
        ? Validator\Success::create($password)
        : Validator\Failure::create(
            'Password must have more than 6 characters'
        );
}

function isPasswordStrongEnough($password)
{
    return preg_match('/[\W]/', $password)
        ? Validator\Success::create($password)
        : Validator\Failure::create([
            'Password must contain special characters'
        ]);
}

function isPasswordValid($password)
{
    return Validator\Success::create(Functional\curryN(2, function () use ($password) {
        return $password;
    }))
        ->ap(isPasswordLongEnough($password))
        ->ap(isPasswordStrongEnough($password));
}

$resultA = isPasswordValid("foo");
assert($resultA instanceof Applicative\Validator\Failure);
assert(f\valueOf($resultA) === [
    'Password must have more than 6 characters',
    'Password must contain special characters',
]);

$resultB = isPasswordValid("asdqMf67123!oo");
assert($resultB instanceof Applicative\Validator\Success);
assert(f\valueOf($resultB) === 'asdqMf67123!oo');
