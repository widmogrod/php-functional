<?php

declare(strict_types=1);

namespace example;

use Widmogrod\Monad\Reader as R;

function hello($name)
{
    return "Hello $name!";
}

function ask($content)
{
    return R::of(function ($name) use ($content) {
        return $content .
            ($name == 'World' ? '' : ' How are you ?');
    });
}

class ReaderMonadTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_should_pass_the_name_around()
    {
        $r = R\reader('example\hello')
            ->bind('example\ask')
            ->map('strtoupper');

        $this->assertEquals(
            'HELLO WORLD!',
            $r->runReader('World')
        );
        $this->assertEquals(
            'HELLO GILLES! HOW ARE YOU ?',
            $r->runReader('Gilles')
        );
    }
}
