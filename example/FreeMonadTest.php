<?php

declare(strict_types=1);

namespace example2;

use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Free as ff;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\IO;
use Widmogrod\Monad\State;
use Widmogrod\Primitive\Listt;
use const Widmogrod\Monad\IO\pure;
use const Widmogrod\Monad\State\value;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Useful\matchPatterns;

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
    public function map(callable $function): Functor
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
    public function map(callable $function): Functor
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
    public function map(callable $function): Functor
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
    return matchPatterns([
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
    return matchPatterns([
        PutStrLn::class => function (PutStrLn $a) {
            return State::of(function (Listt $state) use ($a) {
                return [
                    $a->next,
                    f\append($state, f\fromValue('PutStrLn'))
                ];
            });
        },
        GetLine::class => function (GetLine $a) {
            return State::of(function (Listt $state) use ($a) {
                return [
                    ($a->processor)('demo'),
                    f\append($state, f\fromValue('GetLine'))
                ];
            });
        },
        ExitSuccess::class => function (ExitSuccess $a) {
            return State::of(function (Listt $state) use ($a) {
                return [
                    ff\Pure::of('exit'),
                    f\append($state, f\fromValue('ExitSuccess'))
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
    return f\pipeline(
        getLine_,
        f\bind(putStrLn_),
        f\bind(exitSuccess_),
        f\bind(putStrLn_) // In interpretation of IO Monad this place will never be reached
    )();
}

class FreeMonadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideEchoImplementation
     */
    public function test_it_should_allow_to_interpret_as_a_state_monad(MonadFree $echo)
    {
        $result = ff\foldFree(interpretState, $echo, value);
        $this->assertInstanceOf(State::class, $result);
        $result = State\execState($result, fromNil());

        $this->assertEquals($result, f\fromIterable([
            'GetLine',
            'PutStrLn',
            'ExitSuccess',
        ]));
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
            'echo implementation via function composition' => [echo_composition_()],
        ];
    }
}
