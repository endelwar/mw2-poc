<?php
/**
 * MailWatch2
 * Copyright (C) 2015 Manuel Dalla Lana <endelwar@aregar.it>
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program.
 * If not, see <http://www.gnu.org/licenses/>.
 */

namespace MailWatch;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use Http\HttpRequest;
use Http\HttpResponse;
use josegonzalez\Dotenv\Loader;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$environment = 'development';

/**
 * Register the error handler
 */
$woops = new Run;
if ($environment !== 'production') {
    $woops->pushHandler(new PrettyPageHandler);
} else {
    $woops->pushHandler(function ($e) {
        echo 'Friendly error page and send an email to the developer, if autorized by sysadmin';
    });
}
$woops->register();

if (is_file($env = __DIR__ . '/../.env')) {
    $env = new Loader($env);
    $env->parse()
        ->expect(
            'MAILWATCH_DB_HOST',
            'MAILWATCH_DB_PORT',
            'MAILWATCH_DB_NAME',
            'MAILWATCH_DB_USERNAME',
            'MAILWATCH_DB_PASSWORD'
        )
        ->toEnv();
}

$injector = require __DIR__ . '/Dependencies.php';

/** @var HttpRequest $request */
$request = $injector->make(HttpRequest::class);
/** @var HttpResponse $response */
$response = $injector->make(HttpResponse::class);

//check if .env file is present
if (!isset($_ENV['MAILWATCH_DB_HOST'])) {
    /** @var \MailWatch\Controller\Requirements $requirements */
    $requirements = $injector->make(Controller\Requirements::class);
    $requirements->checkRequirements();
} else {
    $routeDefinitionCallback = function (RouteCollector $r) {
        $routes = include __DIR__ . '/Routes.php';
        foreach ($routes as $route) {
            $r->addRoute($route[0], $route[1], $route[2]);
        }
    };
    $dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback);

    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getPath());
    switch ($routeInfo[0]) {
        case Dispatcher::NOT_FOUND:
            $response->setContent('404 - Page not found');
            $response->setStatusCode(404);
            break;
        case Dispatcher::METHOD_NOT_ALLOWED:
            $response->setContent('405 - Method not allowed');
            $response->setStatusCode(405);
            break;
        case Dispatcher::FOUND:
            $className = $routeInfo[1][0];
            $method = $routeInfo[1][1];
            $vars = $routeInfo[2];

            $class = $injector->make($className);
            $class->$method($vars);
            break;
    }
}

foreach ($response->getHeaders() as $header) {
    header($header, false);
}

echo $response->getContent();
