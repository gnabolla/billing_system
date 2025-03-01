# Multi-Tenant Billing Platform

A PHP-based billing platform that allows multiple companies to manage their own subscribers, plans, statements, and payments.

## Features

- Multi-tenant architecture
- User authentication and authorization
- Company registration and management
- Subscriber management
- Plan management
- Statement generation
- Payment processing
- Company-specific settings
- Role-based access control

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)

## Setup Instructions

### 1. Database Setup

1. Create a MySQL database:
   ```sql
   CREATE DATABASE billing_db;
   ```

2. Configure database connection in `config.php`:
   ```php
   return [
       'database' => [
           'host' => 'localhost',
           'port' => 3306,
           'dbname' => 'billing_db',
           'charset' => 'utf8mb4'
       ],
   ];
   ```

3. Run the database initialization script:
   ```bash
   php init_db.php
   ```

### 2. Web Server Configuration

#### Apache

Create a `.htaccess` file in the project root:
```
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

#### Nginx

Add this to your server configuration:
```
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 3. Application Setup

1. Clone the repository to your web server directory.
2. Make sure the webserver has write permissions to the necessary directories.
3. Visit the application URL in your browser.
4. Register your first company and admin user.

## Directory Structure

```
/
├── controllers/        # Controller files
│   ├── dashboard.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   └── register.php
├── models/             # Database models
│   ├── Company.php
│   └── User.php
├── services/           # Service classes
│   └── Auth.php
├── views/              # View templates
│   ├── partials/       # Reusable view components
│   │   ├── foot.php
│   │   ├── head.php
│   │   ├── nav.php
│   │   └── sidebar.php
│   ├── dashboard.view.php
│   ├── login.view.php
│   └── register.view.php
├── Database.php        # Database connection class
├── config.php          # Configuration file
├── functions.php       # Helper functions
├── index.php           # Entry point
├── init_db.php         # Database initialization script
└── router.php          # URL routing
```

## Development Roadmap

The next development phases will include:

1. Subscriber management (CRUD operations)
2. Plan management
3. Statement generation
4. Payment recording
5. Reporting and analytics
6. API development
7. Third-party integrations

## License

[MIT License](LICENSE)