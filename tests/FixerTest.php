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

    public function test_invalid_literal()
    {
        $this->assertSame('{"a" : invalid', static::$fixer->fix('{"a" : invalid', true));
        $this->assertSame(' hmm ', static::$fixer->fix(' hmm ', true));
    }

    public function test_ws()
    {
        $this->assertSame('{ "a"  :null}', static::$fixer->fix('{ "a"  :'));
        $this->assertSame("\n [{}]", static::$fixer->fix("\n [{,"));
    }

    public function test_custom_missing()
    {
        $this->assertSame('{"a":false}', static::$fixer->fix('{"a', false, 'false'));
        $this->assertSame('{"a":true}', static::$fixer->fix('{"a":', false, 'true'));
        $this->assertSame('{"a":1,"b":"missing"}', static::$fixer->fix('{"a":1,"b"', false, '"missing"'));
    }

    public function test_fail_silent()
    {
        $this->assertSame('{"a"}', static::$fixer->fix('{"a"}', true));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not fix JSON
     */
    public function test_fail_throws()
    {
        static::$fixer->fix('{,"a');
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
            'json'   => '12.34',
            'expect' => '12.34',
        ], [
            'json'   => '"str',
            'expect' => '"str"',
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
            'json'   => '{"a":',
            'expect' => '{"a":null}',
        ], [
            'json'   => '{"a"',
            'expect' => '{"a":null}',
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
        ], [
            'json'   => '[ {"id":1, "data": []}, {"id":2, "data": [',
            'expect' => '[ {"id":1, "data": []}, {"id":2, "data": []}]',
        ],
        ];
    }
}
