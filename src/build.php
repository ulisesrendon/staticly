<?php

use Neuralpin\File\DirectoryCleaner;
use Neuralpin\File\FileCopier;
use Neuralpin\File\FileCreator;
use Neuralpin\File\TemplateRender;

require __DIR__.'/../vendor/autoload.php';

$sourceDir = __DIR__.'/../content';
$destinyDir = __DIR__.'/../public';

if (is_dir($destinyDir)) {
    try {
        new DirectoryCleaner($destinyDir)->deleteFiles();
        file_put_contents('php://output', 'Cleaning output dir...'.PHP_EOL);
    } catch (\Exception $e) {
        file_put_contents('php://output', 'Error trying to clean dir'.PHP_EOL);
        exit();
    }
}

try {
    $Copier = new FileCopier($sourceDir, $destinyDir);
    $Copier->copyFiles(function ($sourceFile, $destinyFile) {
        $extension = pathinfo($sourceFile, PATHINFO_EXTENSION);
        if ($extension === 'php') {
            file_put_contents('php://output', "Processing PHP file: $sourceFile".PHP_EOL);
            $renderedContent = new TemplateRender($sourceFile)->render();
        } elseif ($extension === 'md') {
            file_put_contents('php://output', "Processing MD file: $sourceFile".PHP_EOL);
            $renderedContent = new Parsedown()->text(file_get_contents($sourceFile));
        }

        if ($extension === 'php' || $extension === 'md') {
            $destinyPathInfo = pathinfo($destinyFile);
            new FileCreator("{$destinyPathInfo['dirname']}/{$destinyPathInfo['filename']}.html")->putContents($renderedContent);
            return false;
        }else if($extension === 'gitkeep'){
            return false;
        }

        return true;
    });

    file_put_contents('php://output', 'Files rendered successfully'.PHP_EOL);
} catch (Exception $e) {
    file_put_contents('php://output', "Error: {$e->getMessage()}".PHP_EOL);
}
