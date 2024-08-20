<?php

require (__DIR__) . "/vendor/autoload.php";

set_error_handler([ResponseHandling\ErrorHandler::class, 'handleError']);
set_exception_handler([ResponseHandling\ErrorHandler::class, 'handleException']);

header("Content-type: application/json; charset=UTF-8");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$database = new Config\Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);
