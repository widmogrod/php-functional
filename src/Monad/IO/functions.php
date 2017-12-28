<?php

declare(strict_types=1);

namespace Widmogrod\Monad\IO;

use Widmogrod\Functional as f;
use Widmogrod\Monad as M;

const pure = 'Widmogrod\Monad\IO\pure';

/**
 * pure :: Applicative IO f => a -> f a
 *
 * @param callable $f
 *
 * @return M\IO
 */
function pure($f)
{
    return M\IO::of(function () use ($f) {
        return $f;
    });
}

const until = 'Widmogrod\Monad\IO\until';

/**
 * until :: (a -> Bool) -> (a -> b -> b) -> b -> IO a -> IO b
 *
 * @param callable $predicate (a -> Bool)
 * @param callable $do        (a -> b -> a)
 * @param mixed    $base      b
 * @param M\IO     $ioValue   IO a
 *
 * @return M\IO
 */
function until(callable $predicate, callable $do, $base, M\IO $ioValue)
{
    return M\IO::of(function () use ($predicate, $do, $base, $ioValue) {
        do {
            $value = $ioValue->run();
            $isFulfilled = $predicate($value);
            $base = $isFulfilled
                ? $base
                : $do($value, $base);
        } while (!$isFulfilled);

        return $base;
    });
}

const getChar = 'Widmogrod\Monad\IO\getChar';

/**
 * @throws IOError
 *
 * @return M\IO
 */
function getChar()
{
    return M\IO::of(function () {
        if (false === ($char = fgetc(STDIN))) {
            throw userError(
                'Can\'t read from stdin, because its closed'
            );
        }

        return $char;
    });
}

const getLine = 'Widmogrod\Monad\IO\getLine';

/**
 * getLine :: IO String
 *
 * @throws IOError
 *
 * @return M\IO
 */
function getLine()
{
    return until(f\eql("\n"), f\flip(f\concatStrings), '', getChar());
}

const putStrLn = 'Widmogrod\Monad\IO\putStrLn';

/**
 * putStrLn :: String -> IO ()
 *
 * @param $string
 *
 * @return M\IO
 */
function putStrLn($string)
{
    return M\IO::of(function () use ($string) {
        echo $string, "\n";
    });
}

const readFile = 'Widmogrod\Monad\IO\readFile';

/**
 * readFile :: String -> IO String
 *
 * @param $fileName
 *
 * @throws IOError
 *
 * @return M\IO
 */
function readFile($fileName)
{
    return M\IO::of(function () use ($fileName) {
        $content = @file_get_contents($fileName);
        if ($content === false) {
            throw userError(sprintf(
                'Have problems with reading file "%s", because: %s',
                $fileName,
                error_get_last()['message']
            ));
        }

        return $content;
    });
}

const writeFile = 'Widmogrod\Monad\IO\writeFile';

/**
 * writeFile :: String -> String -> IO ()
 *
 * @param string $fileName
 * @param string $content
 *
 * @throws IOError
 *
 * @return M\IO
 */
function writeFile($fileName, $content)
{
    return M\IO::of(function () use ($fileName, $content) {
        $content = file_put_contents($fileName, $content);
        if ($content === false) {
            throw userError(sprintf(
                'Have problems with writing to file "%s", because: %s',
                $fileName,
                error_get_last()['message']
            ));
        }

        return $content;
    });
}

const getArgs = 'Widmogrod\Monad\IO\getArgs';

/**
 * getArgs :: IO [String]
 *
 * Computation getArgs returns a list of the program's command line arguments (not including the program name).
 *
 * @throws IOError
 *
 * @return M\IO
 */
function getArgs()
{
    return M\IO::of(function () {
        if (!ini_get('register_argc_argv')) {
            throw userError(sprintf(
                'argv is not available, because ini option "register_argc_argv" is disabled'
            ));
        }

        return f\tail(f\fromIterable($_SERVER['argv']));
    });
}

const getEnv = 'Widmogrod\Monad\IO\getEnv';

/**
 * getEnv :: String -> IO String
 *
 * @param string $name
 *
 * @throws IOError
 *
 * @return M\IO
 */
function getEnv($name)
{
    return M\IO::of(function () use ($name) {
        $value = \getenv($name);
        if (false === $value) {
            throw userError(sprintf(
                'Environment variable "%s" does not exists.',
                $name
            ));
        }

        return $value;
    });
}

const lookupEnv = 'Widmogrod\Monad\IO\lookupEnv';

/**
 * lookupEnv :: String -> IO (Maybe String)
 *
 * @param string $name
 *
 * @return M\IO
 */
function lookupEnv($name)
{
    return tryMaybe(getEnv($name));
}
