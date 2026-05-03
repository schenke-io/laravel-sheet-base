<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Unit\Skills;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Skills\MakePhp;

class MakePhpTest extends TestCase
{
    use MakePhp;

    public function test_pretty_array_simple()
    {
        $data = ['a' => 1, 'b' => 'c'];
        $result = $this->prettyArray($data);
        $this->assertStringContainsString("'a' => 1", $result);
        $this->assertStringContainsString("'b' => 'c'", $result);
        $this->assertStringStartsWith('[', $result);
        $this->assertStringEndsWith(']', $result);
    }

    public function test_pretty_array_nested()
    {
        $data = ['a' => ['b' => 1]];
        $result = $this->prettyArray($data);
        $this->assertStringContainsString("'a' => [", $result);
        $this->assertStringContainsString("'b' => 1", $result);
    }

    public function test_pretty_array_with_bool_and_null()
    {
        $data = ['a' => true, 'b' => false, 'c' => null];
        $result = $this->prettyArray($data);
        $this->assertStringContainsString("'a' => true", $result);
        $this->assertStringContainsString("'b' => false", $result);
        $this->assertStringContainsString("'c' => null", $result);
    }

    public function test_get_comment()
    {
        $result = $this->getComment('//', 'TestWriter');
        $this->assertStringContainsString('// ============================================================', $result);
        $this->assertStringContainsString('// auto-written by TestWriter', $result);
        $this->assertStringContainsString('// do not edit manually', $result);
    }

    public function test_get_php()
    {
        $data = ['a' => 1];
        $result = $this->getPhp($data, 'TestWriter');
        $this->assertStringStartsWith('<?php', $result);
        $this->assertStringContainsString('auto-written by TestWriter', $result);
        $this->assertStringContainsString('return [', $result);
        $this->assertStringContainsString("'a' => 1", $result);
    }

    public function test_pretty_array_with_object()
    {
        $data = ['a' => new \stdClass];
        $result = $this->prettyArray($data);
        $this->assertStringContainsString("'a' => null", $result);
    }
}
