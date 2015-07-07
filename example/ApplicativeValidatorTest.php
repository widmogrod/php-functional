<?php
namespace example;

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
    return Validator\Success::create(f\curryN(2, function () use ($password) {
        return $password;
    }))
        ->ap(isPasswordLongEnough($password))
        ->ap(isPasswordStrongEnough($password));
}

class ApplicativeValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_collect_error_messages()
    {
        $result = isPasswordValid("foo");

        $this->assertInstanceOf(Validator\Failure::class, $result);
        $this->assertEquals([
            'Password must have more than 6 characters',
            'Password must contain special characters',
        ], f\valueOf($result));
    }

    public function test_it_should_return_valid_data()
    {
        $result = isPasswordValid("asdqMf67123!oo");

        $this->assertInstanceOf(Validator\Success::class, $result);
        $this->assertEquals('asdqMf67123!oo', f\valueOf($result));
    }
}
