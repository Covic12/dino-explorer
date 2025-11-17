<?php
require '../vendor/autoload.php';

require_once __DIR__ . '/../src/services/DinosaursService.php';
require_once __DIR__ . '/../src/services/EraService.php';
require_once __DIR__ . '/../src/services/LocationService.php';
require_once __DIR__ . '/../src/services/ResearcherService.php';
require_once __DIR__ . '/../src/services/UserService.php';

use OpenApi\Annotations as OA;

Flight::register('dinosaurService', 'DinosaursService');
Flight::register('eraService', 'EraService');
Flight::register('locationService', 'LocationService');
Flight::register('researcherService', 'ResearcherService');
Flight::register('userService', 'UserService');

/**
 * Simple HTML page (presentation layer) – Dinosaurs list
 */
Flight::route('GET /dinosaurs', function () {
    $service   = Flight::dinosaurService();
    $dinosaurs = $service->getAll();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Dinosaurs</title>
        <meta charset="UTF-8">
    </head>
    <body>
        <h1>Dinosaurs list</h1>
        <ul>
            <?php foreach ($dinosaurs as $d): ?>
                <li>
                    <strong><?= htmlspecialchars($d['name']) ?></strong>
                    (<?= htmlspecialchars($d['period']) ?>,
                    <?= htmlspecialchars($d['location']) ?>)
                </li>
            <?php endforeach; ?>
        </ul>
    </body>
    </html>
    <?php
});

/**
 * ===================== DINOSAURS API =====================
 */

/**
 * @OA\Get(
 *     path="/api/dinosaurs",
 *     summary="Get all dinosaurs",
 *     @OA\Response(
 *         response=200,
 *         description="List of dinosaurs",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="period", type="string"),
 *                 @OA\Property(property="location", type="string")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /api/dinosaurs', function () {
    $service = Flight::dinosaurService();
    Flight::json($service->getAll());
});

/**
 * @OA\Get(
 *     path="/api/dinosaurs/{id}",
 *     summary="Get dinosaur by id",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Dinosaur found"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid id"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dinosaur not found"
 *     )
 * )
 */
Flight::route('GET /api/dinosaurs/@id', function ($id) {
    $service = Flight::dinosaurService();
    try {
        $dino = $service->getById($id);
        Flight::json($dino);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Post(
 *     path="/api/dinosaurs",
 *     summary="Create dinosaur",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","period","location"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="period", type="string"),
 *             @OA\Property(property="location", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Dinosaur created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /api/dinosaurs', function () {
    $service = Flight::dinosaurService();
    $data    = Flight::request()->data->getData();

    try {
        $created = $service->create($data);
        Flight::json($created, 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/api/dinosaurs/{id}",
 *     summary="Update dinosaur",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="period", type="string"),
 *             @OA\Property(property="location", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated dinosaur"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dinosaur not found"
 *     )
 * )
 */
Flight::route('PUT /api/dinosaurs/@id', function ($id) {
    $service = Flight::dinosaurService();
    $data    = Flight::request()->data->getData();

    try {
        $updated = $service->update($id, $data);
        Flight::json($updated);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Delete(
 *     path="/api/dinosaurs/{id}",
 *     summary="Delete dinosaur",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Deleted successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dinosaur not found"
 *     )
 * )
 */
Flight::route('DELETE /api/dinosaurs/@id', function ($id) {
    $service = Flight::dinosaurService();
    try {
        $service->delete($id);
        Flight::json(['message' => 'Deleted'], 204);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * ===================== ERAS API =====================
 */

/**
 * @OA\Get(
 *     path="/api/eras",
 *     summary="Get all eras",
 *     @OA\Response(
 *         response=200,
 *         description="List of eras",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="period", type="string"),
 *                 @OA\Property(property="description", type="string")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /api/eras', function () {
    $service = Flight::eraService();
    Flight::json($service->getAll());
});

/**
 * @OA\Get(
 *     path="/api/eras/{id}",
 *     summary="Get era by id",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Era found"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid id"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Era not found"
 *     )
 * )
 */
Flight::route('GET /api/eras/@id', function ($id) {
    $service = Flight::eraService();
    try {
        $era = $service->getById($id);
        Flight::json($era);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Post(
 *     path="/api/eras",
 *     summary="Create era",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","period","description"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="period", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Era created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /api/eras', function () {
    $service = Flight::eraService();
    $data    = Flight::request()->data->getData();

    try {
        $created = $service->create($data);
        Flight::json($created, 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/api/eras/{id}",
 *     summary="Update era",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="period", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Era updated"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Era not found"
 *     )
 * )
 */
Flight::route('PUT /api/eras/@id', function ($id) {
    $service = Flight::eraService();
    $data    = Flight::request()->data->getData();

    try {
        $updated = $service->update($id, $data);
        Flight::json($updated);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Delete(
 *     path="/api/eras/{id}",
 *     summary="Delete era",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Era deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Era not found"
 *     )
 * )
 */
Flight::route('DELETE /api/eras/@id', function ($id) {
    $service = Flight::eraService();
    try {
        $service->delete($id);
        Flight::json(['message' => 'Deleted'], 204);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * ===================== LOCATIONS API =====================
 */

/**
 * @OA\Get(
 *     path="/api/locations",
 *     summary="Get all locations",
 *     @OA\Response(
 *         response=200,
 *         description="List of locations",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="continent", type="string"),
 *                 @OA\Property(property="description", type="string")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /api/locations', function () {
    $service = Flight::locationService();
    Flight::json($service->getAll());
});

/**
 * @OA\Get(
 *     path="/api/locations/{id}",
 *     summary="Get location by id",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Location found"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid id"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Location not found"
 *     )
 * )
 */
Flight::route('GET /api/locations/@id', function ($id) {
    $service = Flight::locationService();
    try {
        $location = $service->getById($id);
        Flight::json($location);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Get(
 *     path="/api/locations/continent/{continent}",
 *     summary="Get locations by continent",
 *     @OA\Parameter(
 *         name="continent",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Locations for a given continent"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('GET /api/locations/continent/@continent', function ($continent) {
    $service = Flight::locationService();
    try {
        $locations = $service->getByContinent($continent);
        Flight::json($locations);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Post(
 *     path="/api/locations",
 *     summary="Create location",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","continent","description"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="continent", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Location created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /api/locations', function () {
    $service = Flight::locationService();
    $data    = Flight::request()->data->getData();

    try {
        $created = $service->create($data);
        Flight::json($created, 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/api/locations/{id}",
 *     summary="Update location",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="continent", type="string"),
 *             @OA\Property(property="description", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Location updated"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Location not found"
 *     )
 * )
 */
Flight::route('PUT /api/locations/@id', function ($id) {
    $service = Flight::locationService();
    $data    = Flight::request()->data->getData();

    try {
        $updated = $service->update($id, $data);
        Flight::json($updated);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Delete(
 *     path="/api/locations/{id}",
 *     summary="Delete location",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Location deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Location not found"
 *     )
 * )
 */
Flight::route('DELETE /api/locations/@id', function ($id) {
    $service = Flight::locationService();
    try {
        $service->delete($id);
        Flight::json(['message' => 'Deleted'], 204);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * ===================== RESEARCHERS API =====================
 */

/**
 * @OA\Get(
 *     path="/api/researchers",
 *     summary="Get all researchers",
 *     @OA\Response(
 *         response=200,
 *         description="List of researchers",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="field", type="string"),
 *                 @OA\Property(property="bio", type="string")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /api/researchers', function () {
    $service = Flight::researcherService();
    Flight::json($service->getAll());
});

/**
 * @OA\Get(
 *     path="/api/researchers/{id}",
 *     summary="Get researcher by id",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Researcher found"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid id"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Researcher not found"
 *     )
 * )
 */
Flight::route('GET /api/researchers/@id', function ($id) {
    $service = Flight::researcherService();
    try {
        $researcher = $service->getById($id);
        Flight::json($researcher);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Get(
 *     path="/api/researchers/search/{name}",
 *     summary="Search researchers by name",
 *     @OA\Parameter(
 *         name="name",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Matching researchers"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('GET /api/researchers/search/@name', function ($name) {
    $service = Flight::researcherService();
    try {
        $results = $service->searchByName($name);
        Flight::json($results);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Post(
 *     path="/api/researchers",
 *     summary="Create researcher",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","field","bio"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="field", type="string"),
 *             @OA\Property(property="bio", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Researcher created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /api/researchers', function () {
    $service = Flight::researcherService();
    $data    = Flight::request()->data->getData();

    try {
        $created = $service->create($data);
        Flight::json($created, 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/api/researchers/{id}",
 *     summary="Update researcher",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="field", type="string"),
 *             @OA\Property(property="bio", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Researcher updated"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Researcher not found"
 *     )
 * )
 */
Flight::route('PUT /api/researchers/@id', function ($id) {
    $service = Flight::researcherService();
    $data    = Flight::request()->data->getData();

    try {
        $updated = $service->update($id, $data);
        Flight::json($updated);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Delete(
 *     path="/api/researchers/{id}",
 *     summary="Delete researcher",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Researcher deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Researcher not found"
 *     )
 * )
 */
Flight::route('DELETE /api/researchers/@id', function ($id) {
    $service = Flight::researcherService();
    try {
        $service->delete($id);
        Flight::json(['message' => 'Deleted'], 204);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * ===================== USERS API =====================
 */

/**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Get all users",
 *     @OA\Response(
 *         response=200,
 *         description="List of users",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer"),
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="email", type="string"),
 *                 @OA\Property(property="role", type="string")
 *             )
 *         )
 *     )
 * )
 */
Flight::route('GET /api/users', function () {
    $service = Flight::userService();
    Flight::json($service->getAll());
});

/**
 * @OA\Get(
 *     path="/api/users/{id}",
 *     summary="Get user by id",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User found"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid id"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /api/users/@id', function ($id) {
    $service = Flight::userService();
    try {
        $user = $service->getById($id);
        Flight::json($user);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Get(
 *     path="/api/users/email/{email}",
 *     summary="Get user by email",
 *     @OA\Parameter(
 *         name="email",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="string", format="email")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User found"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid email"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('GET /api/users/email/@email', function ($email) {
    $service = Flight::userService();
    try {
        $user = $service->getByEmail($email);
        Flight::json($user);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Post(
 *     path="/api/users",
 *     summary="Create user",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","email","password","role"},
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *             @OA\Property(property="role", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 */
Flight::route('POST /api/users', function () {
    $service = Flight::userService();
    $data    = Flight::request()->data->getData();

    try {
        $created = $service->create($data);
        Flight::json($created, 201);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    }
});

/**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     summary="Update user",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="password", type="string", format="password"),
 *             @OA\Property(property="role", type="string")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User updated"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('PUT /api/users/@id', function ($id) {
    $service = Flight::userService();
    $data    = Flight::request()->data->getData();

    try {
        $updated = $service->update($id, $data);
        Flight::json($updated);
    } catch (InvalidArgumentException $e) {
        Flight::json(['error' => $e->getMessage()], 400);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * @OA\Delete(
 *     path="/api/users/{id}",
 *     summary="Delete user",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="User deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     )
 * )
 */
Flight::route('DELETE /api/users/@id', function ($id) {
    $service = Flight::userService();
    try {
        $service->delete($id);
        Flight::json(['message' => 'Deleted'], 204);
    } catch (RuntimeException $e) {
        Flight::json(['error' => $e->getMessage()], 404);
    }
});

/**
 * ===================== SWAGGER UI =====================
 */

Flight::route('GET /docs', function () {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>API Docs</title>
        <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css" />
    </head>
    <body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            SwaggerUIBundle({
                url: "/backend/rest/swagger.php", // adjust if your base path is different
                dom_id: '#swagger-ui'
            });
        };
    </script>
    </body>
    </html>
    <?php
});

Flight::start();
