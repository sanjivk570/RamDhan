# RamDhan API

Enterprise-grade Laravel 12 REST API Boilerplate built using a modular architecture.

---

# Technology Stack

- Laravel 12
- PHP 8.3+
- MySQL
- Laravel Sanctum
- Spatie Laravel Permission
- Repository Pattern
- Service Layer
- Action Pattern
- API Resources
- Form Requests
- UUID Support
- Soft Deletes

---

# Project Architecture

```
app
│
├── Core
│   ├── Enums
│   ├── Exceptions
│   ├── Helpers
│   ├── Http
│   ├── Requests
│   ├── Traits
│   └── Services
│
├── Modules
│   │
│   ├── Auth
│   │
│   ├── User
│   │
│   └── Role
│
└── Providers
```

Each module follows the same structure.

```
Module
│
├── Actions
├── Controllers
├── Models
├── Repositories
├── Requests
├── Resources
├── Routes
├── Seeders
└── Services
```

---

# Request Flow

```
Request

↓

Route

↓

Controller

↓

Action

↓

Service

↓

Repository

↓

Model

↓

Database
```

---

# Features Completed

## Authentication

- User Registration
- Login
- Logout
- Profile
- Change Password
- Forgot Password
- Reset Password
- Sanctum Token Authentication

---

## User Module

Completed APIs

- User List
- User Details
- Create User
- Update User
- Activate User
- Deactivate User
- Soft Delete User
- Restore User

Supports

- Pagination
- Searching
- Sorting
- Filtering
- UUID
- Soft Delete

---

## Role Module

Completed APIs

- Role List
- Role Details
- Create Role
- Update Role
- Delete Role
- Get Role Permissions
- Assign Permissions

---

## Permission Module

Completed APIs

- Permission List
- Permission Details

Permission CRUD is intentionally disabled because permissions are developer-managed.

---

# Authorization

Integrated

- Laravel Sanctum
- Spatie Permission

Supported

- Roles
- Permissions
- Middleware Protection

Example

```
permission:user.view

permission:user.create

permission:user.update

permission:user.delete
```

---

# API Response Format

Success

```json
{
    "success": true,
    "message": "Success",
    "data": {}
}
```

Validation Error

```json
{
    "success": false,
    "message": "Validation Error",
    "errors": {}
}
```

Server Error

```json
{
    "success": false,
    "message": "Internal Server Error"
}
```

---

# Folder Structure Example

```
User

Actions
Controllers
Models
Repositories
Requests
Resources
Routes
Services
```

---

# Design Patterns

Implemented

- Repository Pattern
- Service Layer
- Action Pattern
- Resource Pattern
- Request Validation
- Dependency Injection

---

# Authentication Flow

```
Register

↓

Login

↓

Sanctum Token

↓

Bearer Token

↓

Protected APIs
```

---

# Role Permission Flow

```
Permission Seeder

↓

Role Seeder

↓

Assign Permission

↓

User Role

↓

Middleware

↓

Protected API
```

---

# Validation

Using Laravel Form Requests

Example

- CreateUserRequest
- UpdateUserRequest
- LoginRequest
- RegisterRequest

---

# UUID

UUID is used as the public identifier for APIs.

Example

```
GET /users/{uuid}
```

instead of

```
GET /users/{id}
```

---

# Database Features

- UUID
- Soft Deletes
- Timestamps
- Indexes

---

# Security

- Password Hashing
- Sanctum Authentication
- Authorization Middleware
- Validation
- Hidden Attributes
- Mass Assignment Protection

---

# Current Modules

✅ Auth

✅ User

✅ Role

✅ Permission

---

# Next Planned Modules

- Category
- Brand
- Product
- Media
- Inventory
- Customer
- Supplier
- Purchase
- Sales
- Orders
- Dashboard
- Settings
- Notifications
- Activity Logs

---

# Coding Standards

- PSR-12
- Strict Types
- Constructor Dependency Injection
- Thin Controllers
- Business Logic inside Services
- Database Logic inside Repositories
- Single Responsibility Principle

---

# API Testing

Recommended Tool

- Postman

Authentication

```
Authorization

Bearer {token}
```

Header

```
Accept: application/json
```

---

# Future Enhancements

- DTO Layer
- Policies
- Events & Listeners
- Queue Jobs
- API Versioning
- Swagger/OpenAPI
- Docker Support
- CI/CD
- Unit Testing
- Feature Testing
- Audit Logs
- Multi Tenancy

---

# Project Status

Current Version

```
v1.0.0 (Foundation Completed)
```

Completed

- Authentication
- Authorization
- User Management
- Role Management
- Permission Management

The project is now ready for business module development.