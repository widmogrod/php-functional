<?php
require_once 'vendor/autoload.php';

use Monad\Either as E;
use Functional as f;

function validateName(array $request)
{
    return $request['name'] === ''
        ? E\Left::create('Request name is empty')
        : E\Right::create($request);
}

function validateEmail(array $request)
{
    return $request['email'] === ''
        ? E\Left::create('Request e-mail is empty')
        : E\Right::create($request);
}

function validateNameLength(array $request)
{
    return strlen($request['name']) > 30
        ? E\Left::create('Request name is to long.')
        : E\Right::create($request);
}

function validateInput(array $request)
{
    return E\Right::create($request)
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
    if (rand(1, 1000) > 500) {
        throw new \Exception('Cannot connect to database');
    }
}

function sendEmail(array $request)
{
    if (rand(1, 1000) > 700) {
        throw new \Exception('Cannot connect to smtp server');
    }
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

$request = [
    'name' => ['', 'Gabriel', 'SomeForeignNameThatIsToLongAndWhoKnowsWhatElese'][rand(0, 2)],
    'email' => ['', 'test@test.pl'][rand(0, 1)]
];

function handleRequest(array $request)
{
    return
        validateInput($request)
        ->lift('canonizeEmail')
        ->bind(E\tryCatch(f\tee('updateDatabase'), function(\Exception $e) { return $e->getMessage(); }))
        ->lift('returnMessage')
        ->orElse('returnFailure');
}
var_dump(handleRequest($request));

