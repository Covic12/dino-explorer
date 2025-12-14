<?php
require_once __DIR__ . '/../services/UserService.php';
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController {
    private $service;

    public function __construct() {
        $this->service = new UserService();
    }

    public function register() {
        try {
            $data = Flight::request()->data->getData();
            $user = $this->service->register($data);
            unset($user['password']);
            Flight::json([
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    public function login() {
        try {
            $data = Flight::request()->data->getData();
            
            if (empty($data['username']) || empty($data['password'])) {
                throw new Exception("Username and password are required");
            }

            $user = $this->service->login($data['username'], $data['password']);
            
            $payload = [
                'iss' => 'dino-explorer',
                'iat' => time(),
                'exp' => time() + (60 * 60 * 24),
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];

            $jwt = JWT::encode($payload, Config::JWT_SECRET(), 'HS256');
            
            unset($user['password']);
            
            Flight::json([
                'message' => 'Login successful',
                'token' => $jwt,
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 401);
        }
    }
}
