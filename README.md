<div align="center">

# 🚀 GROK EVIL

### **The Next Generation AI Chat Platform**

![Grok Evil Logo](logo.png)

[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![SQLite](https://img.shields.io/badge/SQLite-3-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://sqlite.org)
[![OpenRouter](https://img.shields.io/badge/OpenRouter-API-FF6B35?style=for-the-badge)](https://openrouter.ai)
[![License](https://img.shields.io/badge/License-Proprietary-red?style=for-the-badge)]()

[![YouTube](https://img.shields.io/badge/YouTube-0xk--j7z-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](https://www.youtube.com/@0xk-j7z)

---

</div>

<div align="center">

## ⚡ **The Most Advanced Open-Source AI Platform**

</div>

---

## 🎯 **Why Grok Evil is Different**

<div align="center">

| Feature | Description |
|---------|-------------|
| 🚀 **Blazing Fast** | Parallel request processing with real streaming |
| 🛡️ **Multi-Layer Security** | 7 integrated security layers |
| 💾 **Built-in Database** | SQLite with advanced performance optimizations |
| 🌐 **Multi-Language** | Auto-detection with optimized responses |
| 🎨 **Modern UI** | Cyberpunk design with smooth UX |
| 📊 **Flexible Plans** | FREE/PRO with smart credit management |

</div>

---

## 📋 **Table of Contents**

<div align="center">

[**🔥 About**](#-about) • 
[**✨ Features**](#-features) • 
[**💻 Requirements**](#-system-requirements) • 
[**📦 Installation**](#-installation-guide) • 
[**⚙️ Configuration**](#️-configuration) • 
[**🎮 Usage**](#-usage) • 
[**🛡️ Security**](#️-security) • 
[**🔧 Troubleshooting**](#-troubleshooting) • 
[**📞 Support**](#-support--community)

</div>

---

## 🔥 **About**

<div align="center">

### **Grok Evil is Not Just Another Chat App**

</div>

**Grok Evil** is an advanced AI-powered chat system designed to deliver an unrestricted, direct conversational experience. Built with modern technologies, it runs on any PHP hosting with a built-in SQLite database requiring no complex setup.

### 🎪 **Vision**
> "We believe AI should be free, unrestricted, and accessible to everyone"

### 🏗️ **Architecture**

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│   Client Side   │◄──►│   PHP Backend    │◄──►│  OpenRouter API │
│   (JavaScript)  │    │   (REST API)     │    │   (AI Models)   │
└─────────────────┘    └──────────────────┘    └─────────────────┘
         │                       │                       │
         ▼                       ▼                       ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│  Real-time UI   │    │  SQLite Database │    │  Streaming      │
│  (Chat Interface)│   │  (User Data)     │    │  Responses      │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

---

## ✨ **Features**

### 🎯 **Advanced User Features**

<div align="center">

<table>
<tr>
<th>Feature</th>
<th>Description</th>
<th>Status</th>
</tr>
<tr>
<td>💬 Live Chat</td>
<td>Modern chat interface with real-time updates</td>
<td>✅ Active</td>
</tr>
<tr>
<td>📜 Chat History</td>
<td>Auto-save with resume capability</td>
<td>✅ Active</td>
</tr>
<tr>
<td>🧠 DeepThink Mode</td>
<td>View AI reasoning process</td>
<td>✅ Active</td>
</tr>
<tr>
<td>🌍 Multi-Language</td>
<td>Auto-detection with optimized responses</td>
<td>✅ Active</td>
</tr>
<tr>
<td>📝 Markdown</td>
<td>Full formatting support with syntax highlighting</td>
<td>✅ Active</td>
</tr>
<tr>
<td>⚡ Real Streaming</td>
<td>Character-by-character response display</td>
<td>✅ Active</td>
</tr>
</table>

</div>

### 👑 **Smart Subscription System**

<div align="center">

| Plan | Price | Words/Message | Credits/Day | Cooldown | Models |
|------|-------|---------------|-------------|----------|--------|
| **🆓 FREE** | Free | 200 words | 20 credits | 3 seconds | Basic |
| **💎 PRO** | Paid | Unlimited | Unlimited | None | Premium |

</div>

### 🛡️ **Advanced Security System**

<div align="center">

```
┌─────────────────────────────────────────────────────────┐
│                  Seven Layers of Protection             │
├─────────────────────────────────────────────────────────┤
│ 7️⃣  Audit Logging      - All operations logged          │
│ 6️⃣  Rate Limiting      - Prevents brute force attacks   │
│ 5️⃣  Session Security   - Secure sessions with SameSite  │
│ 4️⃣  Input Sanitization - Clean all user inputs          │
│ 3️⃣  XSS Protection     - Prevents script injection      │
│ 2️⃣  SQL Injection      - Parameterized queries          │
│ 1️⃣  CSRF Tokens        - Form protection tokens         │
└─────────────────────────────────────────────────────────┘
```

</div>

---

## 💻 **System Requirements**

### 📋 **Minimum Requirements**

<div align="center">

| Component | Required Version | Notes |
|-----------|------------------|-------|
| **PHP** | 7.4+ | 8.0+ recommended for performance |
| **SQLite** | 3.x | Usually bundled with PHP |
| **Web Server** | Apache 2.4+ or Nginx | With mod_rewrite |
| **RAM** | 128MB | 256MB+ recommended |
| **Storage** | 500MB | 1GB+ for expansion |

</div>

### 🔧 **Required Extensions**

```bash
# Essential extensions
pdo_mysql or pdo_sqlite
curl
json
mbstring
openssl
```

### ✅ **Requirements Checker**

```bash
# Comprehensive check script
#!/bin/bash
echo "🔍 Checking Grok Evil Requirements..."

# PHP Version
php_version=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "-" -f 1)
echo "📌 PHP Version: $php_version"

# Required Extensions
extensions=("pdo" "sqlite3" "curl" "json" "mbstring")
for ext in "${extensions[@]}"; do
    if php -m | grep -q "$ext"; then
        echo "✅ $ext extension found"
    else
        echo "❌ $ext extension missing"
    fi
done

# Write permissions
if [ -w "secure_data" ]; then
    echo "✅ secure_data directory is writable"
else
    echo "❌ secure_data directory is not writable"
fi
```

---

## 📦 **Installation Guide**

### 🚀 **Quick Install (5 Minutes)**

#### **Step 1️⃣: Download Project**

```bash
# Git method (recommended)
git clone https://github.com/0xk/grok-evil.git
cd grok-evil

# Or download ZIP
wget https://github.com/0xk/grok-evil/archive/main.zip
unzip main.zip
cd grok-evil-main
```

#### **Step 2️⃣: Set Permissions**

```bash
# Create secure data directory
mkdir -p secure_data

# Set permissions (Linux/Mac)
chmod 755 secure_data/
chmod 644 .env config.php db.php api.php

# Set owner (Apache/Nginx)
sudo chown -R www-data:www-data secure_data/
```

#### **Step 3️⃣: Configure Environment**

```bash
# Copy environment template
cp .env.example .env

# Edit settings
nano .env
```

#### **Step 4️⃣: Edit .env File**

```ini
# ═══════════════════════════════════════════════════════════
# ═══  GROK EVIL - Environment Configuration  ═══
# ═══════════════════════════════════════════════════════════

# 🔑 OpenRouter API Key (Required)
# Get your key from: https://openrouter.ai/keys
OPENROUTER_API_KEY=sk-or-v1-xxxxxxxxxxxxxxxxxxxxxxxx

# 👤 Admin Credentials (First-time setup)
# Change these from defaults for security!
BOOTSTRAP_ADMIN_USERNAME=admin_yourname
BOOTSTRAP_ADMIN_PASSWORD=Your_Str0ng_P@ssw0rd!
```

#### **Step 5️⃣: Configure Web Server**

##### **A) Apache (with included .htaccess)**

```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

##### **B) Nginx**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/grok-evil;
    index index.php;

    # Block sensitive files
    location ~ /\.(env|htaccess)$ {
        deny all;
        return 404;
    }

    # Block data directory
    location ~ /secure_data/ {
        deny all;
        return 404;
    }

    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # URL rewriting
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

#### **Step 6️⃣: Verify Installation**

```bash
# Create test script
cat > test_install.php << 'EOF'
<?php
echo "<h1>🔍 Grok Evil Installation Test</h1>";
$tests = [
    'PHP Version' => phpversion(),
    'PDO Available' => extension_loaded('pdo') ? '✅' : '❌',
    'SQLite Available' => extension_loaded('pdo_sqlite') ? '✅' : '❌',
    'CURL Available' => extension_loaded('curl') ? '✅' : '❌',
    'secure_data Writable' => is_writable('secure_data') ? '✅' : '❌',
];
echo "<pre>" . print_r($tests, true) . "</pre>";
?>
EOF
```

#### **Step 7️⃣: First Login**

1. Open browser: `http://your-domain.com`
2. Login with admin credentials from `.env`
3. Database will be created automatically

---

## ⚙️ **Configuration**

### 📊 **Environment Variables Reference**

<div align="center">

| Variable | Type | Description | Default |
|----------|------|-------------|---------|
| `OPENROUTER_API_KEY` | string | API key | Empty (Required) |
| `BOOTSTRAP_ADMIN_USERNAME` | string | Admin username | Empty |
| `BOOTSTRAP_ADMIN_PASSWORD` | string | Admin password | Empty |

</div>

### 🔄 **Changing AI Models**

In `api.php`:

```php
// 📊 Model for FREE users (line 49)
$ai_model = 'arcee-ai/trinity-large-preview:free';

// 💎 Model for PRO users (line 81)
$ai_model = 'deepseek/deepseek-chat';
```

---

## 🎮 **Usage**

### 👤 **User Guide**

#### **1. Registration**
```
1. Go to registration page
2. Enter: username, email, password
3. Click "Register"
```

#### **2. Start Chatting**
```
1. Log in
2. Type your message in input field
3. Press Enter or click Send
4. Watch response stream character by character
```

#### **3. Manage Conversations**
```
📋 View history: Left sidebar
🔄 Resume chat: Click any previous conversation
➕ New chat: Click "+" button
```

### 👨‍💼 **Admin Guide**

```
URL: http://your-domain.com/admin.php

Features:
├── 👥 User Management
├── 💬 Conversation Monitoring
├── 📊 Statistics
└── 🔒 Security Logs
```

---

## 🛡️ **Security**

### 🏰 **Security Architecture**

<div align="center">

```
┌─────────────────────────────────────────────────────────────┐
│                    GROK EVIL SECURITY                       │
├─────────────────────────────────────────────────────────────┤
│  Layer 7: Audit Logging    │  Layer 4: Input Sanitization  │
│  Layer 6: Rate Limiting    │  Layer 3: XSS Protection      │
│  Layer 5: Session Security │  Layer 2: SQL Injection       │
│                            │  Layer 1: CSRF Tokens         │
└─────────────────────────────────────────────────────────────┘
```

</div>

### 🔒 **Best Practices**

1. **Always use HTTPS** in production
2. **Change admin credentials** immediately
3. **Keep PHP updated** regularly
4. **Review audit logs** frequently

---

## 🔧 **Troubleshooting**

### Common Issues

| Issue | Cause | Solution |
|-------|-------|----------|
| `API_KEY_MISSING` | API key not configured | Edit `.env` |
| `Database Error` | `secure_data/` not writable | `chmod 755 secure_data/` |
| White screen | Hidden PHP error | Check `secure_data/php_errors.log` |
| `COOLDOWN_ACTIVE` | Free plan limit | Wait 3 seconds or upgrade to PRO |
| `QUOTA_EXCEEDED` | Daily credits exhausted | Wait tomorrow or upgrade to PRO |

---

## 📞 **Support & Community**

### 🌟 **Connect With Us**

<div align="center">

[![YouTube](https://img.shields.io/badge/YouTube-Subscribe%20Now-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](https://www.youtube.com/@0xk-j7z)

**Subscribe to our YouTube channel** for:
- 📹 Installation tutorials
- 🎬 Feature demonstrations
- 🔔 Security updates
- 💡 Tips and tricks

</div>

---

<div align="center">

## ⚡ **Grok Evil - Unleashed AI Power** ⚡

### **Built with passion by [0xk](https://www.youtube.com/@0xk-j7z)**

---

<p>
  <strong>If you find this project useful, please ⭐ star this repository and subscribe to our channel!</strong>
</p>

[![YouTube](https://img.shields.io/badge/YouTube-0xk--j7z-FF0000?style=for-the-badge&logo=youtube&logoColor=white)](https://www.youtube.com/@0xk-j7z)

</div>