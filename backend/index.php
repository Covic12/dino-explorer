<?php
require 'vendor/autoload.php';
require_once __DIR__ . '/rest/middleware/CorsMiddleware.php';
require_once __DIR__ . '/rest/middleware/AuthMiddleware.php';
require_once __DIR__ . '/rest/controllers/DinosaurController.php';
require_once __DIR__ . '/rest/controllers/EraController.php';
require_once __DIR__ . '/rest/controllers/LocationController.php';
require_once __DIR__ . '/rest/controllers/ResearcherController.php';
require_once __DIR__ . '/rest/controllers/AuthController.php';

CorsMiddleware::handle();

$dinosaurController = new DinosaurController();
$eraController = new EraController();
$locationController = new LocationController();
$researcherController = new ResearcherController();
$authController = new AuthController();

Flight::route('GET /api/docs', function() {
    $openapi = \OpenApi\Generator::scan([__DIR__ . '/rest/controllers']);
    header('Content-Type: application/json');
    echo $openapi->toJson();
});

Flight::route('GET /api/swagger', function() {
    $html = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dino Explorer API Documentation</title>
        <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui.css" />
    </head>
    <body>
        <div id="swagger-ui"></div>
        <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-bundle.js"></script>
        <script src="https://unpkg.com/swagger-ui-dist@5.9.0/swagger-ui-standalone-preset.js"></script>
        <script>
            window.onload = function() {
                SwaggerUIBundle({
                    url: "/dino-explorer/backend/api/docs",
                    dom_id: "#swagger-ui",
                    presets: [
                        SwaggerUIBundle.presets.apis,
                        SwaggerUIStandalonePreset
                    ],
                    layout: "StandaloneLayout"
                });
            };
        </script>
    </body>
    </html>';
    echo $html;
});

Flight::route('POST /api/auth/register', [$authController, 'register']);
Flight::route('POST /api/auth/login', [$authController, 'login']);

Flight::route('GET /api/dinosaurs', [$dinosaurController, 'getAll']);
Flight::route('GET /api/dinosaurs/@id', [$dinosaurController, 'getById']);
Flight::route('POST /api/dinosaurs', function() use ($dinosaurController) {
    AuthMiddleware::requireAuth();
    $dinosaurController->create();
});
Flight::route('PUT /api/dinosaurs/@id', function($id) use ($dinosaurController) {
    AuthMiddleware::requireAuth();
    $dinosaurController->update($id);
});
Flight::route('DELETE /api/dinosaurs/@id', function($id) use ($dinosaurController) {
    AuthMiddleware::requireAdmin();
    $dinosaurController->delete($id);
});

Flight::route('GET /api/eras', [$eraController, 'getAll']);
Flight::route('GET /api/eras/@id', [$eraController, 'getById']);
Flight::route('POST /api/eras', function() use ($eraController) {
    AuthMiddleware::requireAuth();
    $eraController->create();
});
Flight::route('PUT /api/eras/@id', function($id) use ($eraController) {
    AuthMiddleware::requireAuth();
    $eraController->update($id);
});
Flight::route('DELETE /api/eras/@id', function($id) use ($eraController) {
    AuthMiddleware::requireAdmin();
    $eraController->delete($id);
});

Flight::route('GET /api/locations', [$locationController, 'getAll']);
Flight::route('GET /api/locations/@id', [$locationController, 'getById']);
Flight::route('POST /api/locations', function() use ($locationController) {
    AuthMiddleware::requireAuth();
    $locationController->create();
});
Flight::route('PUT /api/locations/@id', function($id) use ($locationController) {
    AuthMiddleware::requireAuth();
    $locationController->update($id);
});
Flight::route('DELETE /api/locations/@id', function($id) use ($locationController) {
    AuthMiddleware::requireAdmin();
    $locationController->delete($id);
});

Flight::route('GET /api/researchers', [$researcherController, 'getAll']);
Flight::route('GET /api/researchers/@id', [$researcherController, 'getById']);
Flight::route('POST /api/researchers', function() use ($researcherController) {
    AuthMiddleware::requireAuth();
    $researcherController->create();
});
Flight::route('PUT /api/researchers/@id', function($id) use ($researcherController) {
    AuthMiddleware::requireAuth();
    $researcherController->update($id);
});
Flight::route('DELETE /api/researchers/@id', function($id) use ($researcherController) {
    AuthMiddleware::requireAdmin();
    $researcherController->delete($id);
});

Flight::start();