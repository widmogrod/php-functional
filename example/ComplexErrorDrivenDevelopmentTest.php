<?php
require_once 'vendor/autoload.php';

use Monad\Either as E;
use Functional as f;

function validateName(array $request)
{
    return $request['name'] === ''
        ? E\Left::of('Request name is empty')
        : E\Right::of($request);
}

function validateEmail(array $request)
{
    return $request['email'] === ''
        ? E\Left::of('Request e-mail is empty')
        : E\Right::of($request);
}

function validateNameLength(array $request)
{
    return strlen($request['name']) > 30
        ? E\Left::of('Request name is to long')
        : E\Right::of($request);
}

function validateInput(array $request)
{
    return E\Right::of($request)
        ->bind('validateName')
        ->bind('validateEmail')
        ->bind('validateNameLength');
}

function canonizeEmail(array $request)
{
    $request['email'] = strtolower(trim($request['email']));

    return $request;
}

function updateDatabase(array $request)
{
    // may throw exception
}

function updateDatabaseStep(array $request)
{
    return call_user_func(E\tryCatch(
        f\tee('updateDatabase'),
        function (\Exception $e) {
            return $e->getMessage();
        }
    ), $request);
}

function sendMessage(E\Either $either)
{
    return E\doubleMap('returnMessage', 'returnFailure', $either);
}

function returnMessage(array $request)
{
    return [
        'status' => 200,
    ];
}

function returnFailure($data)
{
    return [
        'error' => (string)$data,
    ];
}

function handleRequest(array $request)
{
    return call_user_func(f\pipeline(
        'validateInput',
        f\map('canonizeEmail'),
        f\bind('updateDatabaseStep'),
        'sendMessage'
    ), $request);
}

class ComplexErrorDrivenDevelopmentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_be_prepared_of_errors(array $request, $isError, $expected)
    {
        $result = handleRequest($request);

        $this->assertInstanceOf(
            $isError ? E\Left::class : E\Right::class,
            $result
        );

        $this->assertEquals($expected, f\valueOf($result));
    }

    public function provideData()
    {
        return [
            'success case' => [
                '$request' => [
                    'name' => 'Jone Doe',
                    'email' => 'test@example.com'
                ],
                '$isError' => false,
                '$expected' => ['status' => 200],
            ],
            'username to short' => [
                '$request' => [
                    'name' => '',
                    'email' => 'test@example.com'
                ],
                '$isError' => true,
                '$expected' => ['error' => 'Request name is empty'],
            ],
            'username to long' => [
                '$request' => [
                    'name' => 'asd asdasdlaks askl djalskd jalskdjaslkdjasldjadsa asd',
                    'email' => 'test@example.com'
                ],
                '$isError' => true,
                '$expected' => ['error' => 'Request name is to long'],
            ],
            'email empty' => [
                '$request' => [
                    'name' => 'Jone Doe',
                    'email' => ''
                ],
                '$isError' => true,
                '$expected' => ['error' => 'Request e-mail is empty'],
            ],
        ];
    }
}
