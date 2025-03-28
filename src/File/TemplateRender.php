<?php

declare(strict_types=1);

namespace Neuralpin\File;

use Exception;
use Stringable;

class TemplateRender implements Stringable
{
    /**
     * @param string $filepath
     * @param mixed[] $context
     * @throws \Exception
     */
    public function __construct(
        public string $filepath,
        public array $context = []
    ) {
        if (! file_exists($this->filepath)) {
            throw new Exception("File not found: {$this->filepath}");
        }
    }

    public function render(): bool|string
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
