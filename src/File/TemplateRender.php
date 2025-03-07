<?php
namespace Neuralpin\File;

use Exception;
use Stringable;

class TemplateRender implements Stringable
{
    public function __construct(
        public string $filepath,
        public array $context = []
    ) {
        if (!file_exists($this->filepath)) {
            throw new Exception("File not found: {$this->filepath}");
        }
    }

    public function render()
    {
        ob_start();
        extract($this->context);
        require $this->filepath;

        return ob_get_clean();
    }

    public function __toString(): string
    {
        return (string) $this->render();
    }
}