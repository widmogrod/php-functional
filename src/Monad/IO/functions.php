<?php
namespace Monad\IO;

use Functional as f;
use Monad as M;

const getLine = 'Monad\IO\getLine';

/**
 * getLine :: IO String
 *
 * @return M\IO
 */
function getLine()
{
    return M\IO::of(function () {
        $f = fopen('php://stdin', 'rb');
        $line = '';
        while (($char = fread($f, 1)) !== "\n") {
            $line .= $char;
        }

        return $line;
    });
}

const putStrLn = 'Monad\IO\putStrLn';

/**
 * putStrLn :: String -> IO ()
 *
 * @param $string
 * @return M\IO
 */
function putStrLn($string)
{
    return M\IO::of(function () use ($string) {
        echo $string, "\n";
    });
}

const readFile = 'Monad\IO\readFile';

/**
 * readFile :: String -> IO String
 *
 * @param $fileName
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

const writeFile = 'Monad\IO\writeFile';

/**
 * writeFile :: String -> String -> IO ()
 *
 * @param string $fileName
 * @param string $content
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
