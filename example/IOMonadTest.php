<?php
namespace example;

use Monad as M;
use Monad\Either as E;
use Functional as f;

class IOMonadTest extends \PHPUnit_Framework_TestCase
{
    public function test_io()
    {
        // $log :: String -> IO String
        $log = function ($x) {
            return M\IO::of(function () use ($x) {
                var_dump($x);

                return $x;
            });
        };

        $setStyle = f\curryN(2, function ($path, $prop) {
            return M\IO::of(function () use ($path, $prop) {
                var_dump('stype set to', $path, $path);
            });
        });

        // $getItem :: String -> IO String
        $getItem = function ($key) {
            return M\IO::of(function () use ($key) {
                // Do search somewhere
                return json_encode([
                    'color' => 'red is ' . $key,
                ]);
            });
        };

        // $readFile :: String -> IO (Either String String)
        $readFile = function ($file) {
            return M\IO::of(function () use ($file) {
                $content = @file_get_contents($file);

                return false !== $content
                    ? E\succeed($content)
                    : E\fail("File $file does not exists");
            });
        };

        // $parseDom :: String -> IO (Maybe DOMDocument)
        $parseDom = function ($string) {
            return M\IO::of(function () use ($string) {
                $dom = new \DOMDocument();

                return $dom->loadHTML($string)
                    ? M\Maybe\just($dom)
                    : M\Maybe\nothing();
            });
        };

        // $fileToDom :: String -> IO (Either String (Maybe DOMDocument))
        $fileToDom = f\pipeline(
            $readFile,
            f\bind(E\either(f\identity(), f\tee('var_dump'))),
            f\bind($parseDom)
        );

        // TODO make it as a helper
        $file = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($file, <<<HTML
<html>
<head></head>
<body>
<div id="name">Guest?</div>
</body>
</html>
HTML
        );


        $file = 'asd';
        print_r($fileToDom($file)->run());

//        $applyPreferences = f\pipeline(
//            $getItem,
//            f\map('json_decode'),
//            f\bind($log),
//            f\bind($setStyle('#main'))
//        );
//
//        $applyPreferences('tom')->run();
    }
}
