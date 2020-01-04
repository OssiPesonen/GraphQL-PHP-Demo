<?php
declare(strict_types=1);

use App\Application\Controllers\GraphQLController;
use Slim\App;

return function (App $app) {
    $app->any('/', GraphQLController::class . ':index');
};
