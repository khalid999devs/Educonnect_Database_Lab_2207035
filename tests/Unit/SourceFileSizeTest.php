<?php

namespace Tests\Unit;

use FilesystemIterator;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class SourceFileSizeTest extends TestCase
{
    private const MAX_LINES = 600;

    public function test_project_source_files_stay_within_the_line_limit(): void
    {
        $projectRoot = dirname(__DIR__, 2);

        foreach ($this->sourceFiles() as $file) {
            $contents = file_get_contents($file->getPathname());
            $lineCount = $contents === '' ? 0 : substr_count($contents, "\n") + 1;
            $relativePath = str_replace($projectRoot.DIRECTORY_SEPARATOR, '', $file->getPathname());

            $this->assertLessThanOrEqual(
                self::MAX_LINES,
                $lineCount,
                "{$relativePath} contains {$lineCount} lines.",
            );
        }
    }

    /** @return list<SplFileInfo> */
    private function sourceFiles(): array
    {
        $files = [];
        $projectRoot = dirname(__DIR__, 2);
        $directories = ['app', 'routes', 'resources', 'tests', 'database/oracle', 'public/assets'];
        $extensions = ['php', 'css', 'js', 'sql'];

        foreach ($directories as $directory) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($projectRoot.DIRECTORY_SEPARATOR.$directory, FilesystemIterator::SKIP_DOTS),
            );

            foreach ($iterator as $file) {
                if ($file->isFile() && in_array($file->getExtension(), $extensions, true)) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }
}
