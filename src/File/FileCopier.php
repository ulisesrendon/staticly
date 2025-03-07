<?php

namespace Neuralpin\File;

use Exception;

class FileCopier
{
    private string $sourceDir;

    private string $destinyDir;

    public function __construct(string $sourceDir, string $destinyDir)
    {
        $this->sourceDir = rtrim($sourceDir, '/\\').DIRECTORY_SEPARATOR;
        $this->destinyDir = rtrim($destinyDir, '/\\').DIRECTORY_SEPARATOR;
    }

    public function copyFiles(?callable $callback = null)
    {
        if (! is_dir($this->sourceDir)) {
            throw new Exception("Source directory does not exist: {$this->sourceDir}");
        }

        if (! is_dir($this->destinyDir)) {
            if (! mkdir($this->destinyDir, 0777, true)) {
                throw new Exception("Failed to create target directory: {$this->destinyDir}");
            }
        }

        $this->copyDirectory($this->sourceDir, $this->destinyDir, $callback);
    }

    private function copyDirectory(string $source, string $target, ?callable $callback = null)
    {
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $src = $source.$file;
            $dst = $target.$file;

            if (is_dir($src)) {
                if (! is_dir($dst)) {
                    mkdir($dst);
                }
                $this->copyDirectory($src.DIRECTORY_SEPARATOR, $dst.DIRECTORY_SEPARATOR);
            } else {
                if ($callback === null || call_user_func($callback, $src, $dst)) { // Check callback
                    if (! copy($src, $dst)) {
                        throw new Exception('Failed to copy file: '.$file);
                    }
                }
            }
        }
    }
}
