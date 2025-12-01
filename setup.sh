#!/bin/bash

echo "======================================"
echo "SaaS UMKM - Quick Setup Script"
echo "======================================"
echo ""

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

echo "✓ Docker and Docker Compose are installed"
echo ""

# Start Docker containers
echo "🚀 Starting Docker containers..."
docker-compose up -d

echo ""
echo "⏳ Waiting for PostgreSQL to be ready..."
sleep 10

# Backend setup
echo ""
echo "📦 Setting up Laravel backend..."
docker-compose exec -T backend bash -c "
    echo '→ Installing Composer dependencies...'
    composer install --no-interaction --prefer-dist --optimize-autoloader
    
    if [ ! -f .env ]; then
        echo '→ Creating .env file...'
        cp .env.example .env
    fi
    
    echo '→ Generating application key...'
    php artisan key:generate
    
    echo '→ Running migrations...'
    php artisan migrate --force
    
    echo '→ Seeding database...'
    php artisan db:seed
    
    echo '→ Clearing cache...'
    php artisan config:clear
    php artisan cache:clear
"

# Frontend setup (when created)
# echo ""
# echo "📦 Setting up Next.js frontend..."
# docker-compose exec -T frontend bash -c "
#     echo '→ Installing npm dependencies...'
#     npm install
# "

echo ""
echo "======================================"
echo "✅ Setup Complete!"
echo "======================================"
echo ""
echo "🌐 Applications are running at:"
echo "   Backend API:  http://localhost:8000"
echo "   Frontend:     http://localhost:3000 (not yet created)"
echo "   PostgreSQL:   localhost:5432"
echo ""
echo "🔑 Demo Login Credentials:"
echo "   Admin:  admin@demo.com / password"
echo "   Staff:  staff@demo.com / password"
echo ""
echo "📚 View API docs at: http://localhost:8000/api/documentation"
echo ""
echo "🛠️  Useful commands:"
echo "   docker-compose logs -f backend    # View backend logs"
echo "   docker-compose exec backend bash  # Enter backend container"
echo "   docker-compose down               # Stop all containers"
echo ""
