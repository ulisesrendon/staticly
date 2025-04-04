<?php

declare(strict_types=1);

use Neuralpin\File\DirectoryCleaner;
use Neuralpin\File\FileCopier;
use Neuralpin\File\FileCreator;
use Neuralpin\File\TemplateRender;
use function Neuralpin\consoleLog;
use function Neuralpin\extractMainTitle;

require __DIR__.'/../vendor/autoload.php';

define('CONFIG', (array) require __DIR__.'/config.php');

if (is_dir((string) CONFIG['destinyDir'])) {
    try {
        new DirectoryCleaner((string) CONFIG['destinyDir'])->deleteFiles();
        consoleLog('Cleaning output dir...');
    } catch (\Exception $e) {
        consoleLog('Error trying to clean dir');
        exit();
    }
}

try {
    $Copier = new FileCopier(CONFIG['sourceDir'], CONFIG['destinyDir']);
    $Copier->copyFiles(function ($sourceFile, $destinyFile)  {
        $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);

        $pathinfo = pathinfo($destinyFile);
        $filename = $pathinfo['filename'] ? $pathinfo['filename'] : '';
        $dirname = $pathinfo['dirname'] ?? '';

        $title = '';
        $renderedContent = '';

        if ($extension === 'php') {
            $renderedContent = new TemplateRender($sourceFile)->render();
        } elseif ($extension === 'md') {
            $markdownContent = (string) file_get_contents($sourceFile);
            $renderedContent = new Parsedown()->text($markdownContent);
            $title = extractMainTitle($markdownContent);
        }

        if (strpos($filename, '__') !== false) {
            $template = CONFIG['templates'][explode('__', $filename)[1]] ?? CONFIG['templates']['page'];

            $renderedContent = new TemplateRender($template, [
                'title' => $title,
                'content' => $renderedContent,
            ])->render();

            $filename = preg_replace('/__([a-zA-Z0-9]+)/', '', $filename);
        }

        if ($extension === 'php' || $extension === 'md') {
            new FileCreator("{$dirname}/{$filename}.html")->putContents($renderedContent);
            return false;
        }else if(in_array($extension, CONFIG['ignoreExtensions'])){
            return false;
        }

        return true;
    });

    consoleLog( 'Files rendered successfully');
} catch (Exception $e) {
    consoleLog( "Error: {$e->getMessage()}");
}
