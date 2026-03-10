# 🏫 School Management System — Backend API
> Pure PHP + MySQL | No Framework | JWT Auth

---

## 📁 Project Structure

```
school-management/
├── index.php                  # Entry point
├── .htaccess                  # URL rewriting
├── database.sql               # Full DB schema
├── config/
│   └── database.php           # DB connection (PDO)
├── helpers/
│   ├── jwt.php                # JWT generate/verify
│   └── response.php           # JSON response helpers
├── middleware/
│   └── auth.php               # JWT auth + role guard
├── models/
│   └── UserModel.php          # User DB queries
├── controllers/
│   └── AuthController.php     # register/login/me
└── routes/
    ├── router.php             # Core router
    └── auth.php               # Auth route definitions
```

---

## ⚙️ Setup Instructions

### 1. Requirements
- PHP 8.1+
- MySQL 8.0+
- Apache with mod_rewrite (XAMPP/Laragon/WAMP)

### 2. Install
```bash
# Place project in your web server root
# e.g. C:/xampp/htdocs/school-management
```

### 3. Create Database
```bash
# Open phpMyAdmin or MySQL CLI and run:
mysql -u root -p < database.sql
```

### 4. Configure Database
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'school_management');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
```

### 5. Change JWT Secret
Edit `helpers/jwt.php`:
```php
define('JWT_SECRET', 'your-very-long-random-secret-key');
```

---

## 🔑 Auth API Endpoints

### Register
```
POST /api/auth/register
Content-Type: application/json

{
  "name":     "John Doe",
  "email":    "john@school.com",
  "password": "password123",
  "role":     "teacher"        // admin | teacher | student | parent
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful.",
  "data": {
    "user":  { "id": 2, "name": "John Doe", "email": "...", "role": "teacher" },
    "token": "eyJ0eXAiOiJKV1Qi..."
  }
}
```

---

### Login
```
POST /api/auth/login
Content-Type: application/json

{
  "email":    "admin@school.com",
  "password": "admin123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful.",
  "data": {
    "user":       { "id": 1, "name": "System Admin", "role": "admin" },
    "token":      "eyJ0eXAiOiJKV1Qi...",
    "expires_in": 86400
  }
}
```

---

### Get Current User (Protected)
```
GET /api/auth/me
Authorization: Bearer <token>
```

---

### Change Password (Protected)
```
POST /api/auth/change-password
Authorization: Bearer <token>
Content-Type: application/json

{
  "current_password": "admin123",
  "new_password":     "newpass456"
}
```

---

## 🔒 Default Admin Credentials
```
Email:    admin@school.com
Password: admin123
```
> ⚠️ Change this immediately after setup!

---

## 🛡️ Protecting Routes (Usage)

```php
// In any controller method:
$payload = AuthMiddleware::authenticate();    // verifies JWT
AuthMiddleware::authorize($payload, ['admin', 'teacher']); // role check
```

---

## 📦 Coming Next (Modules to Build)

| Module      | Routes                                        |
|-------------|-----------------------------------------------|
| Students    | GET/POST/PUT/DELETE /api/students             |
| Teachers    | GET/POST/PUT/DELETE /api/teachers             |
| Classes     | GET/POST/PUT/DELETE /api/classes              |
| Subjects    | GET/POST/PUT/DELETE /api/subjects             |
| Attendance  | GET/POST /api/attendance                      |
| Grades      | GET/POST /api/grades                          |
| Timetable   | GET/POST /api/timetable                       |
