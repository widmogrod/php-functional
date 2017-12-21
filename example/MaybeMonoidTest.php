<?php

declare(strict_types=1);

use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Maybe;
use Widmogrod\Primitive\Stringg;
use const Widmogrod\Monad\Maybe\maybeNull;
use function Widmogrod\Functional\map;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\maybeNull;

class MaybeMonoidTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_concat_only_strings_and_skip_nulls(array $data, array $expected, string $asString)
    {
        $fullName = f\fromIterable($data)
            ->map(maybeNull)
            ->map(map(Stringg::of))
            ->reduce(f\concatM, just(Stringg::mempty()));

        $this->assertInstanceOf(Just::class, $fullName);
        $this->assertEquals($fullName, just(Stringg::of($asString)));
    }

    /**
     * @dataProvider provideData
     */
    public function test_it_should_concat_only_just_values_naive_string_implementation(array $data, array $expected, string $asString)
    {
        // $makeMaybeMonoid :: string -> Maybe Stringg
        $makeMaybeMonoid = function ($val): Maybe {
            return maybeNull($val)->map(Stringg::of);
        };

        // $names :: array Maybe Stringg
        $names = array_values(array_map($makeMaybeMonoid, $data));

        // $firstName :: Maybe Stringg
        // $middleName :: Maybe Stringg
        // $lastName :: Maybe Stringg
        list($firstName, $middleName, $lastName) = $names;

        // $fullName :: Maybe Stringg
        $fullName = $firstName->concat($middleName)->concat($lastName);

        $this->assertInstanceOf(Just::class, $fullName);
        $this->assertEquals($fullName, just(Stringg::of($asString)));
    }

    /**
     * @dataProvider provideData
     */
    public function test_it_should_concat_only_just_values_list_naive_implementation2(array $data, array $expected)
    {
        // $makeMaybeMonoid :: string -> Maybe Listt string
        $makeMaybeMonoid = function ($val): Maybe {
            return maybeNull($val)->map(f\fromValue);
        };

        // $names :: array Maybe Listt string
        $names = array_values(array_map($makeMaybeMonoid, $data));

        // $firstName :: Maybe Listt string
        // $middleName :: Maybe Listt string
        // $lastName :: Maybe Listt string
        list($firstName, $middleName, $lastName) = $names;

        // $fullName :: Maybe Listt string
        $fullName = $firstName->concat($middleName)->concat($lastName);

        $this->assertInstanceOf(Just::class, $fullName);
        $this->assertEquals($fullName, just(f\fromIterable($expected)));
    }

    public function provideData()
    {
        return [
            'array with null values' => [
                '$data' => [
                    'firstName' => 'First',
                    'middleName' => null,
                    'lastName' => 'Last'
                ],
                '$expected' => ['First', 'Last'],
                '$asString' => 'FirstLast',
            ],
            'array with strings' => [
                '$data' => [
                    'firstName' => 'First',
                    'middleName' => 'Middle',
                    'lastName' => 'Last'
                ],
                '$expected' => ['First', 'Middle', 'Last'],
                '$asString' => 'FirstMiddleLast',
            ]
        ];
    }
}
