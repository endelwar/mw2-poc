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

$injector = new \Auryn\Injector();

$injector->alias('Http\Request', 'Http\HttpRequest');
$injector->share('Http\HttpRequest');
$injector->define('Http\HttpRequest', array(
    ':get' => $_GET,
    ':post' => $_POST,
    ':cookies' => $_COOKIE,
    ':files' => $_FILES,
    ':server' => $_SERVER,
));

$injector->alias('Http\Response', 'Http\HttpResponse');
$injector->share('Http\HttpResponse');

$injector->delegate('Twig_Environment', function () use ($injector) {
    $loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/app/templates');
    $twigConfig = array(
        'debug' => true,
    );
    $twig = new Twig_Environment($loader, $twigConfig);

    $twig->addExtension(new Twig_Extension_Debug());

    $twig->addExtension(new Twig_Extensions_Extension_I18n());

    return $twig;
});
$injector->alias('MailWatch\Template\Renderer', 'MailWatch\Template\TwigRenderer');

if (isset($_ENV['db_host'])) {
    $dbCconfig = new \Doctrine\DBAL\Configuration();
    $dbConnectionParams = array(
        'dbname' => $_ENV['db_name'],
        'user' => $_ENV['db_username'],
        'password' => $_ENV['db_password'],
        'host' => $_ENV['db_host'],
        'driver' => $_ENV['db_driver'],
    );
    $db = \Doctrine\DBAL\DriverManager::getConnection($dbConnectionParams, $dbCconfig);
    $injector->share($db);
}

return $injector;
