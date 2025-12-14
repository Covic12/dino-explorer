<?php
require_once __DIR__ . '/../services/ResearcherService.php';

class ResearcherController {
    private $service;

    public function __construct() {
        $this->service = new ResearcherService();
    }

    /**
     * @OA\Get(
     *     path="/researchers",
     *     tags={"Researchers"},
     *     summary="Get all researchers",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Researcher"))
     *     )
     * )
     */
    public function getAll() {
        try {
            $researchers = $this->service->getAll();
            Flight::json($researchers, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/researchers/{id}",
     *     tags={"Researchers"},
     *     summary="Get researcher by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Researcher")
     *     ),
     *     @OA\Response(response=404, description="Researcher not found")
     * )
     */
    public function getById($id) {
        try {
            $researcher = $this->service->getById($id);
            Flight::json($researcher, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/researchers",
     *     tags={"Researchers"},
     *     summary="Create a new researcher",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResearcherInput")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Researcher created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Researcher")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function create() {
        try {
            $data = Flight::request()->data->getData();
            $researcher = $this->service->add($data);
            Flight::json($researcher, 201);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/researchers/{id}",
     *     tags={"Researchers"},
     *     summary="Update a researcher",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ResearcherInput")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Researcher updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Researcher")
     *     ),
     *     @OA\Response(response=400, description="Invalid input"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Researcher not found")
     * )
     */
    public function update($id) {
        try {
            $data = Flight::request()->data->getData();
            $researcher = $this->service->update($id, $data);
            Flight::json($researcher, 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/researchers/{id}",
     *     tags={"Researchers"},
     *     summary="Delete a researcher",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Researcher deleted successfully"),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=404, description="Researcher not found")
     * )
     */
    public function delete($id) {
        try {
            $this->service->delete($id);
            Flight::json(['message' => 'Researcher deleted successfully'], 204);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }
}

/**
 * @OA\Schema(
 *     schema="Researcher",
 *     @OA\Property(property="researcher_id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="picture", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="discoveries", type="string")
 * )
 * @OA\Schema(
 *     schema="ResearcherInput",
 *     required={"name"},
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="picture", type="string"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="discoveries", type="string")
 * )
 */
