# Database Setup Guide

## 🗄️ MySQL Database Configuration

### Step 1: Create Database

Open MySQL command line or phpMyAdmin and run:

```sql
CREATE DATABASE laravel_medical_chatbot;
```

### Step 2: Update `.env` File

Open `Laravel/.env` and update these lines:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_medical_chatbot
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

**Replace `your_password_here` with your MySQL root password!**

### Step 3: Run Migrations

```bash
cd Laravel
php artisan migrate
```

Expected output:
```
✅ Migration table created successfully
✅ Migrating: 0001_01_01_000000_create_users_table
✅ Migrating: 0001_01_01_000001_create_cache_table
✅ Migrating: 0001_01_01_000002_create_jobs_table
✅ Migrating: 2019_12_14_000001_create_personal_access_tokens_table
✅ Migrating: 2025_12_03_021043_create_chat_sessions_table
✅ Migrating: 2025_12_03_021044_create_chat_messages_table
✅ Migrating: 2025_12_03_021046_create_detection_logs_table
```

---

## 🎯 What We've Built So Far

### ✅ Completed:

1. **Database Schema:**
   - `users` - User authentication
   - `chat_sessions` - Track user/guest chat sessions with detection data
   - `chat_messages` - Store conversation messages
   - `detection_logs` - Log all model detections for analytics
   - `personal_access_tokens` - Laravel Sanctum API tokens

2. **Models with Relationships:**
   - `User` → has many `ChatSession`
   - `ChatSession` → belongs to `User` (nullable for guests)
   - `ChatSession` → has many `ChatMessage`
   - `ChatSession` → has many `DetectionLog`
   - Auto-generates session tokens for guest tracking

3. **Authentication:**
   - Laravel Sanctum installed
   - User model has HasApiTokens trait
   - Ready for API token-based auth

---

## 🔜 Next Steps

### Immediate (After DB Setup):

1. **Create API Controllers:**
   - AuthController (register, login, logout)
   - DetectionController (multi-model detection)
   - ChatSessionController (start session, view history)
   - ChatMessageController (send message, get bot response)

2. **Setup API Routes:**
   - Authentication endpoints
   - Detection endpoints  
   - Chat endpoints

3. **Test Basic Flow:**
   - Register → Login → Detect → Chat

### This Session vs Next Session:

**Can finish today:**
- ✅ Database setup
- ✅ Run migrations
- ✅ Create AuthController
- ✅ Create basic API routes
- ✅ Test registration/login

**Next session:**
- 🔄 Multi-model detection (fatigue, pain)
- 🔄 Chatbot integration
- 🔄 Complete chat system
- 🔄 Frontend integration

---

## 📋 Quick Commands Reference

```bash
# Create database (MySQL)
CREATE DATABASE laravel_medical_chatbot;

# Run migrations
php artisan migrate

# Rollback if needed
php artisan migrate:rollback

# Fresh migration (reset all)
php artisan migrate:fresh

# Check database connection
php artisan db:show

# Create controller
php artisan make:controller Api/AuthController

# Check routes
php artisan route:list
```

---

## 🎯 Backend Architecture Status

```
✅ Database Layer (DONE)
   ├── Migrations created
   ├── Models with relationships
   └── Ready to migrate

⏳ API Layer (NEXT)
   ├── Controllers
   ├── Routes
   └── Services

⏸️ Integration Layer
   ├── Python multi-model API
   ├── Chatbot service
   └── File storage

⏸️ Frontend Layer
   └── API documentation
```

---

## 💡 Tips

1. **Use phpMyAdmin** if you're not comfortable with MySQL CLI
2. **Backup .env** before making changes
3. **Test each migration step** before proceeding
4. **Check Laravel logs** at `storage/logs/laravel.log` if errors occur

---

Ready to setup database? Let me know when done! 🚀
