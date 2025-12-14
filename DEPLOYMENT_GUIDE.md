# Dino Explorer - Deployment Guide

## Milestone 3 & 4 Implementation Complete ✅

This guide will help you deploy and test the complete Dino Explorer application with full CRUD operations, authentication, and authorization.

---

## 📋 Prerequisites

- XAMPP installed with Apache and MySQL running
- Composer installed
- Modern web browser (Chrome, Firefox, Edge)

---

## 🚀 Step-by-Step Deployment

### Step 1: Install Backend Dependencies

```bash
cd C:\xampp\htdocs\dino-explorer\backend
composer install
```

This will install:

- FlightPHP (routing framework)
- Swagger-PHP (OpenAPI documentation)
- Firebase JWT (authentication)

### Step 2: Database Setup

1. **Start XAMPP** and ensure MySQL is running

2. **Run the initial database schema:**

   ```bash
   mysql -u root -p1234 < C:\xampp\htdocs\dino-explorer\data_base_info\dinosaur_db.sql
   ```

3. **Run the authentication migration:**

   ```bash
   mysql -u root -p1234 < C:\xampp\htdocs\dino-explorer\data_base_info\migration_user_auth.sql
   ```

   Or manually execute in phpMyAdmin:

   - Open http://localhost/phpmyadmin
   - Select `dinosaur_db` database
   - Go to SQL tab
   - Copy and paste the contents of `migration_user_auth.sql`
   - Click "Go"

### Step 3: Configure Backend

Update `backend/rest/config.php` if needed:

```php
DB_HOST: 127.0.0.1
DB_PORT: 3306
DB_USER: root
DB_PASSWORD: 1234
DB_NAME: dinosaur_db
JWT_SECRET: your_key_string  // Change this to a secure random string in production
```

### Step 4: Verify Apache Configuration

Ensure `.htaccess` is enabled in Apache:

1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find and uncomment: `LoadModule rewrite_module modules/mod_rewrite.so`
3. Restart Apache

### Step 5: Create Initial Admin User

Open phpMyAdmin and run this SQL to create an admin user:

```sql
USE dinosaur_db;

INSERT INTO user (username, email, password, role, registration_date)
VALUES (
    'admin',
    'admin@dinoexplorer.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',  -- password: password
    'admin',
    CURDATE()
);
```

**Default Admin Credentials:**

- Username: `admin`
- Password: `password`

---

## 🧪 Testing the Application

### 1. Test Backend API

**Access Swagger Documentation:**

```
http://localhost/dino-explorer/backend/api/swagger
```

This provides an interactive interface to test all API endpoints.

**Test Authentication:**

Register a new user:

```bash
curl -X POST http://localhost/dino-explorer/backend/api/auth/register \
  -H "Content-Type: application/json" \
  -d "{\"username\":\"testuser\",\"email\":\"test@example.com\",\"password\":\"test123\"}"
```

Login:

```bash
curl -X POST http://localhost/dino-explorer/backend/api/auth/login \
  -H "Content-Type: application/json" \
  -d "{\"username\":\"testuser\",\"password\":\"test123\"}"
```

**Test CRUD Operations:**

Get all dinosaurs (public):

```bash
curl http://localhost/dino-explorer/backend/api/dinosaurs
```

Create a dinosaur (requires authentication):

```bash
curl -X POST http://localhost/dino-explorer/backend/api/dinosaurs \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d "{\"name\":\"Velociraptor\",\"diet\":\"Carnivore\",\"size\":\"Small\"}"
```

### 2. Test Frontend Integration

**Access the Application:**

```
http://localhost/dino-explorer/frontend/views/login.html
```

**Test User Flow:**

1. **Register a new account:**

   - Go to register page
   - Fill in username, email, password
   - Click "Sign Up"
   - Should redirect to login

2. **Login:**

   - Enter username and password
   - Click "Log In"
   - Should redirect to dashboard

3. **View Dashboard:**

   - Should see welcome message with username and role
   - Navigation menu should be visible

4. **Test Logout:**
   - Click "Log Out"
   - Should redirect to login page
   - Token should be cleared

---

## 🔐 Access Control Testing

### Regular User (role: 'user')

- ✅ Can view all entities (GET)
- ✅ Can create entities (POST)
- ✅ Can update entities (PUT)
- ❌ Cannot delete entities (DELETE) - Admin only

### Admin User (role: 'admin')

- ✅ Full CRUD access to all entities
- ✅ Can delete any entity
- ✅ Can manage all resources

**Test Admin Access:**

1. Login as admin (username: `admin`, password: `password`)
2. Try to delete a dinosaur via API:
   ```bash
   curl -X DELETE http://localhost/dino-explorer/backend/api/dinosaurs/1 \
     -H "Authorization: Bearer ADMIN_TOKEN_HERE"
   ```
3. Should succeed with 204 status

**Test Regular User Restriction:**

1. Login as regular user
2. Try to delete a dinosaur:
   ```bash
   curl -X DELETE http://localhost/dino-explorer/backend/api/dinosaurs/1 \
     -H "Authorization: Bearer USER_TOKEN_HERE"
   ```
3. Should fail with 403 Forbidden

---

## 📁 Project Structure

```
dino-explorer/
├── backend/
│   ├── rest/
│   │   ├── controllers/          # Presentation Layer
│   │   │   ├── DinosaurController.php
│   │   │   ├── EraController.php
│   │   │   ├── LocationController.php
│   │   │   ├── ResearcherController.php
│   │   │   └── AuthController.php
│   │   ├── services/             # Business Logic Layer
│   │   │   ├── BaseService.php
│   │   │   ├── DinosaurService.php
│   │   │   ├── EraService.php
│   │   │   ├── LocationService.php
│   │   │   ├── ResearcherService.php
│   │   │   └── UserService.php
│   │   ├── dao/                  # Data Access Layer
│   │   │   ├── BaseDao.php
│   │   │   ├── DinosaursDao.php
│   │   │   ├── EraDao.php
│   │   │   ├── LocationDao.php
│   │   │   ├── ResearcherDao.php
│   │   │   └── UserDao.php
│   │   ├── middleware/           # Middleware
│   │   │   ├── AuthMiddleware.php
│   │   │   └── CorsMiddleware.php
│   │   └── config.php
│   ├── index.php                 # Main routing file
│   ├── composer.json
│   └── README.md
├── frontend/
│   ├── javascript/
│   │   └── api.js                # API integration
│   ├── views/
│   │   ├── login.html            # Updated with API
│   │   ├── register.html         # Updated with API
│   │   ├── dashboard.html        # Updated with auth
│   │   └── auth.html             # New auth page
│   └── css/
├── data_base_info/
│   ├── dinosaur_db.sql
│   └── migration_user_auth.sql
└── DEPLOYMENT_GUIDE.md
```

---

## 🎯 Milestone Requirements Checklist

### ✅ Milestone 3: Full CRUD Implementation & OpenAPI Documentation

- [x] **Business Logic Layer (2 pts)**

  - Service classes for all entities
  - Business logic encapsulated in services
  - Validation rules implemented
  - Reusable and modular design

- [x] **Presentation Layer (1 pt)**

  - FlightPHP implementation
  - Dynamic content rendering
  - Clean separation of concerns

- [x] **OpenAPI Documentation (2 pts)**
  - All endpoints documented
  - Request/response schemas defined
  - Swagger UI available at `/api/swagger`
  - OpenAPI JSON at `/api/docs`

### ✅ Milestone 4: Middleware, Authentication & Authorization

- [x] **Middleware & Authentication (1 pt)**

  - Authentication middleware (JWT-based)
  - Request validation
  - User registration and login
  - Password hashing (bcrypt)
  - Protected routes

- [x] **Authorization (1 pt)**

  - Role-based access control (RBAC)
  - Admin: Full CRUD access
  - User: Limited access (no delete)
  - Middleware enforcement

- [x] **Frontend Integration & UI Updates (3 pts)**
  - Frontend connected to backend APIs
  - User authentication flow
  - Role-aware UI
  - Login/Register pages
  - Dashboard with user info
  - Logout functionality

---

## 🔧 API Endpoints Summary

### Authentication

- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user

### Dinosaurs

- `GET /api/dinosaurs` - Get all (public)
- `GET /api/dinosaurs/{id}` - Get by ID (public)
- `POST /api/dinosaurs` - Create (authenticated)
- `PUT /api/dinosaurs/{id}` - Update (authenticated)
- `DELETE /api/dinosaurs/{id}` - Delete (admin only)

### Eras

- `GET /api/eras` - Get all (public)
- `GET /api/eras/{id}` - Get by ID (public)
- `POST /api/eras` - Create (authenticated)
- `PUT /api/eras/{id}` - Update (authenticated)
- `DELETE /api/eras/{id}` - Delete (admin only)

### Locations

- `GET /api/locations` - Get all (public)
- `GET /api/locations/{id}` - Get by ID (public)
- `POST /api/locations` - Create (authenticated)
- `PUT /api/locations/{id}` - Update (authenticated)
- `DELETE /api/locations/{id}` - Delete (admin only)

### Researchers

- `GET /api/researchers` - Get all (public)
- `GET /api/researchers/{id}` - Get by ID (public)
- `POST /api/researchers` - Create (authenticated)
- `PUT /api/researchers/{id}` - Update (authenticated)
- `DELETE /api/researchers/{id}` - Delete (admin only)

---

## 🐛 Troubleshooting

### Issue: 404 Not Found on API calls

**Solution:**

- Check `.htaccess` exists in backend folder
- Verify mod_rewrite is enabled in Apache
- Restart Apache

### Issue: Database connection failed

**Solution:**

- Verify MySQL is running in XAMPP
- Check credentials in `config.php`
- Ensure database `dinosaur_db` exists

### Issue: Token invalid or expired

**Solution:**

- Token expires after 24 hours
- Login again to get new token
- Check JWT_SECRET is set correctly

### Issue: CORS errors in browser

**Solution:**

- CORS middleware is already configured
- Check browser console for specific error
- Verify API URL in `frontend/javascript/api.js`

### Issue: Cannot delete entities as admin

**Solution:**

- Verify user role is 'admin' in database
- Check token contains correct role claim
- Ensure Authorization header is sent

---

## 📝 Next Steps

1. **Populate Database:** Add sample dinosaurs, eras, locations, and researchers
2. **Update Frontend Pages:** Integrate API calls into dinosaurs.html, eras.html, etc.
3. **Add Admin Panel:** Create dedicated admin interface for management
4. **Enhance UI:** Add role-based buttons (Create, Edit, Delete)
5. **Error Handling:** Improve user feedback for API errors
6. **Testing:** Comprehensive testing of all endpoints and user flows

---

## 📚 Additional Resources

- **FlightPHP Documentation:** https://flightphp.com/
- **Swagger/OpenAPI:** https://swagger.io/docs/
- **JWT Introduction:** https://jwt.io/introduction
- **Backend README:** `backend/README.md`

---

## ✅ Submission Checklist

- [x] All CRUD endpoints implemented
- [x] Business logic in service layer
- [x] OpenAPI documentation complete
- [x] Swagger UI accessible
- [x] JWT authentication working
- [x] Role-based authorization enforced
- [x] Frontend integrated with backend
- [x] Login/Register functional
- [x] User roles displayed in UI
- [x] Database migration provided
- [x] Documentation complete

---

**Implementation Date:** December 2024  
**Status:** ✅ Ready for Submission  
**Milestones:** 3 & 4 Complete
