<?php

namespace example;

use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\IO;
use Widmogrod\Monad\State;
use function Widmogrod\Functional\append;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\match;
use function Widmogrod\Functional\pipeline;
use function Widmogrod\Monad\Free\liftF;
use function Widmogrod\Monad\Free\runFree;
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

const getLine_ = 'example\getLine_';

// getLine' :: Teletype String
function getLine_()
{
    return liftF(new GetLine());
}

const exitSuccess_ = 'example\exitSuccess_';

// exitSuccess' :: Teletype r
function exitSuccess_()
{
    return liftF(new ExitSuccess());
}

const interpretIO = 'example\interpretIO';

// run :: TeletypeF IO ()
function interpretIO(TeletypeF $r)
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

const interpretState = 'example\interpretState';

// runTest :: TeletypeF State []
function interpretState(TeletypeF $r)
{
    return match([
        PutStrLn::class => function (PutStrLn $a) {
            return State::of(function ($state) use ($a) {
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

function echo_chaining_()
{
    return getLine_()
        ->bind(function ($str) {
            return putStrLn_($str)
                ->bind(function () {
                    return exitSuccess_()
                        ->bind(function () {
                            // In interpretation of IO Monad this place will never be reached
                            return putStrLn_('Finished');
                        });
                });
        });
}

function echo_composition_()
{
    return pipeline(
        getLine_,
        bind(putStrLn_),
        bind(exitSuccess_),
        bind(putStrLn_) // In interpretation of IO Monad this place will never be reached
    )(null);
}

class FreeMonadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideEchoImplementation
     */
    public function test_it_should_allow_to_interpret_as_a_state_monad(MonadFree $echo)
    {
        $result = runFree(interpretState, $echo);
        $this->assertInstanceOf(State::class, $result);
        $result = State\execState($result, []);

        $this->assertEquals($result, [
            'GetLine',
            'PutStrLn',
            'ExitSuccess',
            'PutStrLn'
        ]);
    }

    /**
     * @dataProvider provideEchoImplementation
     */
    public function test_it_should_allow_to_interpret_as_IO(MonadFree $echo)
    {
        $result = runFree(interpretIO, $echo);
        $this->assertInstanceOf(IO::class, $result);
        // Since in PHPUnit STDIN is closed
        // this run will not work, but serves as an example
        // $result->run();
    }

    public function provideEchoImplementation()
    {
        return [
            'echo implementation via explicit chaining (bind)' => [echo_chaining_()],
            'echo implementation via function composition'     => [echo_composition_()],
        ];
    }
}
