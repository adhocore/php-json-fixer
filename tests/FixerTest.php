<?php

/*
 * This file is part of the PHP-JSON-FIXER package.
 *
 * (c) Jitendra Adhikari <jiten.adhikary@gmail.com>
 *     <https://github.com/adhocore>
 *
 * Licensed under MIT license.
 */

namespace Ahc\Test\Json;

use Ahc\Json\Fixer;

class FixerTest extends \PHPUnit\Framework\TestCase
{
    protected $fixer;

    protected function setUp()
    {
        $this->fixer = new Fixer;
    }

    /** @dataProvider theTests */
    public function test($json, $expect, $msg = null)
    {
        $this->assertSame($expect, $this->fixer->fix($json), $msg);
    }

    public function test_invalid_literal()
    {
        $this->fixer->silent(true);

        $this->assertSame('{"a" : invalid', $this->fixer->fix('{"a" : invalid'));
        $this->assertSame(' hmm ', $this->fixer->fix(' hmm '));
    }

    public function test_ws()
    {
        $this->assertSame('{ "a"  :null}', $this->fixer->missingValue(null)->fix('{ "a"  :'));
        $this->assertSame("\n [{}]", $this->fixer->fix("\n [{,"));
    }

    public function test_custom_missing()
    {
        $this->assertSame('{"a":false}', $this->fixer->missingValue(false)->fix('{"a'));
        $this->assertSame('{"a":true}', $this->fixer->missingValue('true')->fix('{"a":'));
        $this->assertSame('{"a":1,"b":"missing"}', $this->fixer->missingValue('"missing"')->fix('{"a":1,"b"'));
    }

    public function test_fail_silent()
    {
        $this->fixer->silent(true);

        $this->assertSame('{"a"}', $this->fixer->fix('{"a"}'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Could not fix JSON
     */
    public function test_fail_throws()
    {
        $this->fixer->silent(false);

        $this->fixer->fix('{,"a');
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
