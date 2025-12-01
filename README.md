# SaaS UMKM - Multi-tenant Business Management Platform

A comprehensive SaaS platform for Indonesian SMEs (UMKM) with multi-module support including Retail Inventory, POS, Workshop, and Garment management.

## 🏗️ Architecture

- **Backend**: Laravel 11 (API)
- **Frontend**: Next.js 14 (App Router)
- **Database**: PostgreSQL 16
- **Auth**: Laravel Sanctum
- **Deployment**: Monorepo structure

## 📁 Project Structure

```
saas-umkm/
├── apps/
│   ├── backend/          # Laravel API
│   └── frontend/         # Next.js Dashboard
├── docker/               # Docker configurations
├── packages/             # Shared packages (optional)
└── docker-compose.yml    # Local development setup
```

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### Setup

1. **Clone repository**
```bash
git clone https://github.com/sanzuke/saas-umkm.git
cd saas-umkm
```

2. **Start development environment**
```bash
docker-compose up -d
```

3. **Backend setup**
```bash
# Enter backend container
docker-compose exec backend bash

# Install dependencies
composer install

# Run migrations
php artisan migrate

# Generate app key
php artisan key:generate
```

4. **Frontend setup**
```bash
# Enter frontend container
docker-compose exec frontend bash

# Install dependencies
npm install

# Run dev server (auto-started by docker-compose)
npm run dev
```

5. **Access applications**
- Frontend: http://localhost:3000
- Backend API: http://localhost:8000
- PostgreSQL: localhost:5432

## 📊 Database Schema

### Core Entities
- **Groups**: 3-level hierarchy (Corporate → Company → Business Unit)
- **Users**: Multi-group, multi-role support
- **Roles**: Per business unit with inheritance
- **Permissions**: Module-based (inventory, pos, workshop, garment)
- **Subscriptions**: Per business unit with module activation

## 🔐 Authentication Flow

1. User registers/logs in
2. Backend issues Sanctum token
3. Frontend stores token in httpOnly cookie
4. All API requests include token
5. Backend validates tenant context

## 🎯 Features (MVP)

- [x] Multi-tenant architecture
- [x] 3-level organizational hierarchy
- [x] Role-based access control (RBAC)
- [x] Permission inheritance
- [x] Subscription management per BU
- [x] Module activation system
- [ ] POS Retail module (Phase 3)
- [ ] Inventory management (Phase 3)
- [ ] Workshop management (Future)
- [ ] Garment/Konveksi management (Future)

## 🛠️ Development

### Backend (Laravel)
```bash
cd apps/backend

# Run tests
php artisan test

# Create migration
php artisan make:migration create_table_name

# Create controller
php artisan make:controller ControllerName

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### Frontend (Next.js)
```bash
cd apps/frontend

# Run dev server
npm run dev

# Build for production
npm run build

# Run tests
npm run test

# Type check
npm run type-check
```

## 📝 API Documentation

API documentation will be available at: http://localhost:8000/api/documentation

### Key Endpoints

**Auth**
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get current user

**Groups**
- `GET /api/groups` - List groups (hierarchy)
- `POST /api/groups` - Create group
- `GET /api/groups/{id}` - Get group details
- `PUT /api/groups/{id}` - Update group
- `DELETE /api/groups/{id}` - Delete group

**Users**
- `GET /api/users` - List users
- `POST /api/users` - Create user
- `PUT /api/users/{id}` - Update user
- `POST /api/users/{id}/roles` - Assign roles

**Subscriptions**
- `GET /api/subscriptions` - List subscriptions
- `POST /api/subscriptions` - Create subscription
- `PUT /api/subscriptions/{id}` - Update subscription

## 🔧 Environment Variables

### Backend (.env)
```
APP_NAME="SaaS UMKM"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=saas_umkm
DB_USERNAME=postgres
DB_PASSWORD=postgres

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### Frontend (.env.local)
```
NEXT_PUBLIC_API_URL=http://localhost:8000
NEXT_PUBLIC_APP_NAME="SaaS UMKM"
```

## 📦 Deployment

### Backend (Railway/Fly.io/VPS)
```bash
# Build for production
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Frontend (Vercel)
```bash
# Vercel will auto-detect Next.js
# Set root directory to: apps/frontend
# Build command: npm run build
# Output directory: .next
```

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is proprietary and confidential.

## 👥 Team

- Developer: Sanzuke
- Repository: https://github.com/sanzuke/saas-umkm.git

---

**Status**: 🚧 MVP Development - Phase 1 (Foundation & Authentication)
