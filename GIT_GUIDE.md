# 🚀 Push to GitHub Repository

## Initial Setup

### Step 1: Navigate to Project Directory
```bash
cd saas-umkm
```

### Step 2: Initialize Git (if not already initialized)
```bash
git init
```

### Step 3: Add Remote Repository
```bash
git remote add origin https://github.com/sanzuke/saas-umkm.git
```

### Step 4: Check Remote
```bash
git remote -v
```

You should see:
```
origin  https://github.com/sanzuke/saas-umkm.git (fetch)
origin  https://github.com/sanzuke/saas-umkm.git (push)
```

## Push to GitHub

### Step 1: Stage All Files
```bash
git add .
```

### Step 2: Create Initial Commit
```bash
git commit -m "Initial commit: Phase 1 - Foundation complete

✅ Database schema (11 migrations)
✅ Laravel models (7 models)
✅ Database seeders with demo data
✅ Docker setup for local development
✅ Complete documentation

Features:
- Multi-tenant architecture
- 3-level organizational hierarchy
- Role-based access control
- Module subscription system
- Demo data: 1 corporate, 1 company, 1 BU, 2 users"
```

### Step 3: Push to GitHub
```bash
# Push to main branch
git push -u origin main
```

**If main branch doesn't exist, try:**
```bash
git branch -M main
git push -u origin main
```

## Subsequent Pushes

After making changes:

```bash
# Check status
git status

# Stage changed files
git add .

# Or stage specific files
git add apps/backend/app/Http/Controllers/AuthController.php

# Commit with message
git commit -m "Add authentication controller"

# Push to remote
git push
```

## Branch Management (Recommended)

### Create Feature Branch
```bash
# Create and switch to new branch
git checkout -b feature/auth-controllers

# Make changes...

# Commit changes
git add .
git commit -m "Implement authentication endpoints"

# Push branch to GitHub
git push -u origin feature/auth-controllers
```

### Merge Branch
```bash
# Switch back to main
git checkout main

# Merge feature branch
git merge feature/auth-controllers

# Push to GitHub
git push
```

## Useful Git Commands

### View Commit History
```bash
git log --oneline
```

### View Changes
```bash
# View unstaged changes
git diff

# View staged changes
git diff --staged
```

### Undo Changes
```bash
# Discard changes in working directory
git checkout -- filename

# Unstage file
git reset HEAD filename

# Undo last commit (keep changes)
git reset --soft HEAD~1

# Undo last commit (discard changes)
git reset --hard HEAD~1
```

### Pull Latest Changes
```bash
git pull origin main
```

## .gitignore

The `.gitignore` file is already configured to exclude:
- Laravel vendor/ directory
- Next.js node_modules/
- Environment files (.env)
- Build artifacts
- IDE files
- Log files

## GitHub Authentication

### Using HTTPS (Recommended for beginners)

When you push, GitHub will ask for credentials:
- **Username**: Your GitHub username
- **Password**: Your GitHub Personal Access Token (not your account password!)

**Create Personal Access Token:**
1. Go to GitHub Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Click "Generate new token (classic)"
3. Select scopes: `repo` (full control of private repositories)
4. Copy the token and use it as password when pushing

### Using SSH (Alternative)

```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "your_email@example.com"

# Add to SSH agent
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519

# Copy public key
cat ~/.ssh/id_ed25519.pub

# Add to GitHub: Settings → SSH and GPG keys → New SSH key

# Change remote to SSH
git remote set-url origin git@github.com:sanzuke/saas-umkm.git
```

## Collaboration Workflow

### Clone Repository (for other developers)
```bash
git clone https://github.com/sanzuke/saas-umkm.git
cd saas-umkm
./setup.sh
```

### Before Starting Work
```bash
# Update local repository
git pull origin main
```

### After Finishing Work
```bash
git add .
git commit -m "Descriptive message"
git pull origin main  # Get latest changes
git push origin main
```

## Git Best Practices

### Commit Messages
✅ Good:
```
git commit -m "Add user authentication controller

- Implement login endpoint
- Implement register endpoint
- Add input validation
- Add error handling"
```

❌ Bad:
```
git commit -m "fix stuff"
git commit -m "update"
```

### Commit Frequency
- Commit often (after completing a feature or fixing a bug)
- Each commit should represent a logical change
- Don't commit broken code to main branch

### Branch Naming
```
feature/user-authentication
bugfix/login-validation
hotfix/security-patch
refactor/database-queries
```

## Troubleshooting

### Push Rejected
```bash
# Pull and merge first
git pull origin main --rebase
git push origin main
```

### Merge Conflicts
```bash
# Edit conflicting files manually
# Then:
git add .
git commit -m "Resolve merge conflicts"
git push
```

### Reset to Remote
```bash
# Discard all local changes and reset to remote
git fetch origin
git reset --hard origin/main
```

---

## Quick Reference Card

```bash
# Status
git status

# Stage
git add .
git add filename

# Commit
git commit -m "message"

# Push
git push
git push origin main

# Pull
git pull
git pull origin main

# Branch
git branch                    # list branches
git branch feature-name       # create branch
git checkout feature-name     # switch branch
git checkout -b feature-name  # create and switch

# Merge
git merge feature-name

# Log
git log
git log --oneline
```

---

**Happy Coding! 🚀**
