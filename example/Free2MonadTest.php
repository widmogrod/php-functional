<?php

namespace example2;

use Widmogrod\FantasyLand\Functor;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Free2 as ff;
use Widmogrod\Monad\Free2\MonadFree;
use Widmogrod\Monad\IO;
use Widmogrod\Monad\State;
use const Widmogrod\Monad\IO\pure;
use const Widmogrod\Monad\State\value;

interface TeletypeF extends Functor
{
}

class PutStrLn implements TeletypeF
{
    public $str;
    public $next;

    public function __construct($str, ff\MonadFree $next)
    {
        $this->str = $str;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return new self(
            $this->str,
            $function($this->next)
        );
    }
}

class GetLine implements TeletypeF
{
    /**
     * @var callable
     */
    public $processor;

    public function __construct(callable $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return new self(function ($x) use ($function) {
            return $function(($this->processor)($x));
        });
    }
}

class ExitSuccess implements TeletypeF
{
    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return $this;
    }
}

const putStrLn_ = 'example2\putStrLn_';

// putStrLn' :: String -> Teletype ()
function putStrLn_($str)
{
    return ff\liftF(new PutStrLn($str, ff\Pure::of(null)));
}

const getLine_ = 'example2\getLine_';

// getLine' :: Teletype String
function getLine_()
{
    return ff\liftF(new GetLine(ff\Pure::of));
}

const exitSuccess_ = 'example2\exitSuccess_';

// exitSuccess' :: Teletype r
function exitSuccess_()
{
    return ff\liftF(new ExitSuccess());
}

const interpretIO = 'example2\interpretIO';

// run :: TeletypeF -> IO ()
function interpretIO(TeletypeF $r)
{
    return f\match([
        PutStrLn::class => function (PutStrLn $a) {
            return IO\putStrLn($a->str)->map(function () use ($a) {
                return $a->next;
            });
        },
        GetLine::class => function (GetLine $a) {
            return IO\getLine()->bind($a->processor);
        },
        ExitSuccess::class => function (ExitSuccess $a) {
            return IO\putStrLn('exit')->bind(ff\Pure::of);
        },
    ], $r);
}

const interpretState = 'example2\interpretState';

// runTest :: TeletypeF -> State MonadFree []
function interpretState(TeletypeF $r)
{
    return f\match([
        PutStrLn::class => function (PutStrLn $a) {
            return State::of(function ($state) use ($a) {
                return [
                    $a->next,
                    f\appendNativeArr($state, 'PutStrLn')
                ];
            });
        },
        GetLine::class => function (GetLine $a) {
            return State::of(function ($state) use ($a) {
                return [
                    ($a->processor)('demo'),
                    f\appendNativeArr($state, 'GetLine')
                ];
            });
        },
        ExitSuccess::class => function (ExitSuccess $a) {
            return State::of(function ($state) use ($a) {
                return [
                    ff\Pure::of('exit'),
                    f\appendNativeArr($state, 'ExitSuccess')
                ];
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
    return call_user_func(f\pipeline(
        getLine_,
        f\bind(putStrLn_),
        f\bind(exitSuccess_),
        f\bind(putStrLn_) // In interpretation of IO Monad this place will never be reached
    ));
}

class Free2MonadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideEchoImplementation
     */
    public function test_it_should_allow_to_interpret_as_a_state_monad(MonadFree $echo)
    {
        $result = ff\foldFree(interpretState, $echo, value);
        $this->assertInstanceOf(State::class, $result);
        $result = State\execState($result, []);

        $this->assertEquals($result, [
            'GetLine',
            'PutStrLn',
            'ExitSuccess',
        ]);
    }

    /**
     * @dataProvider provideEchoImplementation
     */
    public function test_it_should_allow_to_interpret_as_IO(MonadFree $echo)
    {
        $result = ff\foldFree(interpretIO, $echo, pure);
        $this->assertInstanceOf(IO::class, $result);
        // Since this requires input, which would block unit
        // This test serves as an example, uncomment line bellow
        // for your local test
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
