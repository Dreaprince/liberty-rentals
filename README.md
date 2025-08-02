# 📚 Liberty Rentals API

A Laravel RESTful API for an online Book Rental service. It supports user authentication, role-based access (Admin/User), and core book rental operations.

---

## 🚀 Features

- User Registration & Login (Sanctum-authenticated)
- Admin-only Book Management (CRUD)
- Rent & Return Books
- View Rental History
- Token-based Authorization
- Auto-generated API Docs using [Scribe](https://scribe.knuckles.wtf/)

---

## 🔧 Setup Instructions

### 1. Clone and Install

```bash
git clone https://github.com/yourusername/liberty-rentals.git
cd liberty-rentals
composer install
```

### 2. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=liberty_rentals
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Start the App

```bash
php artisan serve
```

App runs at: `http://127.0.0.1:8000`

---

## 🔐 Authentication

- Sanctum-based token auth
- Login/registration returns an access token
- Use token as a `Bearer` token in the header:

```http
Authorization: Bearer {YOUR_TOKEN}
```

---

## 🧪 Sample Credentials

```json
// Admin
{
  "email": "admin@example.com",
  "password": "password"
}

// User
{
  "email": "user@example.com",
  "password": "password"
}
```

> You can seed or manually create these using tinker or database GUI.

---

## 📬 API Endpoints

Full documentation available at:

```
http://127.0.0.1:8000/docs
```

Or import the Postman collection from:

```
storage/app/private/scribe/collection.json
```

---

## ✍️ Example Endpoints

| Method | Endpoint | Auth Required | Description |
|--------|----------|---------------|-------------|
| POST | /api/register | No | Register a new user |
| POST | /api/login | No | Login and receive token |
| GET | /api/books | Yes | List all books |
| GET | /api/books/{id} | Yes | View book detail |
| POST | /api/books | Yes (Admin) | Create a book |
| PUT | /api/books/{id} | Yes (Admin) | Update a book |
| DELETE | /api/books/{id} | Yes (Admin) | Delete a book |
| POST | /api/rentals | Yes | Rent a book |
| POST | /api/rentals/{id}/return | Yes | Return a book |
| GET | /api/my-rentals | Yes | View user’s rental history |

---

## 🧪 Testing

```bash
php artisan test
```

---

## ✅ Requirements

- PHP >= 8.2
- Laravel ^12.x
- MySQL / PostgreSQL
- Composer

---

## 📂 Project Structure

```
├── app
│   ├── Http
│   │   ├── Controllers
│   │   └── Middleware
│   ├── Models
│   └── Providers
├── config
│   └── cors.php
├── database
│   ├── migrations
│   └── seeders
├── routes
│   └── api.php
└── storage
```

---

## 🛠 Tools Used

- Laravel Sanctum – Token authentication
- Scribe – API documentation
- Postman – API Testing
- MySQL – Database

---

## 📜 License

MIT © 2025 Liberty Rentals Team
