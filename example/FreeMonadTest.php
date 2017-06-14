<?php

namespace example;

use Widmogrod\Monad\IO;
use Widmogrod\Monad\State;
use function Widmogrod\Functional\append;
use function Widmogrod\Functional\match;
use function Widmogrod\Monad\Free\liftF;
use function Widmogrod\Monad\IO\getLine;

interface TeletypeF
{
}

class PutStrLn implements TeletypeF
{
    public $str;

    public function __construct($str)
    {
        $this->str = $str;
    }
}

class GetLine implements TeletypeF
{
}

class ExitSuccess implements TeletypeF
{
}

const putStrLn_ = 'example\putStrLn_';

// putStrLn' :: String -> Teletype ()
function putStrLn_($str)
{
    return liftF(new PutStrLn($str, null));
}

// getLine' :: Teletype String
function getLine_()
{
    return liftF(new GetLine());
}

// exitSuccess' :: Teletype r
function exitSuccess_()
{
    return liftF(new ExitSuccess());
}

const run = 'example\run';

// run :: TeletypeF IO ()
function runIO(TeletypeF $r)
{
    return match([
        PutStrLn::class => function (PutStrLn $a) {
            return IO\putStrLn($a->str);
        },
        GetLine::class => function (GetLine $a) {
            return getLine();
        },
        ExitSuccess::class => function (ExitSuccess $a) {
            return exit();
        },
    ], $r);
}

const runTest = 'example\runState';

// runTest :: TeletypeF State []
function runState(TeletypeF $r)
{
    return match([
        PutStrLn::class => function (PutStrLn $a) {
            return State::of(function ($state) {
                return ['PutStrLn', append($state, 'PutStrLn')];
            });
        },
        GetLine::class => function (GetLine $a) {
            return State::of(function ($state) {
                return ['GetLine', append($state, 'GetLine')];
            });
        },
        ExitSuccess::class => function (ExitSuccess $a) {
            return State::of(function ($state) {
                return ['ExitSuccess', append($state, 'ExitSuccess')];
            });
        },
    ], $r);
}

function echo_()
{
    return getLine_()
        ->bind(function ($str) {
            return putStrLn_($str)
                ->bind(function () {
                    return exitSuccess_()
                        ->bind(function () {
                            return putStrLn_('Finished');
                        });
                });
        });
}

class FreeMonadTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_allow_to_interpret()
    {
        $this->markTestSkipped();
        $e = echo_();

        var_dump([
//            'result1' => $e->runFree(run)->run(),
            'result2' => State\runState($e->runFree(runTest), []),
        ]);
    }
}
