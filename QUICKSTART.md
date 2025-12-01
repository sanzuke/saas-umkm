# 🚀 Quick Start Guide - SaaS UMKM

## Cara Termudah untuk Memulai (5 Menit)

### Prerequisites
✅ Docker Desktop terinstal dan berjalan
✅ Git terinstal
✅ Port 3000, 8000, 5432, 6379 tersedia

---

## 📥 Step 1: Download & Extract Project

Anda sudah memiliki folder `saas-umkm`. Buka terminal/command prompt dan masuk ke folder tersebut:

```bash
cd saas-umkm
```

---

## 🐳 Step 2: Jalankan Setup Otomatis

### Untuk macOS / Linux:
```bash
chmod +x setup.sh
./setup.sh
```

### Untuk Windows (PowerShell):
```powershell
docker-compose up -d --build
docker exec saas_backend composer install
docker exec saas_backend php artisan key:generate
docker exec saas_backend php artisan migrate --force
docker exec saas_backend php artisan db:seed --force
docker exec saas_frontend npm install
```

Tunggu 2-3 menit hingga semua container berjalan.

---

## ✅ Step 3: Verifikasi Instalasi

### Check Docker Containers
```bash
docker-compose ps
```

Pastikan semua service status "Up":
- ✅ saas_postgres
- ✅ saas_redis
- ✅ saas_backend
- ✅ saas_frontend

### Check Logs (jika ada masalah)
```bash
docker-compose logs -f
```

---

## 🌐 Step 4: Akses Aplikasi

1. Buka browser: **http://localhost:3000**
2. Klik **"Daftar sekarang"**
3. Isi form registrasi dengan data berikut:

**Informasi Pengguna:**
- Nama: `Admin Utama`
- Email: `admin@example.com`
- Password: `password123`
- Konfirmasi Password: `password123`

**Informasi Organisasi:**
- Corporate: `PT Contoh Indonesia`
- Company: `Cabang Jakarta`
- Business Unit: `Toko Retail Utama`

4. Klik **"Daftar Sekarang"**
5. Anda akan otomatis login dan menjadi **Super Admin**! 🎉

---

## 📱 Step 5: Eksplorasi Dashboard

Setelah login, Anda akan melihat:

1. **Dashboard** - Overview organisasi
2. **Organisasi** - Kelola hierarchy
3. **Pengguna** - User management
4. **Modul** - Inventory, POS, Bengkel, Konveksi

---

## 🛠️ Command Reference

### Start/Stop Services
```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# Restart
docker-compose restart
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
```

### Access Containers
```bash
# Backend (Laravel)
docker exec -it saas_backend bash

# Frontend (Next.js)
docker exec -it saas_frontend sh

# Database (PostgreSQL)
docker exec -it saas_postgres psql -U saas_user -d saas_umkm
```

### Laravel Artisan Commands
```bash
# Run migration
docker exec saas_backend php artisan migrate

# Seed database
docker exec saas_backend php artisan db:seed

# Clear cache
docker exec saas_backend php artisan cache:clear

# Create migration
docker exec saas_backend php artisan make:migration create_table_name
```

---

## 🔧 Troubleshooting

### Problem: Port 3000 already in use
**Solution:**
```bash
# Find process using port 3000
lsof -ti:3000

# Kill the process (macOS/Linux)
lsof -ti:3000 | xargs kill -9

# Or change port in docker-compose.yml:
# frontend:
#   ports:
#     - "3001:3000"  # Change 3000 to 3001
```

### Problem: Cannot connect to database
**Solution:**
```bash
# Check if postgres is running
docker ps | grep postgres

# Restart postgres
docker-compose restart postgres

# Wait 10 seconds then restart backend
sleep 10
docker-compose restart backend
```

### Problem: Frontend shows "Cannot connect to API"
**Solution:**
```bash
# Check backend is running
curl http://localhost:8000/api/me

# Check .env.local
cat frontend/.env.local

# Should show: NEXT_PUBLIC_API_URL=http://localhost:8000/api
```

### Problem: Module not found errors (Frontend)
**Solution:**
```bash
# Reinstall node_modules
docker exec saas_frontend rm -rf node_modules package-lock.json
docker exec saas_frontend npm install

# Restart frontend
docker-compose restart frontend
```

### Problem: Class not found (Backend)
**Solution:**
```bash
docker exec saas_backend composer dump-autoload
docker exec saas_backend php artisan config:clear
docker-compose restart backend
```

---

## 📚 Next Steps

### 1. Explore the Code
- **Backend**: `backend/app/Models` - Lihat Models
- **Frontend**: `frontend/app/dashboard` - Lihat Pages
- **API**: `backend/routes/api.php` - Lihat Routes

### 2. Add More Users
1. Go to Dashboard → Pengguna
2. Click "Tambah Pengguna"
3. Assign roles and groups

### 3. Create More Organizations
1. Go to Dashboard → Organisasi
2. Add Company or Business Unit
3. Assign users to new groups

### 4. Develop New Features
1. Create new migration: `php artisan make:migration`
2. Create new model: `php artisan make:model`
3. Create new controller: `php artisan make:controller`
4. Add new pages in `frontend/app/`

---

## 🎯 Development Workflow

### Backend Development
```bash
# 1. Create migration
docker exec saas_backend php artisan make:migration add_column_to_table

# 2. Edit migration file
# backend/database/migrations/YYYY_MM_DD_HHMMSS_add_column_to_table.php

# 3. Run migration
docker exec saas_backend php artisan migrate

# 4. Create/Edit model
# backend/app/Models/YourModel.php

# 5. Create controller
docker exec saas_backend php artisan make:controller Api/YourController

# 6. Add routes
# backend/routes/api.php
```

### Frontend Development
```bash
# 1. Create new page
# frontend/app/your-page/page.tsx

# 2. Create components
# frontend/components/YourComponent.tsx

# 3. Add API calls
# frontend/lib/api.ts

# 4. Hot reload is automatic! Just save and refresh browser
```

---

## 📞 Getting Help

1. **Check Logs**: `docker-compose logs -f`
2. **Read Documentation**: 
   - `README.md` - General overview
   - `ARCHITECTURE.md` - Architecture details
3. **Common Issues**: See Troubleshooting section above

---

## 🎉 Success Checklist

After setup, you should be able to:

- [ ] Access frontend at http://localhost:3000
- [ ] Register new account
- [ ] Login successfully
- [ ] See dashboard with your organization
- [ ] Navigate through menu items
- [ ] Access API at http://localhost:8000/api/me

If all checked, **congratulations! Your SaaS UMKM is ready!** 🚀

---

## 📈 What's Next?

### Phase 2 Features (Coming Soon)
- Complete User Management UI
- Role & Permission Management
- Subscription Plans Interface
- Inventory Module (CRUD Products)
- POS Module (Transactions)

### Contribute
- Fork the repository
- Create feature branch
- Submit pull request

---

**Happy Building! 💪**
