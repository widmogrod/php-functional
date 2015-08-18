<?php
namespace example;

use Monad as M;
use Monad\Either as E;
use Functional as f;

class IOMonadTest extends \PHPUnit_Framework_TestCase
{
    private $global = [];

    public function setUp()
    {
        $this->global = [];
    }

    /**
     * @dataProvider provideData
     */
    public function test_io($expected, $userName, $filePath)
    {
        // $log :: String -> IO String
        $log = function ($x) {
            return M\IO::of(function () use ($x) {
                // should log to standard output
                // but I just capturing it into array
                $this->global[] = (array) $x;

                return $x;
            });
        };

//        $setStyle = f\curryN(2, function (\DOMDocument $dom, $path, $prop) {
//            $xpath = new \DOMXPath($dom);
//            $list = $xpath->query($path);
//            foreach ($list as $item) {
//                /** @var \DOMNode $item */
//                $item->attributes;
//            }
//
//            return M\IO::of(function () use ($path, $prop) {
//                var_dump('stype set to', $path, $path);
//            });
//        });

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
            $readFile
            , f\map(f\map(f\compose(f\join(), $parseDom)))
        );

        // $domOrError :: Either String (Maybe DOMDocument)
        $domOrError = $fileToDom($filePath)->run();


        $applyPreferences = f\pipeline(
            $getItem
            , f\map('json_decode')
            , f\bind($log)
//            , f\bind($setStyle('//[@id="main"]'))
        );

        $applyPreferences($userName)->run();

        $this->assertEquals(
            $expected,
            $this->global
        );
    }

    public function provideData()
    {
        $filePath = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($filePath, <<<HTML
<html>
<head></head>
<body>
<div id="main">Guest?</div>
</body>
</html>
HTML
        );

        return [
            'default' => [
                '$expected' => [
                    ['color' => 'red is tom']
                ],
                '$userName' => 'tom',
                '$filePath' => $filePath
            ],
        ];
    }
}
