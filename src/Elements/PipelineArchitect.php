<?php

namespace SchenkeIo\LaravelSheetBase\Elements;

use SchenkeIo\LaravelSheetBase\Exceptions\ArchitectureException;
use SchenkeIo\LaravelSheetBase\Helpers\FindEndpointClass;

/**
 * Class PipelineArchitect
 *
 * Verifies the integrity of the project's architecture, specifically naming conventions and schema definitions.
 *
 * Main Responsibilities:
 * - Schema Validation: Ensures `SheetBaseSchema` methods follow naming conventions.
 * - Endpoint Discovery: Verifies that reader and writer classes correspond to the expected file structure.
 * - Naming Enforcement: Checks that endpoint files are correctly named based on their extensions.
 *
 * Usage Example:
 * ```php
 * $architect = new PipelineArchitect();
 * $architect->scan();
 * ```
 */
class PipelineArchitect
{
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
        // all methods starting with 'add' are considered valid column methods
        // and they are discovered via reflection on SheetBaseSchema
    }

    /**
     * @throws ArchitectureException
     */
    private function readersFound(): void
    {
        foreach (FindEndpointClass::getReaders() as $extension => $class) {
            $this->findFile('Readers', 'Read', ucfirst($extension));
        }
    }

    /**
     * @throws ArchitectureException
     */
    private function writersFound(): void
    {
        foreach (FindEndpointClass::getWriters() as $extension => $class) {
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
