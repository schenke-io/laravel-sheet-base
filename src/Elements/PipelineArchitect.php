<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Exceptions\ArchitectureException;

class PipelineArchitect
{
    public const ColumnMethods = [
        'addUnsigned',
        'addUnsignedNotNull',
        'addFloat',
        'addBool',
        'addString',
        'addLanguage',
        'addDateTime',
        'addId',
        'addDot',
        'addClosure',
    ];

    public const ReadExtensions = ['neon', 'psv'];

    public const WriteExtensions = ['neon', 'json', 'php'];

    /**
     * @throws \Throwable
     */
    public function scan(): void
    {
        $this->columnMethodsDefined();
        $this->readersFound();
        $this->writersFound();
    }

    /**
     * @throws \Throwable
     */
    private function columnMethodsDefined(): void
    {
        $class = new \ReflectionClass(SheetBaseSchema::class);
        $methodsFound = [];
        foreach ($class->getMethods() as $method) {
            if (str_starts_with($method->name, 'add')) {
                $methodsFound[] = $method->name;
            }
        }

        foreach (self::ColumnMethods as $method) {
            throw_if(! in_array($method, $methodsFound), new ArchitectureException("method not found: $method"));
        }
    }

    /**
     * @throws \Throwable
     */
    private function readersFound(): void
    {
        foreach (self::ReadExtensions as $extension) {
            $this->findFile('Readers', 'Read', ucfirst($extension));
        }
    }

    /**
     * @throws \Throwable
     */
    private function writersFound(): void
    {
        foreach (self::WriteExtensions as $extension) {
            $this->findFile('Writers', 'Write', ucfirst($extension));
        }
    }

    /**
     * @throws \Throwable
     */
    private function findFile(string $directory, string $type, string $extension): void
    {
        $path = sprintf('%s/%s/Endpoint%s%s.php', __DIR__.'/../Endpoints', $directory, $type, $extension);
        throw_if(! file_exists($path), new ArchitectureException("$type-file not found for extension $extension in Endpoints/$directory"));
    }
}
