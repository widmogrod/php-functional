<?php
require_once 'vendor/autoload.php';

use Monad\State;
use Monad\Maybe;
use Functional as f;

interface Cacher
{
    /**
     * @param string $key
     * @return Maybe\Maybe
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function put($key, $value);
}

class InMemoryCache implements Cacher
{
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function get($key)
    {
        return array_key_exists($key, $this->data)
            ? Maybe\just($this->data[$key])
            : Maybe\nothing();
    }

    public function put($key, $value)
    {
        return new InMemoryCache(array_merge(
            $this->data,
            [$key => $value]
        ));
    }
}


// checkRelatedInCache :: String -> State (Maybe a, s)
function checkRelatedInCache($productName)
{
    return State::of(function (Cacher $cache) use ($productName) {
        return [$cache->get($productName), $cache];
    });
}

// searchRelated :: String -> State (s, [])
function searchRelated($productName)
{
    // TODO: 1. try use doM
    // TODO: 2. try implement getOrElse
    return checkRelatedInCache($productName)
        ->bind(function (Maybe\Maybe $products) use ($productName) {
            switch (get_class($products)) {
                case Monad\Maybe\Just::class:
                    return State\value($products->extract());
//                    return State::of(function ($s) use ($products) {
//                        return [$s, $products->extract()];
//                    });
                case Monad\Maybe\Nothing::class:
                    return retrieveRelated($productName);
            }
        });
}

// retrieveRelated :: String -> State (Cacher, [])
function retrieveRelated($productName)
{
    return State::of(function (Cacher $cache) use ($productName) {
        // do some database work
        $products = ['iPhone 5', 'iPhone 6s'];
        return [$products, $cache->put($productName, $products)];
    });
}

$s1 = new InMemoryCache([]);
$r1 = searchRelated('asia')
    ->run($s1);

var_dump($r1);

$s2 = $r1[1];
$r2 = searchRelated('asia')
    ->run($s2);

var_dump($r2);