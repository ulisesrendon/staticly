<?php

namespace Neuralpin\File;

use Exception;

class DirectoryCleaner
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = rtrim($directory, '/\\').DIRECTORY_SEPARATOR;
    }

    public function deleteFiles()
    {
        if (! is_dir($this->directory)) {
            throw new Exception("Directory does not exist: {$this->directory}");
        }

        $files = scandir($this->directory);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = "{$this->directory}{$file}";
            if (is_file($filePath)) {
                if (! unlink($filePath)) {
                    throw new Exception("Failed to delete file: {$file}");
                }
            } elseif (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            }
        }
    }

    private function deleteDirectory($dir)
    {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $dir.DIRECTORY_SEPARATOR.$file;
            if (is_file($filePath)) {
                unlink($filePath);
            } elseif (is_dir($filePath)) {
                $this->deleteDirectory($filePath);
            }
        }
        rmdir($dir);
    }
}
