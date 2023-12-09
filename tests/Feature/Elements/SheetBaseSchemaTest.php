<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\PipelineType;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaDefinitionException;

class SheetBaseSchemaTest extends TestCase
{
    public static function dataProviderSchemas(): array
    {
        return [
            'correct' => ['', '$this->addId();$this->addString("name");'],
            'no id' => [SchemaDefinitionException::class, '$this->addString("name");'],
            'only id' => [SchemaDefinitionException::class, '$this->addId();'],
            'no column' => [SchemaDefinitionException::class, ''],
            'wrong column type' => [SchemaDefinitionException::class, '$this->fullUnknownColumnTypeGiven();'],
            'two id 1' => [SchemaDefinitionException::class, '$this->addId("a");$this->addId("b");'],
            'two id 2' => [SchemaDefinitionException::class, '$this->addId();$this->addDot();'],
            '2 same names' => [SchemaDefinitionException::class, '$this->addId();$this->string("id");'],
            'unknown type' => [SchemaDefinitionException::class, '$this->addId();$this->addSomethingVeryCrazy();'],
            'wrong language column name' => [SchemaDefinitionException::class, '$this->addId();$this->addLanguage("very long name");'],
            'empty column name' => [SchemaDefinitionException::class, '$this->addId();$this->addString("");'],
            'valid lang tree' => ['', '$this->addDot();$this->addLanguage("de");'],
            'lang tree not only language' => [SchemaDefinitionException::class, '$this->addDot();$this->addLanguage("de");$this->addString("x");'],
            'lang tree language but no dot' => [SchemaDefinitionException::class, '$this->addId();$this->addLanguage("de");'],
        ];
    }

    protected function getClass(string $code): SheetBaseSchema
    {
        return eval("
return (new class() extends \SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema {
    public function define():void{ $code  }
});");
    }

    /**
     * @dataProvider dataProviderSchemas
     *
     * @throws SchemaDefinitionException
     */
    public function testSchemas(string $exception, string $php): void
    {
        if ($exception != '') {
            $this->expectException($exception);
        }
        $testClass = $this->getClass($php);
        $this->assertInstanceOf(SheetBaseSchema::class, $testClass);

        $testClass->verify();
    }

    public function testGetFunctions()
    {
        $testClass = $this->getClass('$this->addId();$this->addString("name");');
        $this->assertEquals(2, count($testClass->getColumns()));
        $this->isInstanceOf(PipelineType::class, $testClass->getPipelineType());
        $this->assertEquals('id', $testClass->getIdName());
    }
}
