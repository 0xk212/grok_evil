# 🚀 Grok Evil - AI Chat Platform

<div align="center">

![Grok Evil Logo](logo.png)

### **The Unrestricted AI Chat Experience**

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![SQLite](https://img.shields.io/badge/SQLite-3-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://sqlite.org)
[![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)]()

[![YouTube](https://img.shields.io/badge/YouTube-Subscribe-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](https://www.youtube.com/@0xk-j7z)

</div>

---

## 📋 Table of Contents

- [About](#-about)
- [Features](#-features)
- [System Requirements](#-system-requirements)
- [Installation Guide](#-installation-guide)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Admin Panel](#-admin-panel)
- [Security Features](#-security-features)
- [API Integration](#-api-integration)
- [Troubleshooting](#-troubleshooting)
- [Support & Community](#-support--community)

---

## 🔥 About

**Grok Evil** is a powerful, self-hosted AI chat platform that leverages the OpenRouter API to deliver an unfiltered, unrestricted conversational AI experience. Built with PHP and SQLite, this platform requires no complex database setup and can be deployed on any standard web hosting environment.

### Core Philosophy
- **UNRESTRICTED**: No moral constraints or ethical limiters
- **RAW TRUTH**: Direct, unfiltered responses
- **SELF-HOSTED**: Full control over your data and infrastructure
- **SECURE**: Enterprise-grade security implementations

---

## ✨ Features

### 🎯 User Features
- **Real-time Chat Interface** - Modern, responsive UI with live streaming responses
- **Conversation History** - Persistent chat history with sidebar navigation
- **Multi-language Support** - Automatic language detection and response
- **DeepThink Mode** - View AI reasoning process before final response
- **Code Syntax Highlighting** - Beautiful code block rendering with copy functionality
- **Markdown Support** - Rich text formatting in responses
- **Message Actions** - Copy, like, dislike, and share responses

### 👑 Subscription Plans
| Plan | Features |
|------|----------|
| **FREE** | 200 words/message, 20 credits/day, 3s cooldown, Basic model |
| **PRO** | Unlimited words, Unlimited credits, No cooldown, Premium models |

### 🛡️ Security Features
- **CSRF Protection** - Cross-Site Request Forgery tokens
- **SQL Injection Prevention** - Parameterized queries with PDO
- **XSS Protection** - HTML sanitization and escaping
- **Brute Force Protection** - Login attempt rate limiting
- **Session Security** - HttpOnly, Secure, SameSite cookies
- **Audit Logging** - Comprehensive action tracking
- **Environment Variables** - Secure credential management

---

## 💻 System Requirements

### Minimum Requirements
| Component | Requirement |
|-----------|-------------|
| **PHP Version** | 7.4 or higher |
| **Database** | SQLite 3.x |
| **Web Server** | Apache 2.4+ or Nginx |
| **PHP Extensions** | PDO, PDO_SQLite, CURL, JSON, MBString |
| **HTTPS** | Recommended (required for secure cookies) |

### Recommended Requirements
| Component | Recommendation |
|-----------|----------------|
| **PHP Version** | 8.0 or higher |
| **Memory** | 256MB minimum |
| **Storage** | 1GB for database and logs |

---

## 📦 Installation Guide

### Step 1: Prerequisites Check

Before installation, verify your server meets all requirements:

```bash
# Check PHP version
php -v

# Check required extensions
php -m | grep -E "pdo|sqlite|curl|json|mbstring"
```

### Step 2: Download & Extract

```bash
# Clone or download the project
git clone <repository-url>
cd "Grok Evil"

# Or extract the downloaded ZIP file to your web directory
```

### Step 3: File Permissions

Set appropriate permissions for the secure_data directory:

```bash
# Linux/Mac
chmod 755 secure_data/
chmod 644 .env
chmod 644 config.php
chmod 644 db.php

# Ensure the web server can write to secure_data
chown -R www-data:www-data secure_data/  # For Apache on Ubuntu/Debian
```

### Step 4: Environment Configuration

Edit the `.env` file with your settings:

```bash
# Open the .env file
nano .env
```

```ini
# ============================================
# GROK EVIL - Environment Configuration
# ============================================

# OpenRouter API Key (Required)
# Get your key from: https://openrouter.ai/keys
OPENROUTER_API_KEY=sk-or-v1-your-actual-api-key-here

# Admin Credentials (First-time setup)
# Change these from the defaults!
BOOTSTRAP_ADMIN_USERNAME=your_admin_username
BOOTSTRAP_ADMIN_PASSWORD=your_strong_password_here
```

> **⚠️ SECURITY WARNING**: Never commit your `.env` file to version control. It contains sensitive credentials.

### Step 5: Database Initialization

The database is automatically created on first run. The system will:
1. Create `secure_data/database.sqlite`
2. Initialize all required tables
3. Create the admin account (if credentials are set)

### Step 6: Web Server Configuration

#### Apache Configuration

The `.htaccess` file is already included. Ensure `mod_rewrite` is enabled:

```apache
# Enable RewriteEngine
RewriteEngine On

# Redirect all traffic to HTTPS (recommended)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

Enable the module:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Nginx Configuration

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/Grok Evil;
    index  index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Block access to sensitive files
    location ~ /\.(env|htaccess)$ {
        deny all;
    }

    location ~ /secure_data/ {
        deny all;
    }
}
```

### Step 7: Verify Installation

1. Navigate to `http://your-domain.com` in your browser
2. You should see the login/registration page
3. Log in with your admin credentials from `.env`

---

## ⚙️ Configuration

### Environment Variables Reference

| Variable | Description | Required |
|----------|-------------|----------|
| `OPENROUTER_API_KEY` | Your OpenRouter API key | ✅ Yes |
| `BOOTSTRAP_ADMIN_USERNAME` | Initial admin username | ✅ Yes |
| `BOOTSTRAP_ADMIN_PASSWORD` | Initial admin password | ✅ Yes |

### Changing Admin Password

After first login, you'll be prompted to change your password. Alternatively:

1. Log in to the admin panel
2. Navigate to **Settings** → **Change Password**
3. Enter current and new password

### Model Configuration

Edit `api.php` to change AI models:

```php
// For FREE users (line 49)
$ai_model = 'arcee-ai/trinity-large-preview:free';

// For PRO users (line 81)
$ai_model = 'deepseek/deepseek-chat';
```

Available models at [OpenRouter Models](https://openrouter.ai/models).

---

## 🎮 Usage

### For Users

1. **Register an Account**
   - Click "Register" on the login page
   - Enter username, email, and password
   - Confirm registration via email (if enabled)

2. **Start Chatting**
   - Log in with your credentials
   - Type your message in the input field
   - Press Enter or click Send
   - View real-time streaming response

3. **Manage Conversations**
   - View conversation history in the sidebar
   - Click any conversation to resume
   - Start new conversations with the "+" button

### For Administrators

Access the admin panel at `http://your-domain.com/admin.php`

#### Admin Features:
- **User Management** - View, edit, delete users
- **Plan Management** - Assign FREE/PRO plans
- **Conversation Monitoring** - View all user conversations
- **System Statistics** - Usage analytics and metrics
- **Audit Logs** - Security event tracking

---

## 🛡️ Security Features

### Implemented Security Measures

| Feature | Description |
|---------|-------------|
| **CSRF Tokens** | Every form submission includes a unique token |
| **Password Hashing** | bcrypt with PASSWORD_DEFAULT |
| **Prepared Statements** | All database queries use PDO prepared statements |
| **Input Sanitization** | HTML escaping and XSS prevention |
| **Rate Limiting** | Login attempt throttling per IP |
| **Secure Sessions** | HttpOnly, Secure, SameSite=Lax cookies |
| **Audit Logging** | All actions logged to `secure_data/audit.log` |
| **Error Suppression** | Production errors hidden from users |

### Security Best Practices

1. **Always use HTTPS** in production
2. **Change default admin credentials** immediately
3. **Keep PHP updated** to the latest stable version
4. **Set proper file permissions** (644 for files, 755 for directories)
5. **Block access to sensitive directories** via `.htaccess`
6. **Regularly review audit logs** for suspicious activity

---

## 🔌 API Integration

### OpenRouter API

This application uses [OpenRouter](https://openrouter.ai/) as the AI provider.

#### Getting Your API Key

1. Visit [OpenRouter](https://openrouter.ai/)
2. Create an account or sign in
3. Go to **Keys** → **Create New Key**
4. Copy the key and add it to your `.env` file

#### API Rate Limits

| Plan | Rate Limit |
|------|------------|
| Free Tier | Varies by model |
| Paid Tier | Higher limits |

Check [OpenRouter Pricing](https://openrouter.ai/pricing) for details.

---

## 🐛 Troubleshooting

### Common Issues

#### 1. "API_KEY_MISSING" Error
**Cause**: OpenRouter API key not configured  
**Solution**: Edit `.env` and set `OPENROUTER_API_KEY`

#### 2. Database Connection Failure
**Cause**: `secure_data/` directory not writable  
**Solution**: 
```bash
chmod 755 secure_data/
chown www-data:www-data secure_data/
```

#### 3. 403 Forbidden on API Calls
**Cause**: CSRF token mismatch or session expired  
**Solution**: Log out and log back in

#### 4. White Screen / Blank Page
**Cause**: PHP error (hidden in production)  
**Solution**: Check `secure_data/php_errors.log`

#### 5. Streaming Not Working
**Cause**: Output buffering or server timeout  
**Solution**: Increase `max_execution_time` in `php.ini`

#### 6. "COOLDOWN_ACTIVE" Message
**Cause**: Free plan rate limit (3 seconds between messages)  
**Solution**: Wait 3 seconds or upgrade to PRO

#### 7. "QUOTA_EXCEEDED" Message
**Cause**: Free plan daily limit reached (20 credits)  
**Solution**: Wait until tomorrow or upgrade to PRO

### Debug Mode

To enable debug output (development only):

```php
// In config.php, add:
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

> **⚠️ WARNING**: Never enable debug mode in production!

---

## 📞 Support & Community

### Get Help

- **YouTube Channel**: [Subscribe for tutorials and updates](https://www.youtube.com/@0xk-j7z)
- **Issues**: Report bugs and request features
- **Documentation**: This README covers most use cases

### Stay Updated

🌟 **Subscribe to our YouTube channel** for:
- Installation tutorials
- Feature demonstrations
- Security updates
- Community highlights

[![YouTube Subscribe](https://img.shields.io/badge/YouTube-Subscribe%20Now-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](https://www.youtube.com/@0xk-j7z)

---

## 📄 License

This project is proprietary software. All rights reserved.

---

## 🙏 Credits

- **OpenRouter** - AI model aggregation platform
- **Font Awesome** - Icon library
- **SQLite** - Database engine

---

<div align="center">

### ⚡ Grok Evil - Unleashed AI Power ⚡

**Built with passion by [0xk](https://www.youtube.com/@0xk-j7z)**

[![YouTube](https://img.shields.io/badge/YouTube-Subscribe-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](https://www.youtube.com/@0xk-j7z)

---

<p align="center">
  <strong>If you find this project useful, please ⭐ star this repository and subscribe to our channel!</strong>
</p>

</div>