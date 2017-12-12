<?php

use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Monad\Maybe\maybeNull;

class MaybeMonoidTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_concat_only_just_values($data, $expected)
    {
        $makeMaybeMonoid = function ($val) {
            return maybeNull($val)->map(Listt::of);
        };

        $names = array_values(array_map($makeMaybeMonoid, $data));

        list($firstName, $middleName, $lastName) = $names;

        $fullName = $firstName->concat($middleName)->concat($lastName);
        $fullNameFromReduce = array_reduce($names, f\concatM, Nothing::mempty());

        $this->assertInstanceOf(Just::class, $fullName);
        $this->assertEquals($fullName->extract()->extract(), $expected);
        $this->assertEquals($fullNameFromReduce->extract()->extract(), $expected);
    }

    public function provideData()
    {
        return [
            [
                '$data'     => ['firstName' => 'First', 'middleName' => null, 'lastName' => 'Last'],
                '$expected' => ['First', 'Last']
            ],
            [
                '$data'     => ['firstName' => 'First', 'middleName' => 'Middle', 'lastName' => 'Last'],
                '$expected' => ['First', 'Middle', 'Last']
            ]
        ];
    }
}
