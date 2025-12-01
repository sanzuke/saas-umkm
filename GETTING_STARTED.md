# 🚀 Getting Started with SaaS UMKM

Welcome! This guide will help you set up and run your SaaS UMKM project.

## 📋 Prerequisites

Before you begin, make sure you have installed:

- **Docker Desktop** (Windows/Mac) or **Docker Engine** (Linux)
  - Download: https://www.docker.com/products/docker-desktop
- **Git**
  - Download: https://git-scm.com/downloads
- **A code editor** (VS Code recommended)
  - Download: https://code.visualstudio.com/

## 🎯 Quick Start (5 minutes)

### Step 1: Clone the Repository

```bash
# Clone from GitHub
git clone https://github.com/sanzuke/saas-umkm.git
cd saas-umkm
```

### Step 2: Run Setup Script

**On macOS/Linux:**
```bash
chmod +x setup.sh
./setup.sh
```

**On Windows (Git Bash):**
```bash
bash setup.sh
```

**Manual Setup (if script doesn't work):**
```bash
# Start containers
docker-compose up -d

# Wait 10 seconds for PostgreSQL to start
sleep 10

# Setup backend
docker-compose exec backend bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
exit
```

### Step 3: Access the Application

Open your browser and go to:

- **Backend API**: http://localhost:8000
- **Database**: localhost:5432 (PostgreSQL)

### Step 4: Test Login

Use these credentials to test:

```
Admin Account:
Email: admin@demo.com
Password: password

Staff Account:
Email: staff@demo.com
Password: password
```

## 📁 Project Structure

```
saas-umkm/
├── apps/
│   ├── backend/              # Laravel API
│   │   ├── app/
│   │   │   ├── Models/      # Database models
│   │   │   ├── Http/
│   │   │   │   └── Controllers/
│   │   │   └── Services/
│   │   ├── database/
│   │   │   ├── migrations/  # Database schema
│   │   │   └── seeders/     # Sample data
│   │   └── routes/
│   │       └── api.php
│   │
│   └── frontend/            # Next.js (to be created)
│
├── docker/
│   ├── backend.Dockerfile
│   └── frontend.Dockerfile
│
├── docker-compose.yml       # Docker configuration
├── setup.sh                 # Quick setup script
├── README.md               # Main documentation
└── PROJECT_SUMMARY.md      # Development roadmap
```

## 🔧 Development Workflow

### Working with Backend (Laravel)

```bash
# Enter backend container
docker-compose exec backend bash

# Run migrations
php artisan migrate

# Create new migration
php artisan make:migration create_products_table

# Create new model
php artisan make:model Product -m

# Create new controller
php artisan make:controller ProductController

# Run tests
php artisan test

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Working with Database

```bash
# Access PostgreSQL
docker-compose exec postgres psql -U postgres -d saas_umkm

# View tables
\dt

# Query data
SELECT * FROM users;

# Exit
\q
```

### Docker Commands

```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# View logs
docker-compose logs -f backend

# Restart a service
docker-compose restart backend

# Rebuild containers
docker-compose up -d --build
```

## 🗃️ Database Overview

### Core Tables

1. **tenants** - Corporate entities
2. **groups** - Organization hierarchy (Corporate → Company → BU)
3. **users** - User accounts
4. **roles** - User roles
5. **permissions** - Access permissions
6. **subscriptions** - Business unit subscriptions
7. **modules** - Available features (POS, Inventory, etc.)

### Sample Data

The demo seeder creates:
- 1 Corporate: "Demo Corporation"
- 1 Company: "Demo Retail Division"
- 1 Business Unit: "Jakarta Store"
- 2 Users: Admin and Staff
- 4 Modules: POS, Inventory, Workshop, Garment
- 40+ Permissions

## 🎯 Next Steps

### Phase 1: Complete Backend (Current)

You need to create:

1. **Controllers** (`apps/backend/app/Http/Controllers/`)
   - AuthController.php
   - GroupController.php
   - UserController.php
   - RoleController.php
   - SubscriptionController.php

2. **Middleware** (`apps/backend/app/Http/Middleware/`)
   - TenantMiddleware.php
   - PermissionMiddleware.php

3. **Routes** (`apps/backend/routes/api.php`)
   - Define all API endpoints

4. **Configuration**
   - Configure Sanctum CORS
   - Update .env.example

### Phase 2: Create Frontend

```bash
# Create Next.js app
cd apps/
npx create-next-app@latest frontend --typescript --app --tailwind

# Install UI library
cd frontend
npx shadcn-ui@latest init

# Install dependencies
npm install axios swr react-hook-form zod
```

## 🆘 Troubleshooting

### Port Already in Use

```bash
# Check what's using port 8000
lsof -i :8000  # macOS/Linux
netstat -ano | findstr :8000  # Windows

# Kill the process or change port in docker-compose.yml
```

### Permission Errors

```bash
# Fix Laravel permissions
docker-compose exec backend bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Database Connection Failed

```bash
# Restart PostgreSQL
docker-compose restart postgres

# Check if PostgreSQL is running
docker-compose ps

# View PostgreSQL logs
docker-compose logs postgres
```

### Composer Install Fails

```bash
# Clear Composer cache
docker-compose exec backend composer clear-cache

# Update Composer
docker-compose exec backend composer self-update

# Try install again
docker-compose exec backend composer install
```

## 📚 Documentation

- **README.md** - Main documentation
- **PROJECT_SUMMARY.md** - Development roadmap and next steps
- **apps/backend/README.md** - Backend-specific documentation

## 🤝 Need Help?

If you encounter any issues:

1. Check the logs: `docker-compose logs -f`
2. Verify all services are running: `docker-compose ps`
3. Try restarting: `docker-compose restart`
4. Rebuild containers: `docker-compose up -d --build`

## 📞 Support

For questions or issues, create an issue on GitHub:
https://github.com/sanzuke/saas-umkm/issues

---

**Happy Coding! 🚀**
