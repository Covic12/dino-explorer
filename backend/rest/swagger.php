<?php
require '../vendor/autoload.php';

header('Content-Type: application/json');

$openapi = \OpenApi\scan([
    __DIR__,               // backend/rest (openAPI.php)
    __DIR__ . '/..',       // backend (index.php with route annotations)
    __DIR__ . '/../src',   // services/dao if you ever put annotations there
]);

echo $openapi->toJson();
