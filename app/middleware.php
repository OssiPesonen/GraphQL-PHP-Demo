<?php
declare(strict_types=1);

use Slim\App;
use App\Application\Middleware\JsonBodyParserMiddleware;

return function (App $app) {
    $app->add(JsonBodyParserMiddleware::class);
};
