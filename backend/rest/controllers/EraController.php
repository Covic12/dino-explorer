<?php
require_once __DIR__ . '/../services/EraService.php';

class EraController {
    private $service;

    public function __construct() {
        $this->service = new EraService();
    }

    /**
     * @OA\Get(
     *     path="/eras",
     *     tags={"Eras"},
     *     summary="Get all eras",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Era"))
     *     )
     * )
     */
    public function getAll() {
        try {
            $eras = $this->service->getAll();
            Flight::json($eras, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/eras/{id}",
     *     tags={"Eras"},
     *     summary="Get era by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Era")
     *     ),
     *     @OA\Response(response=404, description="Era not found")
     * )
     */
    public function getById($id) {
        try {
            $era = $this->service->getById($id);
            Flight::json($era, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/eras",
     *     tags={"Eras"},
     *     summary="Create a new era",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EraInput")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Era created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Era")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function create() {
        try {
            $data = Flight::request()->data->getData();
            $era = $this->service->add($data);
            Flight::json($era, 201);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/eras/{id}",
     *     tags={"Eras"},
     *     summary="Update an era",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/EraInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Era updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Era")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Era not found")
     * )
     */
    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            $era = $this->service->update($id, $data);
            Flight::json($era, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/eras/{id}",
     *     tags={"Eras"},
     *     summary="Delete an era",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Era deleted successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Era not found")
     * )
     */
    public function delete($id) {
        try {
            $this->service->delete($id);
            Flight::json(['message' => 'Era deleted successfully'], 204);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }
}

/**
 * @OA\Schema(
 *     schema="Era",
 *     @OA\Property(property="era_id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="era", type="string"),
 *     @OA\Property(property="period", type="string")
 * )
 * @OA\Schema(
 *     schema="EraInput",
 *     required={"title"},
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="era", type="string"),
 *     @OA\Property(property="period", type="string")
 * )
 */
