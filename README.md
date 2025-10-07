# RIPARO Backend API

<div align="center">

![RIPARO Backend](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)

**Report • Process • Resolve**

_A robust Laravel API backend for citizen reporting and LGU management_

[![Laravel](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0+-orange.svg)](https://mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

</div>

---

## Table of Contents

-   [Overview](#overview)
-   [Features](#features)
-   [Technology Stack](#technology-stack)
-   [Prerequisites](#prerequisites)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Database Setup](#database-setup)
-   [API Documentation](#api-documentation)
-   [Authentication](#authentication)
-   [User Management](#user-management)
-   [Report Management](#report-management)
-   [File Storage](#file-storage)
-   [Testing](#testing)
-   [Deployment](#deployment)
-   [Troubleshooting](#troubleshooting)
-   [Contributing](#contributing)
-   [License](#license)

---

## Overview

RIPARO Backend is a comprehensive Laravel API that powers the citizen reporting platform for Local Government Units (LGUs) in the Philippines. It provides secure, scalable, and efficient backend services for managing citizen reports, user verification, feedback systems, and administrative functions.

### What RIPARO Backend Provides

The backend serves as the central nervous system of the RIPARO platform, offering:

-   **Secure Authentication**: JWT-based authentication with role-based access control
-   **Report Management**: Complete lifecycle management of citizen reports
-   **User Verification**: Multi-step verification process for citizen registration
-   **File Storage**: Secure handling of photos and documents
-   **Analytics**: Comprehensive reporting and statistical data
-   **API Security**: CORS protection, rate limiting, and input validation

---

## Features

### Core Functionality

-   **Multi-Role Authentication**: Separate access levels for citizens, administrators, and mayors
-   **Report Lifecycle Management**: From submission to resolution with status tracking
-   **Document Upload**: Secure file storage for photos and ID documents
-   **User Verification System**: Admin approval process for citizen accounts
-   **Feedback Management**: Citizen feedback collection and review
-   **Profile Update Requests**: Secure profile modification workflow

### Administrative Features

-   **Dashboard Analytics**: Statistical insights and reporting metrics
-   **User Management**: Complete user lifecycle management
-   **Report Assignment**: Task assignment and progress tracking
-   **Bulk Operations**: Efficient handling of multiple records
-   **Audit Logging**: Comprehensive activity tracking

### Security Features

-   **JWT Authentication**: Secure token-based authentication
-   **Role-Based Access Control**: Granular permissions system
-   **Input Validation**: Comprehensive data validation and sanitization
-   **CORS Protection**: Cross-origin request security
-   **Rate Limiting**: API abuse prevention
-   **File Upload Security**: Secure file handling and validation

---

## Technology Stack

### Core Framework

-   **Laravel 11.x**: Modern PHP framework with elegant syntax
-   **PHP 8.1+**: Latest PHP features and performance improvements
-   **Composer**: Dependency management for PHP packages

### Database & Storage

-   **MySQL 8.0+**: Primary database for data persistence
-   **Laravel Eloquent**: Powerful ORM for database interactions
-   **File Storage**: Local and cloud storage support
-   **Database Migrations**: Version-controlled database schema

### Authentication & Security

-   **Laravel Sanctum**: API token authentication
-   **JWT Tokens**: Secure authentication tokens
-   **CORS Middleware**: Cross-origin request handling
-   **Rate Limiting**: API request throttling
-   **Input Validation**: Request validation and sanitization

### Development Tools

-   **Pest PHP**: Modern testing framework
-   **PHPUnit**: Unit and feature testing
-   **Laravel Pint**: Code style fixing
-   **Laravel Telescope**: Debug and monitoring (optional)

---

## Prerequisites

Before installing RIPARO Backend, ensure your system meets these requirements:

### System Requirements

-   **PHP**: Version 8.1 or higher
-   **Composer**: Latest version
-   **MySQL**: Version 8.0 or higher
-   **Web Server**: Apache or Nginx
-   **SSL Certificate**: For production deployment

### PHP Extensions

Ensure these PHP extensions are installed:

```bash
# Required extensions
php-mysql
php-mbstring
php-xml
php-curl
php-zip
php-gd
php-intl
php-bcmath
php-fileinfo
```

### Development Tools (Optional)

-   **Git**: Version control
-   **Postman/Insomnia**: API testing
-   **MySQL Workbench**: Database management
-   **VS Code/Cursor**: Code editor with PHP extensions

---

## Installation

### Step 1: Clone the Repository

```bash
# Clone the repository
git clone https://github.com/YOUR_USERNAME/riparo-backend.git

# Navigate to the project directory
cd riparo-backend
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Configuration

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=riparo_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Run Database Migrations

```bash
# Run database migrations
php artisan migrate

# Seed initial data (optional)
php artisan db:seed
```

### Step 6: Storage Configuration

```bash
# Create storage link
php artisan storage:link
```

### Step 7: Start Development Server

```bash
# Start Laravel development server
php artisan serve
```

The API will be available at `http://localhost:8000`

---

## Configuration

### Environment Variables

Key environment variables in `.env`:

```env
# Application
APP_NAME="RIPARO Backend"
APP_ENV=local
APP_KEY=base64:your_generated_key
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=riparo_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# File Storage
FILESYSTEM_DISK=local
```

### CORS Configuration

Configure CORS in `config/cors.php`:

```php
'allowed_origins' => [
    'http://localhost:5173', // Frontend development URL
    'https://yourdomain.com', // Production frontend URL
],
```

### File Upload Limits

Configure upload limits in `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
```

---

## Database Setup

### Database Schema

The application uses the following main tables:

-   **users**: Citizen and admin user accounts
-   **reports**: Citizen-submitted reports
-   **citizen_feedback**: Feedback from citizens
-   **user_update_requests**: Profile update requests
-   **personal_access_tokens**: API authentication tokens

### Running Migrations

```bash
# Run all migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset all migrations
php artisan migrate:reset

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### Database Seeding

```bash
# Run seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=DatabaseSeeder
```

---

## API Documentation

### Base URL

```
http://localhost:8000/api
```

### Authentication Endpoints

#### Register User

```http
POST /api/register
Content-Type: multipart/form-data

{
    "first_name": "Juan",
    "last_name": "Dela Cruz",
    "email": "juan@example.com",
    "password": "password123",
    "mobile_number": "09123456789",
    "barangay": "San Jose",
    "zone": "Zone 1",
    "id_document": [file]
}
```

#### Login

```http
POST /api/login
Content-Type: application/json

{
    "email": "juan@example.com",
    "password": "password123"
}
```

#### Logout

```http
POST /api/logout
Authorization: Bearer {token}
```

### Report Endpoints

#### Submit Report

```http
POST /api/reports
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "submitter_name": "Juan Dela Cruz",
    "age": 30,
    "gender": "Male",
    "address": "123 Main St, San Jose",
    "type": "infrastructure",
    "description": "Pothole on main road",
    "photos[]": [files]
}
```

#### Get Reports

```http
GET /api/reports?page=1&per_page=10&status=pending
Authorization: Bearer {token}
```

#### Update Report Progress

```http
PUT /api/reports/{id}/progress
Authorization: Bearer {token}
Content-Type: application/json

{
    "progress": "assigned"
}
```

### User Management Endpoints

#### Get Users

```http
GET /api/users?status=pending
Authorization: Bearer {token}
```

#### Update User Status

```http
PUT /api/users/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "verification_status": "verified"
}
```

### Feedback Endpoints

#### Submit Feedback

```http
POST /api/feedback
Authorization: Bearer {token}
Content-Type: application/json

{
    "subject": "Suggestion",
    "message": "Great platform!",
    "anonymous": false,
    "contact_email": "juan@example.com"
}
```

---

## Authentication

### JWT Token Authentication

The API uses Laravel Sanctum for token-based authentication:

1. **Login**: Receive JWT token
2. **Include Token**: Add `Authorization: Bearer {token}` header
3. **Token Expiry**: Tokens expire after 24 hours (configurable)

### Role-Based Access Control

#### User Roles

-   **citizen**: Can submit reports and feedback
-   **admin**: Can manage reports and users
-   **mayor**: Can view analytics and manage users

#### Permission Levels

```php
// Citizen permissions
- Submit reports
- View own reports
- Submit feedback
- Update own profile

// Admin permissions
- View all reports
- Update report status
- Manage users
- View feedback

// Mayor permissions
- View analytics
- Manage all users
- View all data
```

---

## User Management

### User Registration Process

1. **Citizen Registration**:

    - Submit personal information
    - Upload valid ID document
    - Account status: `pending`

2. **Admin Review**:

    - Review submitted information
    - Verify ID document
    - Approve or reject account

3. **Account Activation**:
    - Status changes to `verified`
    - User can access full features

### User Verification States

-   **pending**: Awaiting admin review
-   **verified**: Approved and active
-   **rejected**: Rejected by admin

### Profile Update Requests

Citizens can request profile updates:

1. Submit update request with new information
2. Upload new ID document
3. Admin reviews and approves/rejects
4. Profile updated upon approval

---

## Report Management

### Report Lifecycle

1. **Submission**: Citizen submits report with photos
2. **Review**: Admin reviews report details
3. **Assignment**: Report assigned to staff member
4. **Resolution**: Issue resolved with photos
5. **Completion**: Report marked as resolved

### Report Statuses

-   **pending**: Newly submitted
-   **in_review**: Under admin review
-   **assigned**: Assigned to staff
-   **resolved**: Issue resolved
-   **rejected**: Report rejected

### Report Categories

-   **infrastructure**: Roads, bridges, buildings
-   **sanitation**: Waste management, cleanliness
-   **community_welfare**: Social services, health
-   **behavoural_concerns**: Public behavior issues

---

## File Storage

### File Upload Security

-   **File Type Validation**: Only images allowed
-   **Size Limits**: Maximum 10MB per file
-   **Virus Scanning**: Basic file validation
-   **Secure Storage**: Files stored outside web root

### Storage Structure

```
storage/
├── app/
│   ├── public/
│   │   ├── reports/          # Report photos
│   │   ├── id_documents/     # User ID documents
│   │   └── resolution/       # Resolution photos
│   └── private/              # Private files
```

### File Access

-   **Public Files**: Accessible via URL
-   **Private Files**: Require authentication
-   **CDN Support**: Ready for cloud storage

---

## Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=AuthTest

# Run with coverage
php artisan test --coverage
```

### Test Structure

```
tests/
├── Feature/          # Integration tests
│   ├── AuthTest.php
│   ├── ReportTest.php
│   └── UserTest.php
└── Unit/            # Unit tests
    ├── UserTest.php
    └── ReportTest.php
```

### API Testing

Use Postman or similar tools to test API endpoints:

1. **Import Collection**: Use provided Postman collection
2. **Set Environment**: Configure base URL and tokens
3. **Run Tests**: Execute test scenarios

---

## Deployment

### Production Checklist

-   [ ] Environment variables configured
-   [ ] Database migrated and seeded
-   [ ] SSL certificate installed
-   [ ] File permissions set correctly
-   [ ] Queue workers configured
-   [ ] Log rotation set up
-   [ ] Backup strategy implemented
-   [ ] Monitoring configured

### Deployment Steps

1. **Server Setup**:

    ```bash
    # Install PHP and extensions
    sudo apt update
    sudo apt install php8.1-fpm php8.1-mysql php8.1-mbstring

    # Install Composer
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    ```

2. **Application Deployment**:

    ```bash
    # Clone repository
    git clone https://github.com/YOUR_USERNAME/riparo-backend.git
    cd riparo-backend

    # Install dependencies
    composer install --optimize-autoloader --no-dev

    # Configure environment
    cp .env.example .env
    php artisan key:generate

    # Run migrations
    php artisan migrate --force

    # Create storage link
    php artisan storage:link

    # Cache configuration
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

3. **Web Server Configuration**:
    ```nginx
    # Nginx configuration
    server {
        listen 80;
        server_name yourdomain.com;
        root /path/to/riparo-backend/public;

        index index.php;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }
    ```

---

## Troubleshooting

### Common Issues

#### 1. "Class not found" errors

**Problem**: Missing dependencies
**Solution**:

```bash
composer install
composer dump-autoload
```

#### 2. Database connection errors

**Problem**: Database configuration issues
**Solution**:

-   Check database credentials in `.env`
-   Verify database server is running
-   Test connection: `php artisan tinker`

#### 3. File upload errors

**Problem**: File upload limits exceeded
**Solution**:

-   Increase `upload_max_filesize` in `php.ini`
-   Check file permissions on storage directory
-   Verify disk space availability

#### 4. CORS errors

**Problem**: Frontend cannot access API
**Solution**:

-   Configure CORS in `config/cors.php`
-   Add frontend URL to allowed origins
-   Check API URL in frontend configuration

#### 5. Token authentication errors

**Problem**: JWT token issues
**Solution**:

-   Verify token format and expiration
-   Check Sanctum configuration
-   Ensure proper headers are sent

### Debugging Tools

1. **Laravel Telescope**: Install for debugging
2. **Log Files**: Check `storage/logs/laravel.log`
3. **Artisan Commands**: Use `php artisan` for debugging
4. **Database Queries**: Enable query logging

---

## Contributing

We welcome contributions to improve RIPARO Backend! Here's how you can help:

### Development Setup

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes
4. Write tests for new functionality
5. Ensure all tests pass: `php artisan test`
6. Commit your changes: `git commit -m 'Add amazing feature'`
7. Push to the branch: `git push origin feature/amazing-feature`
8. Open a Pull Request

### Code Standards

-   Follow PSR-12 coding standards
-   Use meaningful variable and function names
-   Add PHPDoc comments for complex functions
-   Write comprehensive tests
-   Update documentation for new features

### Testing Requirements

-   Unit tests for new models and services
-   Feature tests for new API endpoints
-   Integration tests for complex workflows
-   Maintain test coverage above 80%

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

<div align="center">

**RIPARO Backend** - Empowering communities through robust technology

Built with ❤️ for Local Government Units in the Philippines

[Report an Issue](https://github.com/YOUR_USERNAME/riparo-backend/issues) • [Request a Feature](https://github.com/YOUR_USERNAME/riparo-backend/issues) • [View Frontend](https://github.com/YOUR_USERNAME/riparo-frontend)

</div>
