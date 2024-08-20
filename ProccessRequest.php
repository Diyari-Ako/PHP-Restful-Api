<?php

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriSegments = explode('/', $uri);
$part_1 = $uriSegments[3] ?? null;
$part_2  =  $uriSegments[4] ?? null;

if ($uriSegments[1] === 'api') {

    switch ($uriSegments[2]) {

        case 'users':
            (new Gateway\UserGateway($database))->processEndpoint($method, $part_1, $database);
            break;

        case 'posts':
            (new Gateway\PostGateway($database))->processEndpoint($method, $part_1, $part_2, $database);
            break;

        default:
            header("HTTP/1.0 404 Not Found");
            echo json_encode("Invalid API endpoint.");
            break;
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo json_encode("Invalid request.");
}
