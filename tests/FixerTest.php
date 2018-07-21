<?php

namespace Ahc\Test\Json;

use Ahc\Json\Fixer;

class FixerTest extends \PHPUnit\Framework\TestCase
{
    protected static $fixer;

    public static function setUpBeforeClass()
    {
        static::$fixer = new Fixer;
    }

    /** @dataProvider theTests */
    public function test($json, $expect, $msg = null)
    {
        $this->assertSame($expect, static::$fixer->fix($json), $msg);
    }

    public function theTests()
    {
        return [[
            'json'   => '',
            'expect' => '',
        ], [
            'json'   => '"',
            'expect' => '""',
        ], [
            'json'   => '"a"',
            'expect' => '"a"',
        ], [
            'json'   => 'true',
            'expect' => 'true',
        ], [
            'json'   => 'false',
            'expect' => 'false',
        ], [
            'json'   => 'null',
            'expect' => 'null',
        ], [
            'json'   => 'fal',
            'expect' => 'false',
        ], [
            'json'   => 't',
            'expect' => 'true',
        ], [
            'json'   => 'nu',
            'expect' => 'null',
        ], [
            'json'   => '{',
            'expect' => '{}',
        ], [
            'json'   => '[',
            'expect' => '[]',
        ], [
            'json'   => '[{',
            'expect' => '[{}]',
        ], [
            'json'   => '[1',
            'expect' => '[1]',
        ], [
            'json'   => '["',
            'expect' => '[""]',
        ], [
            'json'   => '[1,',
            'expect' => '[1]',
        ], [
            'json'   => '[1,{',
            'expect' => '[1,{}]',
        ], [
            'json'   => '["a',
            'expect' => '["a"]',
        ], [
            'json'   => '["b,',
            'expect' => '["b,"]',
        ], [
            'json'   => '["b",{"',
            'expect' => '["b",{"":null}]',
        ], [
            'json'   => '["b",{"a',
            'expect' => '["b",{"a":null}]',
        ], [
            'json'   => '["b",{"a":',
            'expect' => '["b",{"a":null}]',
        ], [
            'json'   => '["b",{"a":[t',
            'expect' => '["b",{"a":[true]}]',
        ], [
            'json'   => '{"a":2',
            'expect' => '{"a":2}',
        ], [
            'json'   => '{"',
            'expect' => '{"":null}',
        ], [
            'json'   => '{"a":1.2,',
            'expect' => '{"a":1.2}',
        ], [
            'json'   => '{"a":"',
            'expect' => '{"a":""}',
        ], [
            'json'   => '{"a":[',
            'expect' => '{"a":[]}',
        ], [
            'json'   => '{"a":"b","b":["',
            'expect' => '{"a":"b","b":[""]}',
        ], [
            'json'   => '{"a":"b","b":[t',
            'expect' => '{"a":"b","b":[true]}',
        ],
        ];
    }
}
