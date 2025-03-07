<?php

namespace Neuralpin\File;

class FileCreator
{
    public function __construct(public string $filePath) {}

    public function putContents(mixed $data, int $flags = 0)
    {
        $dirPathOnly = dirname($this->filePath);
        if (! is_dir($dirPathOnly)) {
            mkdir($dirPathOnly, 0775, true);
        }
        file_put_contents($this->filePath, $data, $flags);
    }
}
