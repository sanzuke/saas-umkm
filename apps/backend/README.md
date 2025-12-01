# Laravel Backend - SaaS UMKM

Multi-tenant API backend for SaaS UMKM platform.

## 🚀 Setup

### Using Docker (Recommended)
```bash
# From project root
docker-compose up -d

# Enter backend container
docker-compose exec backend bash

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### Manual Setup
```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Configure database in .env
# DB_CONNECTION=pgsql
# DB_HOST=localhost
# DB_PORT=5432
# DB_DATABASE=saas_umkm
# DB_USERNAME=postgres
# DB_PASSWORD=postgres

# Run migrations
php artisan migrate

# Start server
php artisan serve
```

## 📊 Database Structure

### Multi-tenant Architecture

The application uses a **shared database, shared schema** approach with tenant isolation via `tenant_id`.

### Core Tables

1. **tenants** - Root tenant (corporate level)
2. **groups** - Hierarchical organization structure (Corporate → Company → Business Unit)
3. **users** - User accounts
4. **group_user** - Many-to-many relationship with roles
5. **roles** - Role definitions per group
6. **permissions** - Permission definitions
7. **role_permission** - Many-to-many relationship
8. **subscriptions** - Subscription management per business unit
9. **modules** - Available modules (POS, Inventory, etc.)

## 🔐 Authentication

Using **Laravel Sanctum** for API token authentication.

### Endpoints

```
POST /api/register - Register new user
POST /api/login - Login user
POST /api/logout - Logout user
GET /api/user - Get authenticated user
```

## 🎯 Key Features

- Multi-tenant architecture with tenant isolation
- 3-level hierarchical organization (Corporate → Company → Business Unit)
- Role-based access control (RBAC)
- Permission inheritance from parent groups
- Module-based subscription management
- Middleware for tenant context and permissions

## 📝 API Endpoints

### Authentication
- `POST /api/register` - Register new tenant/user
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get current user with groups and roles

### Groups (Organizations)
- `GET /api/groups` - List all groups in hierarchy
- `POST /api/groups` - Create new group
- `GET /api/groups/{id}` - Get group details
- `PUT /api/groups/{id}` - Update group
- `DELETE /api/groups/{id}` - Delete group
- `GET /api/groups/{id}/children` - Get child groups
- `GET /api/groups/{id}/users` - Get users in group

### Users
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `GET /api/users/{id}` - Get user details
- `PUT /api/users/{id}` - Update user
- `DELETE /api/users/{id}` - Delete user
- `POST /api/users/{id}/groups` - Assign user to groups
- `POST /api/users/{id}/roles` - Assign roles to user

### Roles
- `GET /api/roles` - List roles
- `POST /api/roles` - Create role
- `GET /api/roles/{id}` - Get role details
- `PUT /api/roles/{id}` - Update role
- `DELETE /api/roles/{id}` - Delete role
- `POST /api/roles/{id}/permissions` - Assign permissions

### Permissions
- `GET /api/permissions` - List all permissions
- `GET /api/permissions/user` - Get current user permissions

### Subscriptions
- `GET /api/subscriptions` - List subscriptions
- `POST /api/subscriptions` - Create subscription
- `GET /api/subscriptions/{id}` - Get subscription details
- `PUT /api/subscriptions/{id}` - Update subscription
- `DELETE /api/subscriptions/{id}` - Cancel subscription

## 🛠️ Artisan Commands

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Create new migration
php artisan make:migration create_table_name

# Create new model
php artisan make:model ModelName -m

# Create new controller
php artisan make:controller ControllerName

# Create new middleware
php artisan make:middleware MiddlewareName

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 🔒 Middleware

### TenantMiddleware
Ensures all requests are scoped to the correct tenant based on authenticated user.

### PermissionMiddleware
Checks if user has required permissions for the action.

Usage:
```php
Route::middleware(['auth:sanctum', 'tenant', 'permission:module.action'])
    ->group(function () {
        // Protected routes
    });
```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter TestName

# Generate coverage report
php artisan test --coverage
```

## 📦 Key Packages

- `laravel/sanctum` - API authentication
- `spatie/laravel-permission` - Role and permission management (alternative approach)
- Custom RBAC implementation for multi-tenant support

## 🔧 Configuration

### CORS
Configured in `config/cors.php` to allow requests from Next.js frontend.

### Sanctum
Configured in `config/sanctum.php` with stateful domains for SPA authentication.

## 📚 Models & Relationships

### Tenant
```php
hasMany(Group::class)
hasMany(User::class)
```

### Group
```php
belongsTo(Tenant::class)
belongsTo(Group::class, 'parent_id') // Self-referencing
hasMany(Group::class, 'parent_id') // Children
belongsToMany(User::class)->withPivot('roles')
hasMany(Role::class)
hasMany(Subscription::class)
```

### User
```php
belongsTo(Tenant::class)
belongsToMany(Group::class)->withPivot('roles')
```

### Role
```php
belongsTo(Group::class)
belongsToMany(Permission::class)
```

### Permission
```php
belongsToMany(Role::class)
```

### Subscription
```php
belongsTo(Group::class)
belongsToMany(Module::class)
```

## 🚀 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper database credentials
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Set proper file permissions
- [ ] Configure queue workers
- [ ] Setup scheduled tasks (cron)

## 📄 License

Proprietary and confidential.
