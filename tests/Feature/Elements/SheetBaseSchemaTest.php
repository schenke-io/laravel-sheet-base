<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
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
            'two id 1' => [SchemaDefinitionException::class, '$this->addId("a");$this->addId("b");'],
            'two id 2' => [SchemaDefinitionException::class, '$this->addId();$this->addDot();'],
            '2 same names' => [SchemaDefinitionException::class, '$this->addId();$this->addString("id");'],
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
        $this->assertCount(2, $testClass->getColumns());
        $this->isInstanceOf(PipelineType::class, $testClass->getPipelineType());
        $this->assertEquals('id', $testClass->getIdName());
    }

    public static function dataProviderColumnTypes(): array
    {
        $return = [];
        foreach (ColumnType::cases() as $case) {
            if ($case == ColumnType::ID) continue;
            if ($case == ColumnType::Dot) continue;
            if ($case == ColumnType::Language) continue;
            if ($case == ColumnType::Closure) continue;
            $return[$case->name] = [$case];
        }
        return $return;
    }

    /**
     * @dataProvider dataProviderColumnTypes()
     * @return void
     */
    public function testAddColumnNonLanguageOrClosureWithoutException(ColumnType $columnType)
    {
        $this->expectNotToPerformAssertions();
        $this->getClass("\$this->addId();\$this->add" . $columnType->value . '("a");');
    }

    public function testAddColumnLanguage()
    {
        $this->expectNotToPerformAssertions();
        $this->getClass("\$this->addDot();\$this->addLanguage('de');");
    }

    public function testAddColumnClosureNull()
    {
        $this->expectNotToPerformAssertions();
        $this->getClass("\$this->addId();\$this->addClosure('de');");
    }

    public function testAddColumnClosure()
    {
        $this->expectNotToPerformAssertions();
        new class() extends SheetBaseSchema {
            /**
             * define the schema in Laravel migration syntax
             */
            protected function define(): void
            {
                $this->addId();
                $this->addClosure('test', function (mixed $param, array $row) {
                    return $param;
                });
            }
        };
    }
}
