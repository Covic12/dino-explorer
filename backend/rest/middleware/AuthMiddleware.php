<?php
require_once __DIR__ . '/../config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    
    public static function authenticate() {
        $headers = getallheaders();
        $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : null;

        if (!$authHeader) {
            Flight::json(['error' => 'Authorization header missing'], 401);
            return false;
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = JWT::decode($token, new Key(Config::JWT_SECRET(), 'HS256'));
            Flight::set('user', $decoded);
            return true;
        } catch (Exception $e) {
            Flight::json(['error' => 'Invalid or expired token'], 401);
            return false;
        }
    }

    public static function requireAuth() {
        if (!self::authenticate()) {
            Flight::stop();
        }
    }

    public static function requireAdmin() {
        if (!self::authenticate()) {
            Flight::stop();
            return;
        }

        $user = Flight::get('user');
        if ($user->role !== 'admin') {
            Flight::json(['error' => 'Admin access required'], 403);
            Flight::stop();
        }
    }
}
