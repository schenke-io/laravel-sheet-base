# Sheet Base Schema

This skill explains how to define the schema for your pipeline's target.

## Usage

A schema defines the columns of the output file. To create a schema, extend `SheetBaseSchema` and implement the `define()` method.

<code-snippet name="Schema Definition" lang="php">
use SchenkeIo\LaravelSheetBase\Elements\SheetBaseSchema;

class MySchema extends SheetBaseSchema
{
    protected function define(): void
    {
        $this->addId();
        $this->addString('name');
        $this->addBool('is_active');
    }
}
</code-snippet>

## Available Methods

Use these methods within `define()` to add columns:

- `addId(string $name = 'id')`: Standard numeric or string ID.
- `addDot(string $name = 'id')`: Dot notation ID (e.g., `auth.login`).
- `addString(string $name)`: Non-nullable string.
- `addNullString(string $name)`: Nullable string.
- `addUnsigned(string $name)`: Unsigned integer (nullable).
- `addUnsignedNotNull(string $name)`: Unsigned integer (non-nullable).
- `addFloat(string $name)`: Floating point number.
- `addBool(string $name)`: Boolean.
- `addDateTime(string $name)`: DateTime string.
- `addLanguage(string $name)`: Language column (name must be 2-char code).
- `addClosure(string $name, Closure $closure)`: Custom calculated column.

## Guidelines
- **Column Naming:** Use `snake_case` for all column names.
- **Language schemas:** When ANY `addLanguage` column is present, the ID must be `addDot` and every non-ID column must also be `addLanguage`. Mixed schemas (e.g. `addId` + `addLanguage`) are rejected at runtime.

## Examples

### Standard Table
<code-snippet name="Standard Table Schema" lang="php">
protected function define(): void
{
    $this->addId();
    $this->addString('first_name');
    $this->addString('last_name');
    $this->addBool('is_verified');
}
</code-snippet>

### Translation-style (Language Files)
<code-snippet name="Translation Schema" lang="php">
protected function define(): void
{
    $this->addDot('key');
    $this->addLanguage('en');
    $this->addLanguage('de');
    $this->addLanguage('fr');
}
</code-snippet>

## Pitfalls
- **Only one ID column allowed:** You cannot use both `addId()` and `addDot()` in the same schema.
- **ID must be first:** The ID column (`addId` or `addDot`) must be the first column defined.
- **Language columns require `addDot`:** If you add any `addLanguage` column, the ID must be `addDot` and every other column must also be `addLanguage`. Mixing language columns with regular data columns is not allowed.
- **Language column name:** Must be exactly 2 characters (e.g., `'en'`, `'de'`).
- **`define()` is `protected`:** Declaring it `public` causes no error but is incorrect; the method is called only from the constructor.
