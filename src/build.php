<?php

use Neuralpin\File\DirectoryCleaner;
use Neuralpin\File\FileCopier;
use Neuralpin\File\FileCreator;
use Neuralpin\File\TemplateRender;
use function Neuralpin\consoleLog;
use function Neuralpin\extractMainTitle;

require __DIR__.'/../vendor/autoload.php';

define('CONFIG', (array) require __DIR__.'/config.php');

if (is_dir(CONFIG['destinyDir'])) {
    try {
        new DirectoryCleaner(CONFIG['destinyDir'])->deleteFiles();
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
        $destinyPathInfo = pathinfo($destinyFile);

        $title = '';

        if ($extension === 'php') {
            consoleLog( "Processing PHP file: $sourceFile");
            $renderedContent = new TemplateRender($sourceFile)->render();
        } elseif ($extension === 'md') {
            consoleLog( "Processing MD file: $sourceFile");
            $markdownContent =  file_get_contents($sourceFile);
            $renderedContent = new Parsedown()->text($markdownContent);
            $title = extractMainTitle($markdownContent);
        }

        if (strpos($destinyPathInfo['filename'], '__') !== false) {
            $template = CONFIG['templates'][explode('__', $destinyPathInfo['filename'])[1]] ?? CONFIG['templates']['page'];

            $renderedContent = new TemplateRender($template, [
                'title' => $title,
                'content' => $renderedContent,
            ])->render();

            $destinyPathInfo['filename'] = preg_replace('/__([a-zA-Z0-9]+)/', '', $destinyPathInfo['filename']);
        }

        if ($extension === 'php' || $extension === 'md') {
            new FileCreator("{$destinyPathInfo['dirname']}/{$destinyPathInfo['filename']}.html")->putContents($renderedContent);
            return false;
        }else if($extension === 'gitkeep'){
            return false;
        }

        return true;
    });

    consoleLog( 'Files rendered successfully');
} catch (Exception $e) {
    consoleLog( "Error: {$e->getMessage()}");
}
