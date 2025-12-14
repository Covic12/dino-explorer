# Dino Explorer Backend API

## Overview

This is a RESTful API built with FlightPHP for managing dinosaurs, eras, locations, researchers, and users. It includes JWT-based authentication and role-based access control (RBAC).

## Setup Instructions

### 1. Install Dependencies

```bash
cd backend
composer install
```

### 2. Database Setup

Run the following SQL files in order:

1. `data_base_info/dinosaur_db.sql` - Creates the initial database schema
2. `data_base_info/migration_user_auth.sql` - Adds authentication fields to user table

```bash
mysql -u root -p < ../data_base_info/dinosaur_db.sql
mysql -u root -p < ../data_base_info/migration_user_auth.sql
```

### 3. Configuration

Update `backend/rest/config.php` with your database credentials:

- DB_HOST
- DB_PORT
- DB_USER
- DB_PASSWORD
- JWT_SECRET (change to a secure random string)

### 4. Start Server

Ensure XAMPP Apache server is running and access:

```
http://localhost/dino-explorer/backend/
```

## API Documentation

### Swagger UI

Access interactive API documentation at:

```
http://localhost/dino-explorer/backend/api/swagger
```

### OpenAPI JSON

Get the OpenAPI specification at:

```
http://localhost/dino-explorer/backend/api/docs
```

## Authentication

### Register a New User

```bash
POST /api/auth/register
Content-Type: application/json

{
  "username": "john_doe",
  "email": "john@example.com",
  "password": "password123",
  "role": "user"
}
```

### Login

```bash
POST /api/auth/login
Content-Type: application/json

{
  "username": "john_doe",
  "password": "password123"
}
```

Response includes a JWT token:

```json
{
  "message": "Login successful",
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "user": {
    "user_id": 1,
    "username": "john_doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

### Using the Token

Include the token in the Authorization header for protected endpoints:

```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
```

## API Endpoints

### Dinosaurs

- `GET /api/dinosaurs` - Get all dinosaurs (public)
- `GET /api/dinosaurs/{id}` - Get dinosaur by ID (public)
- `POST /api/dinosaurs` - Create dinosaur (authenticated)
- `PUT /api/dinosaurs/{id}` - Update dinosaur (authenticated)
- `DELETE /api/dinosaurs/{id}` - Delete dinosaur (admin only)

### Eras

- `GET /api/eras` - Get all eras (public)
- `GET /api/eras/{id}` - Get era by ID (public)
- `POST /api/eras` - Create era (authenticated)
- `PUT /api/eras/{id}` - Update era (authenticated)
- `DELETE /api/eras/{id}` - Delete era (admin only)

### Locations

- `GET /api/locations` - Get all locations (public)
- `GET /api/locations/{id}` - Get location by ID (public)
- `POST /api/locations` - Create location (authenticated)
- `PUT /api/locations/{id}` - Update location (authenticated)
- `DELETE /api/locations/{id}` - Delete location (admin only)

### Researchers

- `GET /api/researchers` - Get all researchers (public)
- `GET /api/researchers/{id}` - Get researcher by ID (public)
- `POST /api/researchers` - Create researcher (authenticated)
- `PUT /api/researchers/{id}` - Update researcher (authenticated)
- `DELETE /api/researchers/{id}` - Delete researcher (admin only)

## Architecture

### Layers

#### 1. Presentation Layer (Controllers)

- `DinosaurController.php`
- `EraController.php`
- `LocationController.php`
- `ResearcherController.php`
- `AuthController.php`

Controllers handle HTTP requests/responses and delegate business logic to services.

#### 2. Business Logic Layer (Services)

- `DinosaurService.php`
- `EraService.php`
- `LocationService.php`
- `ResearcherService.php`
- `UserService.php`

Services contain all business logic, validation, and application-specific constraints.

#### 3. Data Access Layer (DAOs)

- `DinosaursDao.php`
- `EraDao.php`
- `LocationDao.php`
- `ResearcherDao.php`
- `UserDao.php`

DAOs handle database operations and extend `BaseDao` for common CRUD operations.

### Middleware

- `AuthMiddleware.php` - JWT authentication and authorization
- `CorsMiddleware.php` - CORS headers for cross-origin requests

## Role-Based Access Control (RBAC)

### Roles

- **user** - Regular user with read access and limited write access
- **admin** - Full CRUD access to all entities

### Access Rules

- **Public endpoints** - GET requests for viewing data
- **Authenticated endpoints** - POST/PUT requests require valid JWT token
- **Admin-only endpoints** - DELETE requests require admin role

## Error Handling

All endpoints return JSON responses with appropriate HTTP status codes:

- `200` - Success
- `201` - Created
- `204` - No Content (successful deletion)
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (missing or invalid token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `500` - Internal Server Error

## Security Features

1. **Password Hashing** - Uses bcrypt for secure password storage
2. **JWT Tokens** - Stateless authentication with 24-hour expiration
3. **Input Validation** - All inputs validated in service layer
4. **SQL Injection Prevention** - PDO prepared statements
5. **CORS Protection** - Configurable CORS headers

## Development

### Adding a New Entity

1. Create DAO in `rest/dao/` extending `BaseDao`
2. Create Service in `rest/services/` extending `BaseService`
3. Create Controller in `rest/controllers/` with OpenAPI annotations
4. Add routes in `index.php`
5. Apply authentication/authorization middleware as needed

### Testing

Use tools like Postman, Insomnia, or curl to test endpoints:

```bash
# Get all dinosaurs
curl http://localhost/dino-explorer/backend/api/dinosaurs

# Login
curl -X POST http://localhost/dino-explorer/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}'

# Create dinosaur (with token)
curl -X POST http://localhost/dino-explorer/backend/api/dinosaurs \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{"name":"T-Rex","diet":"Carnivore","size":"Large"}'
```

## Troubleshooting

### Common Issues

1. **404 errors** - Check `.htaccess` file exists and mod_rewrite is enabled
2. **Database connection errors** - Verify credentials in `config.php`
3. **CORS errors** - Check `CorsMiddleware` settings
4. **Token errors** - Ensure JWT_SECRET is set and token is not expired

### Logs

Check Apache error logs:

```
C:\xampp\apache\logs\error.log
```
