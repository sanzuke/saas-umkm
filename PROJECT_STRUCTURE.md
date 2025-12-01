# 📁 Project Structure - SaaS UMKM

## Overview Directory Tree

```
saas-umkm/
│
├── 📄 README.md                    # Dokumentasi utama
├── 📄 QUICKSTART.md                # Panduan cepat setup
├── 📄 ARCHITECTURE.md              # Dokumentasi arsitektur
├── 📄 .gitignore                   # Git ignore rules
├── 🐳 docker-compose.yml           # Docker orchestration
├── 🔧 setup.sh                     # Auto setup script
│
├── 📂 backend/                     # Laravel 11 API
│   ├── 📂 app/
│   │   ├── 📂 Http/
│   │   │   ├── 📂 Controllers/
│   │   │   │   └── 📂 Api/
│   │   │   │       ├── AuthController.php      # Authentication
│   │   │   │       └── GroupController.php     # Organization management
│   │   │   └── 📂 Middleware/
│   │   │       ├── TenantMiddleware.php        # (TODO) Multi-tenant
│   │   │       └── PermissionMiddleware.php    # (TODO) Authorization
│   │   │
│   │   ├── 📂 Models/
│   │   │   ├── User.php                        # ✅ User model with permissions
│   │   │   ├── Group.php                       # ✅ Organization hierarchy
│   │   │   ├── Role.php                        # ✅ Role management
│   │   │   ├── Permission.php                  # ✅ Permission system
│   │   │   └── Subscription.php                # ✅ Plans & subscriptions
│   │   │
│   │   └── 📂 Services/                        # (TODO) Business logic
│   │       ├── GroupService.php
│   │       ├── UserService.php
│   │       └── PermissionService.php
│   │
│   ├── 📂 database/
│   │   ├── 📂 migrations/
│   │   │   ├── 2024_01_01_000001_create_groups_table.php           # ✅
│   │   │   ├── 2024_01_01_000002_create_roles_table.php            # ✅
│   │   │   ├── 2024_01_01_000003_create_permissions_table.php      # ✅
│   │   │   ├── 2024_01_01_000004_create_role_permission_table.php  # ✅
│   │   │   ├── 2024_01_01_000005_create_users_table.php            # ✅
│   │   │   ├── 2024_01_01_000006_create_group_user_table.php       # ✅
│   │   │   └── 2024_01_01_000007_create_subscriptions_table.php    # ✅
│   │   │
│   │   ├── 📂 seeders/
│   │   │   └── DatabaseSeeder.php              # ✅ Initial data
│   │   │
│   │   └── 📂 factories/                       # (TODO) Test data generators
│   │
│   ├── 📂 routes/
│   │   └── api.php                             # ✅ API routes definition
│   │
│   ├── 📂 config/
│   │   ├── cors.php                            # ✅ CORS configuration
│   │   └── (other Laravel configs)
│   │
│   ├── 📄 .env.example                         # ✅ Environment template
│   ├── 📄 composer.json                        # ✅ PHP dependencies
│   └── 🐳 Dockerfile                           # ✅ Backend container config
│
└── 📂 frontend/                    # Next.js 14 App
    ├── 📂 app/                     # App Router (Next.js 14)
    │   ├── 📄 layout.tsx                       # ✅ Root layout
    │   ├── 📄 page.tsx                         # ✅ Home/redirect page
    │   ├── 📄 providers.tsx                    # ✅ React Query provider
    │   ├── 📄 globals.css                      # ✅ Global styles
    │   │
    │   ├── 📂 login/
    │   │   └── page.tsx                        # ✅ Login page
    │   │
    │   ├── 📂 register/
    │   │   └── page.tsx                        # ✅ Registration page
    │   │
    │   └── 📂 dashboard/
    │       ├── layout.tsx                      # ✅ Dashboard layout + sidebar
    │       ├── page.tsx                        # ✅ Main dashboard
    │       │
    │       ├── 📂 organizations/               # (TODO) Organization management
    │       │   └── page.tsx
    │       │
    │       ├── 📂 users/                       # (TODO) User management
    │       │   └── page.tsx
    │       │
    │       ├── 📂 modules/                     # Business modules
    │       │   ├── 📂 inventory/               # (TODO) Inventory module
    │       │   ├── 📂 pos/                     # (TODO) POS module
    │       │   ├── 📂 workshop/                # (TODO) Workshop module
    │       │   └── 📂 garment/                 # (TODO) Garment module
    │       │
    │       └── 📂 settings/                    # (TODO) Settings
    │           └── page.tsx
    │
    ├── 📂 components/
    │   └── 📂 ui/                  # Shadcn/ui components
    │       ├── button.tsx                      # ✅ Button component
    │       ├── input.tsx                       # ✅ Input component
    │       └── card.tsx                        # ✅ Card component
    │
    ├── 📂 lib/
    │   ├── api.ts                              # ✅ API client (Axios)
    │   ├── auth-store.ts                       # ✅ Auth state (Zustand)
    │   └── utils.ts                            # ✅ Utility functions
    │
    ├── 📂 types/
    │   └── index.ts                            # ✅ TypeScript interfaces
    │
    ├── 📄 .env.local                           # ✅ Frontend environment
    ├── 📄 package.json                         # ✅ Node dependencies
    ├── 📄 next.config.mjs                      # ✅ Next.js configuration
    ├── 📄 tsconfig.json                        # ✅ TypeScript config
    ├── 📄 tailwind.config.ts                   # ✅ Tailwind CSS config
    ├── 📄 postcss.config.js                    # ✅ PostCSS config
    └── 🐳 Dockerfile                           # ✅ Frontend container config
```

## 📊 File Statistics

### Completed Files ✅
- **Backend**: 18 files
  - 7 Migrations
  - 5 Models
  - 2 Controllers
  - 1 Seeder
  - 1 Routes
  - 1 Config
  - 1 Dockerfile

- **Frontend**: 16 files
  - 7 Pages (App Router)
  - 3 UI Components
  - 3 Lib files (API, Store, Utils)
  - 1 Types file
  - 2 Config files (Next.js, Tailwind)

- **DevOps**: 4 files
  - Docker Compose
  - Setup script
  - Gitignore
  - Documentation (3 MD files)

**Total**: **38 files** created and configured!

## 🎯 Feature Status

### ✅ Completed (MVP - Phase 1)
- [x] Docker setup (Compose + Dockerfiles)
- [x] Database schema (7 tables)
- [x] Laravel Models with relationships
- [x] Authentication system (Register/Login/Logout)
- [x] Multi-tenant architecture
- [x] Hierarchical organization (3 levels)
- [x] Role & Permission system
- [x] API endpoints (Auth + Groups)
- [x] Next.js setup with TypeScript
- [x] Shadcn/ui components
- [x] React Query integration
- [x] Zustand state management
- [x] Auth pages (Login/Register)
- [x] Dashboard layout with sidebar
- [x] Main dashboard page
- [x] API client with Axios
- [x] Type definitions

### 🚧 TODO (Phase 2)
- [ ] Complete organization management UI
- [ ] User management CRUD
- [ ] Role assignment interface
- [ ] Permission management UI
- [ ] Subscription plans interface
- [ ] Middleware for tenant context
- [ ] Middleware for permission checks
- [ ] Service classes for business logic
- [ ] Unit tests (PHPUnit)
- [ ] E2E tests (Playwright)

### 📅 Planned (Phase 3)
- [ ] Inventory module (Products, Stock, Categories)
- [ ] POS module (Transactions, Cashier, Reports)
- [ ] Workshop module (Service orders, Mechanics, Parts)
- [ ] Garment module (Orders, Production, Delivery)
- [ ] Advanced reporting dashboard
- [ ] Multi-language support
- [ ] Email notifications
- [ ] Webhook system
- [ ] Mobile app (React Native)

## 🔌 API Endpoints (Current)

### Authentication
```
POST   /api/register         - Register new user + organization
POST   /api/login            - Login user
POST   /api/logout           - Logout user
GET    /api/me               - Get current user info
POST   /api/refresh          - Refresh auth token
```

### Groups (Organization)
```
GET    /api/groups           - Get organization hierarchy
POST   /api/groups           - Create new company/BU
GET    /api/groups/{id}      - Get group details
PUT    /api/groups/{id}      - Update group
DELETE /api/groups/{id}      - Delete group
GET    /api/groups/{id}/users - Get users in group
```

## 🗄️ Database Tables (Current)

1. **groups** - Organization hierarchy (Corporate/Company/BU)
2. **users** - User accounts
3. **roles** - Roles per group
4. **permissions** - Module permissions
5. **role_permission** - Many-to-many pivot
6. **group_user** - User-Group with roles
7. **subscriptions** - BU subscriptions
8. **subscription_plans** - Available plans

## 🎨 UI Components Available

### Shadcn/ui Components
- ✅ Button (variants: default, destructive, outline, secondary, ghost, link)
- ✅ Input (text, email, password, etc)
- ✅ Card (with Header, Content, Footer)

### Layout Components
- ✅ Dashboard Layout (with sidebar navigation)
- ✅ Auth Layout (centered forms)

### Pages
- ✅ Login Page
- ✅ Register Page
- ✅ Dashboard Home
- 🚧 Organizations Page (TODO)
- 🚧 Users Page (TODO)
- 🚧 Settings Page (TODO)

## 🔐 Security Features Implemented

1. **Authentication**
   - ✅ Laravel Sanctum token-based auth
   - ✅ Password hashing with Bcrypt
   - ✅ Token stored in localStorage (client)

2. **Authorization**
   - ✅ Permission check methods in User model
   - ✅ Role inheritance system
   - ✅ Tenant isolation via tenant_id

3. **Data Protection**
   - ✅ CORS configured for Next.js
   - ✅ Eloquent ORM (SQL injection prevention)
   - ✅ React XSS protection (default escaping)

4. **TODO**
   - [ ] Rate limiting on login
   - [ ] CSRF token validation
   - [ ] Permission middleware
   - [ ] Audit logging

## 📱 Responsive Design

Current implementation:
- ✅ Desktop layout (sidebar visible)
- ✅ Tablet layout (hamburger menu placeholder)
- 🚧 Mobile optimization (TODO)

## 🌐 Browser Support

Target browsers:
- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)

## 📦 Dependencies

### Backend (Laravel)
- Laravel 11
- Laravel Sanctum
- PostgreSQL Driver
- Redis Client

### Frontend (Next.js)
- Next.js 14
- React 18
- TypeScript 5
- Tailwind CSS 3
- Shadcn/ui
- React Query 5
- Zustand 4
- Axios 1.7

## 🔄 State Management

- **Global Auth**: Zustand (`useAuthStore`)
- **Server Data**: React Query (caching, refetching)
- **Local UI**: React useState/useEffect

## 🎯 Performance Considerations

### Current
- ✅ React Query caching (1 min stale time)
- ✅ Code splitting (Next.js automatic)
- ✅ Image optimization ready (Next.js Image)

### TODO
- [ ] Database indexing optimization
- [ ] API response compression
- [ ] Redis caching for frequently accessed data
- [ ] CDN for static assets
- [ ] Lazy loading for module components

## 📝 Code Quality

### Implemented
- ✅ TypeScript for type safety
- ✅ ESLint configuration
- ✅ Consistent code structure

### TODO
- [ ] Unit tests (PHPUnit for backend)
- [ ] Component tests (Jest + RTL for frontend)
- [ ] E2E tests (Playwright)
- [ ] Code coverage reports
- [ ] CI/CD pipeline

---

**Last Updated**: November 2024
**Version**: 0.1.0 (MVP - Phase 1)
