<?php
require_once 'vendor/autoload.php';

use Applicative\Validator;

function isPasswordLongEnough($password)
{
    return strlen($password) > 6
        ? Validator\Success::create($password)
        : Validator\Failure::create([
            'Password must have more than 6 characters'
        ]);
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
    return Validator\Success::create(function () use ($password) {
        return function () use ($password) {
            return $password;
        };
    })
    ->ap(isPasswordLongEnough($password))
    ->ap(isPasswordStrongEnough($password));
}

var_dump(isPasswordValid("foo"));
var_dump(isPasswordValid("asdqMf67123!oo"));
