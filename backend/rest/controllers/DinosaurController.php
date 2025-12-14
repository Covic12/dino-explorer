<?php
require_once __DIR__ . '/../services/DinosaurService.php';

/**
 * @OA\Info(
 *     title="Dino Explorer API",
 *     version="1.0.0",
 *     description="API for managing dinosaurs, eras, locations, researchers, and users"
 * )
 * @OA\Server(
 *     url="http://localhost/dino-explorer/backend",
 *     description="Local development server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class DinosaurController {
    private $service;

    public function __construct() {
        $this->service = new DinosaurService();
    }

    /**
     * @OA\Get(
     *     path="/dinosaurs",
     *     tags={"Dinosaurs"},
     *     summary="Get all dinosaurs",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Dinosaur"))
     *     )
     * )
     */
    public function getAll() {
        try {
            $dinosaurs = $this->service->getAll();
            Flight::json($dinosaurs, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/dinosaurs/{id}",
     *     tags={"Dinosaurs"},
     *     summary="Get dinosaur by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Dinosaur")
     *     ),
     *     @OA\Response(response=404, description="Dinosaur not found")
     * )
     */
    public function getById($id) {
        try {
            $dinosaur = $this->service->getById($id);
            Flight::json($dinosaur, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/dinosaurs",
     *     tags={"Dinosaurs"},
     *     summary="Create a new dinosaur",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DinosaurInput")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dinosaur created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Dinosaur")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function create() {
        try {
            $data = Flight::request()->data->getData();
            $dinosaur = $this->service->add($data);
            Flight::json($dinosaur, 201);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/dinosaurs/{id}",
     *     tags={"Dinosaurs"},
     *     summary="Update a dinosaur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/DinosaurInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dinosaur updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Dinosaur")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Dinosaur not found")
     * )
     */
    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            $dinosaur = $this->service->update($id, $data);
            Flight::json($dinosaur, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/dinosaurs/{id}",
     *     tags={"Dinosaurs"},
     *     summary="Delete a dinosaur",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Dinosaur deleted successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Dinosaur not found")
     * )
     */
    public function delete($id) {
        try {
            $this->service->delete($id);
            Flight::json(['message' => 'Dinosaur deleted successfully'], 204);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }
}

/**
 * @OA\Schema(
 *     schema="Dinosaur",
 *     @OA\Property(property="dinosaur_id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="diet", type="string"),
 *     @OA\Property(property="size", type="string"),
 *     @OA\Property(property="weight", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="era_id", type="integer"),
 *     @OA\Property(property="location_id", type="integer")
 * )
 * @OA\Schema(
 *     schema="DinosaurInput",
 *     required={"name"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="diet", type="string"),
 *     @OA\Property(property="size", type="string"),
 *     @OA\Property(property="weight", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="era_id", type="integer"),
 *     @OA\Property(property="location_id", type="integer")
 * )
 */
