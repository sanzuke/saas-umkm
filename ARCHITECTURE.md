# Arsitektur SaaS UMKM

## 📐 Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      Client Browser                          │
│                    (Next.js 14 App)                          │
└──────────────────────┬──────────────────────────────────────┘
                       │ HTTP/HTTPS
                       │ REST API
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                   API Gateway / CORS                         │
│                   (Laravel Sanctum)                          │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                  Application Layer                           │
│                  (Laravel Controllers)                       │
│  ┌──────────────┬──────────────┬──────────────┐            │
│  │   Auth       │   Groups     │   Users      │            │
│  │ Controller   │  Controller  │  Controller  │            │
│  └──────────────┴──────────────┴──────────────┘            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                   Business Logic                             │
│                  (Services & Models)                         │
│  ┌──────────────┬──────────────┬──────────────┐            │
│  │    Group     │     User     │  Permission  │            │
│  │   Service    │   Service    │   Service    │            │
│  └──────────────┴──────────────┴──────────────┘            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Data Layer                                │
│  ┌──────────────────────┬─────────────────────┐            │
│  │    PostgreSQL        │       Redis         │            │
│  │  (Primary Database)  │  (Cache & Queue)    │            │
│  └──────────────────────┴─────────────────────┘            │
└─────────────────────────────────────────────────────────────┘
```

## 🏛️ Multi-Tenant Architecture

### Tenant Isolation Strategy
Menggunakan **Shared Database, Shared Schema** dengan `tenant_id` untuk isolasi data:

```
┌───────────────────────────────────────────────┐
│                   DATABASE                     │
│                                                │
│  ┌──────────────────────────────────────┐    │
│  │  Tenant 1 (Corporate A)              │    │
│  │  tenant_id = 1                       │    │
│  │  ├── Groups (corp, company, BU)      │    │
│  │  ├── Users                           │    │
│  │  └── Data (isolated by tenant_id)    │    │
│  └──────────────────────────────────────┘    │
│                                                │
│  ┌──────────────────────────────────────┐    │
│  │  Tenant 2 (Corporate B)              │    │
│  │  tenant_id = 2                       │    │
│  │  ├── Groups (corp, company, BU)      │    │
│  │  ├── Users                           │    │
│  │  └── Data (isolated by tenant_id)    │    │
│  └──────────────────────────────────────┘    │
└───────────────────────────────────────────────┘
```

**Keuntungan:**
- ✅ Cost-effective (single database)
- ✅ Easy backup & maintenance
- ✅ Efficient resource usage
- ✅ Simple deployment

**Security Measures:**
- Global scopes pada Eloquent Models
- Middleware untuk tenant context
- Row-level security (RLS) di PostgreSQL (optional)

## 🌳 Organization Hierarchy

### 3-Level Hierarchy Structure

```
┌────────────────────────────────────────────┐
│       Corporate (Level 0)                  │
│       tenant_id = self.id                  │
│       type = 'corporate'                   │
└───────────────┬────────────────────────────┘
                │
                ├─────────────────┬─────────────────┐
                │                 │                 │
        ┌───────▼──────┐  ┌───────▼──────┐  ┌───────▼──────┐
        │  Company 1   │  │  Company 2   │  │  Company 3   │
        │  (Level 1)   │  │  (Level 1)   │  │  (Level 1)   │
        │ Cabang JKT   │  │ Cabang SBY   │  │ Cabang BDG   │
        └──────┬───────┘  └──────┬───────┘  └──────┬───────┘
               │                 │                 │
          ┌────┼────┐       ┌────┼────┐       ┌────┼────┐
          │    │    │       │    │    │       │    │    │
      ┌───▼┐ ┌─▼─┐┌─▼─┐ ┌──▼┐ ┌─▼─┐┌─▼─┐ ┌──▼┐ ┌─▼─┐┌─▼─┐
      │ BU1│ │BU2││BU3│ │BU1│ │BU2││BU3│ │BU1│ │BU2││BU3│
      │Toko│ │Gud││Svc│ │Toko│ │Gud││Svc│ │Toko│ │Gud││Svc│
      └────┘ └───┘└───┘ └───┘ └───┘└───┘ └───┘ └───┘└───┘
       L2     L2   L2    L2    L2   L2    L2    L2   L2
```

### Hierarchy Rules
1. **Corporate (Level 0)**
   - Root of organization
   - Acts as tenant for all child groups
   - Cannot be deleted
   - Only one per tenant

2. **Company (Level 1)**
   - Child of Corporate
   - Can have multiple Business Units
   - Represents branch/division

3. **Business Unit (Level 2)**
   - Leaf node (no children allowed)
   - Where subscriptions are assigned
   - Actual operational unit

## 🔐 Permission System

### Permission Inheritance Flow

```
┌─────────────────────────────────────────────────────┐
│              Corporate Admin                        │
│         (has inheritable roles)                     │
│    Can see & manage all child groups                │
└────────────────────┬────────────────────────────────┘
                     │ Inherits down
                     ▼
┌─────────────────────────────────────────────────────┐
│              Company Manager                        │
│    Can see & manage own company + child BUs         │
└────────────────────┬────────────────────────────────┘
                     │ Inherits down
                     ▼
┌─────────────────────────────────────────────────────┐
│            Business Unit Staff                      │
│         Can only access own BU                      │
└─────────────────────────────────────────────────────┘
```

### Permission Check Logic

```php
// User can access if:
1. User is Super Admin (is_super_admin = true)
   OR
2. User belongs to the group
   OR
3. User belongs to any ancestor group (Corporate/Company can see children)
```

### Role Assignment Example

```
User: john@example.com
├── Group: Corporate ABC (tenant)
│   └── Roles: []  (no direct role here)
│
├── Group: Company Jakarta
│   └── Roles: [Manager]  (has company-level permissions)
│
└── Group: BU Toko Utama
    └── Roles: [Cashier, Stock Manager]  (has BU-specific permissions)
```

## 💾 Database Design

### Core Entity Relationships

```
┌──────────┐       ┌──────────┐       ┌──────────────┐
│  Groups  │──────▶│  Users   │──────▶│ Permissions  │
│          │       │          │       │              │
│ id       │       │ id       │       │ id           │
│ name     │       │ name     │       │ name         │
│ type     │       │ email    │       │ slug         │
│ level    │       │ tenant_id│       │ module       │
│ parent_id│       └──────────┘       │ action       │
│ tenant_id│            │             └──────────────┘
└──────────┘            │                     ▲
     ▲                  │                     │
     │                  │                     │
     │            ┌─────▼──────┐             │
     │            │ group_user │             │
     │            │            │             │
     │            │ group_id   │             │
     │            │ user_id    │             │
     └────────────│ role_ids[] │             │
                  └────────────┘             │
                        │                    │
                  ┌─────▼──────┐             │
                  │   Roles    │─────────────┘
                  │            │
                  │ id         │
                  │ name       │
                  │ group_id   │
                  │ is_inherit │
                  └────────────┘
```

### Subscription Model

```
┌──────────────────┐       ┌──────────────────┐
│ SubscriptionPlan │       │  Subscription    │
│                  │       │                  │
│ id               │◀──────│ id               │
│ name             │       │ group_id  (BU)   │
│ modules[]        │       │ plan_id          │
│ features[]       │       │ modules_enabled[]│
│ price            │       │ status           │
│ billing_cycle    │       │ started_at       │
│ max_users        │       │ expires_at       │
└──────────────────┘       └──────────────────┘
```

## 🔄 Request Flow

### Authentication Flow

```
1. User Login
   ↓
2. Laravel Sanctum validates credentials
   ↓
3. Generate API Token
   ↓
4. Frontend stores token in localStorage
   ↓
5. Subsequent requests include token in header:
   Authorization: Bearer {token}
   ↓
6. Middleware validates token
   ↓
7. Attach user to request
   ↓
8. Controller processes request
```

### Data Access Flow

```
1. Request comes with token
   ↓
2. Middleware: Authenticate user
   ↓
3. Controller: Extract user
   ↓
4. Service: Check tenant_id
   ↓
5. Model: Apply global scope (filter by tenant_id)
   ↓
6. Query: WHERE tenant_id = user.tenant_id
   ↓
7. Return: Only tenant's data
```

## 📊 Scalability Considerations

### Current Architecture (MVP)
- **Scale**: Up to 100 concurrent users
- **Database**: Single PostgreSQL instance
- **Cache**: Single Redis instance
- **Server**: Single Docker host

### Future Scaling Options

#### Horizontal Scaling
```
┌────────────┐     ┌────────────┐     ┌────────────┐
│  Laravel   │     │  Laravel   │     │  Laravel   │
│  Instance  │────▶│  Instance  │────▶│  Instance  │
│     1      │     │     2      │     │     3      │
└────────────┘     └────────────┘     └────────────┘
      │                  │                  │
      └──────────────────┼──────────────────┘
                         ▼
              ┌──────────────────┐
              │  Load Balancer   │
              └──────────────────┘
```

#### Database Scaling
```
┌──────────────┐
│   Primary    │──────┐
│  PostgreSQL  │      │ Replication
└──────────────┘      │
                      ▼
              ┌──────────────┐
              │   Replica 1  │
              └──────────────┘
                      │
                      ▼
              ┌──────────────┐
              │   Replica 2  │
              └──────────────┘
```

## 🚀 Deployment Strategy

### Development
- Docker Compose
- Hot reload untuk backend & frontend
- Debug mode enabled

### Staging
- Docker Swarm / Kubernetes
- Separate database
- Redis cluster
- SSL/TLS enabled

### Production
- Kubernetes cluster
- PostgreSQL with replication
- Redis Sentinel for HA
- CDN for static assets
- Full monitoring & logging

## 📈 Performance Optimization

### Backend
1. **Database Indexing**
   - tenant_id, parent_id, user_id
   - Composite indexes for frequent queries

2. **Query Optimization**
   - Eager loading relationships
   - Select only needed columns
   - Use pagination

3. **Caching Strategy**
   - User permissions cache (5 min)
   - Organization hierarchy cache (10 min)
   - Static data cache (1 hour)

### Frontend
1. **Code Splitting**
   - Route-based splitting
   - Dynamic imports for modules

2. **Data Fetching**
   - React Query caching
   - Optimistic updates
   - Background refetch

3. **Asset Optimization**
   - Image optimization
   - Lazy loading
   - Bundle size monitoring

## 🔒 Security Best Practices

1. **Authentication**
   - Token-based (Sanctum)
   - Password hashing (Bcrypt)
   - Rate limiting on login

2. **Authorization**
   - Permission-based access
   - Tenant isolation
   - Action logging

3. **Data Protection**
   - HTTPS only in production
   - CORS configuration
   - SQL injection prevention (ORM)
   - XSS protection (React escaping)

4. **Monitoring**
   - Failed login attempts
   - Unauthorized access attempts
   - Unusual data access patterns
