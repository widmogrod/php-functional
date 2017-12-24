<?php

declare(strict_types=1);

namespace example;

use Widmogrod\Monad\Maybe;
use Widmogrod\Monad\State as S;

/**
 * Caching is an example state that you could have in your application.
 */
interface Cacher
{
    /**
     * @param string $key
     *
     * @return Maybe\Maybe
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed  $value
     *
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
        return new self(array_merge(
            $this->data,
            [$key => $value]
        ));
    }
}

// checkRelatedInCache :: String -> State (Maybe a, s)
function checkRelatedInCache($productName)
{
    return S::of(function (Cacher $cache) use ($productName) {
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
                case Maybe\Just::class:
                    return S\value($products->extract());
                case Maybe\Nothing::class:
                    return retrieveRelated($productName);
            }
        });
}

// retrieveRelated :: String -> State (Cacher, [])
function retrieveRelated($productName)
{
    return S::of(function (Cacher $cache) use ($productName) {
        // do some database work
        $products = ['iPhone 5', 'iPhone 6s'];

        return [$products, $cache->put($productName, $products)];
    });
}

class StateMonadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_demonstrate_state_monad($expectedProducts)
    {
        $initialState = new InMemoryCache([]);
        list($result1, $outputState1) = S\runState(
            searchRelated('asia'),
            $initialState
        );
        $this->assertEquals($expectedProducts, $result1);

        list($result2, $outputState2) = S\runState(
            searchRelated('asia'),
            $outputState1
        );
        $this->assertEquals($expectedProducts, $result2);

        // After second computation, state shouldn't be modified
        // Because we have item already in cache
        $this->assertSame($outputState1, $outputState2);
    }

    public function provideData()
    {
        return [
            'default' => [
                '$expectedProducts' => ['iPhone 5', 'iPhone 6s'],
            ],
        ];
    }
}
