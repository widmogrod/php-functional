<?php

namespace example;

interface ScenarioF
{
    //    public function map(callable $function): ScenarioF;
}

class Given implements ScenarioF
{
    public function __construct(string $desc, When $when)
    {
    }
}

class When implements ScenarioF
{
    public function __construct(string $desc, When $when)
    {
    }
}

class Then implements ScenarioF
{
    public function __construct(string $desc, When $when)
    {
    }
}

class Scenario
{
    public function when()
    {
    }

    public function then()
    {
    }
}

function given(string $desc, $state): Scenario
{
}

class FreeDSLTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_interpret()
    {
    }
}
