# HTTP Router

HTTP Routing System for microservices, serverless and vanilla custom apps

```php
require __DIR__.'/../vendor/autoload.php';

use Neuralpin\HTTPRouter\Demo\DemoController;
use Neuralpin\HTTPRouter\Response;
use Neuralpin\HTTPRouter\Router;

$Router = new Router;

// Static and Dynamic HTML Routes
$Router->any('/', fn () => 'Hello world!');
$Router->get('/page', fn () => Response::template(__DIR__.'/template/hello-page.html'));
$Router->get('/page2', [DemoController::class, 'templating']);

// APi Like Routes
$Router->get('/api/product', [DemoController::class, 'list']);
$Router->post('/api/product', [DemoController::class, 'create']);
$Router->get('/api/product/:id', [DemoController::class, 'get']);
$Router->patch('/api/product/:id', [DemoController::class, 'update']);
$Router->delete('/api/product/:id', [DemoController::class, 'delete']);

// Match URI when it starts with /search and bind as param anything before /search
$Router->get('/search:search', function ($search) {

    $search = explode('/', htmlspecialchars($search));
    $search = implode(' ', $search);

    return "Searching: $search";
})->ignoreParamSlash();

try {

    $Controller = $Router->getController();

} catch (\Exception $Exception) {
    if ($Router->isNotFoundException($Exception)) {
        $Controller = $Router->wrapController(
            fn () => Response::template(content: __DIR__.'/template/404.html', status: 404)
        );
    } elseif ($Router->isMethodNotAllowedException($Exception)) {
        $Controller = $Router->wrapController(
            fn () => Response::template(content: __DIR__.'/template/405.html', status: 405)
        );
    } else {
        $Controller = $Router->wrapController(
            fn () => Response::template(content: __DIR__.'/template/500.html', status: 500)
        );
    }
}

echo $Controller->getResponse();
```

## Use this module with composer

```shell
composer config repositories.neuralpin/httprouter vcs https://github.com/neuralpin/httprouter
composer require neuralpin/httprouter
```

### Check yourself with the demo example dir inside this repo

Run PHP server with the next command in your console

```shell
php -S localhost:8001 <PathToRepoSource>/httprouter/demo/index.php
```
