<?php

return [
    'sourceDir' => __DIR__.'/../content',
    'destinyDir' => __DIR__.'/../public',
    'templates' => [
        'article' => __DIR__.'/../templates/article.php',
        'page' => __DIR__.'/../templates/page.php',
    ],
    'ignoreExtensions' => [
        'gitkeep',
        'draft',
        'content',
    ],
];
