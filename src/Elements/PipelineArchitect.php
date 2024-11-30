<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Exceptions\ArchitectureException;

class PipelineArchitect
{
    public array $columnMethods = [
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

    public array $readExtensions = ['neon', 'psv', 'csv', 'tsv', 'txt', 'yaml', 'yml'];

    public array $writeExtensions = ['neon', 'json', 'php', 'csv', 'psv', 'tsv', 'txt', 'yaml'];

    /**
     * @throws ArchitectureException
     */
    public function scan(): void
    {
        $this->columnMethodsDefined();
        $this->readersFound();
        $this->writersFound();
    }

    /**
     * @throws ArchitectureException
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

        foreach ($this->columnMethods as $method) {
            if (! in_array($method, $methodsFound)) {
                throw new ArchitectureException("method not found: $method");
            }
        }
    }

    /**
     * @throws ArchitectureException
     */
    private function readersFound(): void
    {
        foreach ($this->readExtensions as $extension) {
            $this->findFile('Readers', 'Read', ucfirst($extension));
        }
    }

    /**
     * @throws ArchitectureException
     */
    private function writersFound(): void
    {
        foreach ($this->writeExtensions as $extension) {
            $this->findFile('Writers', 'Write', ucfirst($extension));
        }
    }

    /**
     * @throws ArchitectureException
     */
    private function findFile(string $directory, string $type, string $extension): void
    {
        $path = sprintf('%s/%s/Endpoint%s%s.php', __DIR__.'/../Endpoints', $directory, $type, $extension);
        if (! file_exists($path)) {
            throw new ArchitectureException("$type-file not found for extension $extension in Endpoints/$directory");
        }
    }
}
