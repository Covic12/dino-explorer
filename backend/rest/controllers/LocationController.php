<?php
require_once __DIR__ . '/../services/LocationService.php';

class LocationController {
    private $service;

    public function __construct() {
        $this->service = new LocationService();
    }

    /**
     * @OA\Get(
     *     path="/locations",
     *     tags={"Locations"},
     *     summary="Get all locations",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Location"))
     *     )
     * )
     */
    public function getAll() {
        try {
            $locations = $this->service->getAll();
            Flight::json($locations, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/locations/{id}",
     *     tags={"Locations"},
     *     summary="Get location by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Location")
     *     ),
     *     @OA\Response(response=404, description="Location not found")
     * )
     */
    public function getById($id) {
        try {
            $location = $this->service->getById($id);
            Flight::json($location, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/locations",
     *     tags={"Locations"},
     *     summary="Create a new location",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LocationInput")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Location created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Location")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function create() {
        try {
            $data = Flight::request()->data->getData();
            $location = $this->service->add($data);
            Flight::json($location, 201);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/locations/{id}",
     *     tags={"Locations"},
     *     summary="Update a location",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/LocationInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Location updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Location")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Location not found")
     * )
     */
    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            $location = $this->service->update($id, $data);
            Flight::json($location, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/locations/{id}",
     *     tags={"Locations"},
     *     summary="Delete a location",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Location deleted successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Location not found")
     * )
     */
    public function delete($id) {
        try {
            $this->service->delete($id);
            Flight::json(['message' => 'Location deleted successfully'], 204);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }
}

/**
 * @OA\Schema(
 *     schema="Location",
 *     @OA\Property(property="location_id", type="integer"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="picture", type="string"),
 *     @OA\Property(property="continent", type="string"),
 *     @OA\Property(property="country", type="string"),
 *     @OA\Property(property="location_url", type="string")
 * )
 * @OA\Schema(
 *     schema="LocationInput",
 *     required={"title"},
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="picture", type="string"),
 *     @OA\Property(property="continent", type="string"),
 *     @OA\Property(property="country", type="string"),
 *     @OA\Property(property="location_url", type="string")
 * )
 */
