<?php

use Neuralpin\File\TemplateRender;

echo new TemplateRender(__DIR__.'/sections/header.php', ['title' => $title]);
echo new TemplateRender(__DIR__.'/sections/article.php', ['content' => $content]);
echo new TemplateRender(__DIR__.'/sections/footer.php');
