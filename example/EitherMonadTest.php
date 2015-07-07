<?php
namespace example;

use Functional as f;
use Monad\Either;

function read($file)
{
    return is_file($file)
        ? Either\Right::create(file_get_contents($file))
        : Either\Left::create(sprintf('File "%s" does not exists', $file));
}

class EitherMonadTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_concat_content_of_two_files_only_when_files_exists()
    {
        $concat = f\liftM2(
            read(__FILE__),
            read('aaa'),
            function ($first, $second) {
                return $first . $second;
            }
        );

        $this->assertInstanceOf(Either\Left::class, $concat);
        $this->assertEquals('File "aaa" does not exists', $concat->orElse('Functional\identity'));
    }
}


