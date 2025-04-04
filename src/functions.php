<?php

declare(strict_types=1);

namespace Neuralpin;

function consoleLog(string $text): void
{
    file_put_contents('php://output', $text.PHP_EOL);
}

function extractMainTitle(string $markdown): string
{
    if (preg_match('/#\s+(.+)/', $markdown, $matches)) {
        return trim($matches[1]);
    }

    return '';
}
