

## Supported Readers

- File readers
- Array readers
- Collection readers
- Eloquent model readers
- Cloud file readers
- Google Sheets readers


## Supported Writers

The package includes versatile writers capable of handling various file formats:

- typical data file formats (JSON, Neon, YAML)
- PHP (config) files (suitable for tests, configuration files, or Laravel Sushi)
- Laravel language php-files

Additionally, all written files that support comments include
remarks about the reference file, discouraging direct
editing.

## Schema building blocks

Here are the three basic building blocks for creating a schema:
+ **ID-based tables:** These tables have many columns and a single ID field in the first column.
+ **Language files:** These files use a key in dot notation and have separate columns for each language code.
+ **Simple sources:** These are single-column sources that lack a key column, the ID is generated automatically.


## Pipeline Pumping Process

Here's the breakdown of how a pipeline pumps data:

+ **Read data:** Each line of data is read into a "dataRow".
+ **Assign key:** The dataRow gets its key either from an "ID" column or a numeric sequence.
+ **New key:** If the key is new, a new "target dataRow" is created for that ID, with all its columns filled with null values.
+ **Fill data:** For each key required by the schema, its value is searched for in the source data. If found, it's added to the corresponding column of the target dataRow.
+ **Repeat:** This process repeats for each source dataRow, even if the same ID appears multiple times.
+ **Write output:** Once all sources are read, the entire dataset is written from memory to a target, using the appropriate format function for each column.

**Pipeline execution:**
+ Pipelines are executed in the alphabetic order of the pipeline names.
+ Important points:
    + **No warning for duplicate IDs:** Sources can have duplicate IDs, but the final result only allows unique IDs.
    + **Optional data in sources:** Sources might not have all the data defined in the Schema.
    + **Pipelines can be chained:** One pipeline's output can be another's input.
