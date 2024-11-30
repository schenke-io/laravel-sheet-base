<?php

namespace SchenkeIo\LaravelSheetBase\Tests\Feature\Elements;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use SchenkeIo\LaravelSheetBase\Elements\ColumnType;
use SchenkeIo\LaravelSheetBase\Elements\PipelineType;
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaAddColumnException;
use SchenkeIo\LaravelSheetBase\Exceptions\SchemaVerifyColumnsException;

class SheetBaseSchemaTest extends TestCase
{
    // todo test exception when 2x id is defined
    public static function dataProviderSchemas(): array
    {
        return [
            'correct' => ['', '$this->addId();$this->addString("name");'],
            'no id 1' => ['', '$this->addString("name");'], // ok for single column
            'no id 2' => [SchemaAddColumnException::class, '$this->addString("name1");$this->addString("name1");'], // 2 columns need one id
            'only id' => ['', '$this->addId();'],
            'id not first' => [SchemaAddColumnException::class, '$this->addString("name");$this->addId();'],
            'no column' => [SchemaVerifyColumnsException::class, ''],
            'two id 1' => [SchemaAddColumnException::class, '$this->addId("a");$this->addId("b");'],
            'two id 2' => [SchemaAddColumnException::class, '$this->addId();$this->addDot();'],
            '2 same names' => [SchemaAddColumnException::class, '$this->addId();$this->addString("id");'],
            '2 columns no id' => [SchemaVerifyColumnsException::class, '$this->addString("a");$this->addString("b");'],
            'wrong language column name' => [SchemaAddColumnException::class, '$this->addId();$this->addLanguage("very long name");'],
            'empty column name' => [SchemaAddColumnException::class, '$this->addId();$this->addString("");'],
            'valid lang tree' => ['', '$this->addDot();$this->addLanguage("de");'],
            'lang tree not only language' => [SchemaVerifyColumnsException::class, '$this->addDot();$this->addLanguage("de");$this->addString("x");'],
            'lang tree language but no dot' => [SchemaVerifyColumnsException::class, '$this->addId();$this->addLanguage("de");'],
        ];
    }

    protected function getClass(string $code): SheetBaseSchema
    {
        return eval("
return (new class() extends \SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema {
    public function define():void{ $code  }
});");
    }

    #[DataProvider('dataProviderSchemas')]
    /**
     * @throws SchemaVerifyColumnsException
     */
    public function test_schemas(string $exception, string $php): void
    {
        if ($exception != '') {
            $this->expectException($exception);
        }
        $testClass = $this->getClass($php);
        $this->assertInstanceOf(SheetBaseSchema::class, $testClass);

        $testClass->verify('test');
    }

    public function test_get_functions()
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
            if ($case == ColumnType::ID) {
                continue;
            }
            if ($case == ColumnType::Dot) {
                continue;
            }
            if ($case == ColumnType::Language) {
                continue;
            }
            if ($case == ColumnType::Closure) {
                continue;
            }
            $return[$case->name] = [$case];
        }

        return $return;
    }

    #[DataProvider('DataProviderColumnTypes')]
    /**
     * @return void
     */
    public function test_add_column_non_language_or_closure_without_exception(ColumnType $columnType)
    {
        $this->expectNotToPerformAssertions();
        $this->getClass('$this->addId();$this->add'.$columnType->value.'("a");');
    }

    public function test_add_column_language()
    {
        $this->expectNotToPerformAssertions();
        $this->getClass("\$this->addDot();\$this->addLanguage('de');");
    }

    public function test_add_column_closure_null()
    {
        $this->expectNotToPerformAssertions();
        $this->getClass("\$this->addId();\$this->addClosure('de');");
    }

    public function test_add_column_closure()
    {
        $this->expectNotToPerformAssertions();
        new class extends SheetBaseSchema
        {
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
