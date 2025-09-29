# 🚌 Springool — Secure Bus Booking System

> An educational PHP/MySQL web application for bus ticket booking with user roles, route management, and admin panel. Built with security-first principles: protected against SQL injection, XSS, and password leaks.

[![Security Focused](https://img.shields.io/badge/Security-Focused-red?style=flat&logo=lock)](https://owasp.org)
[![PHP](https://img.shields.io/badge/PHP-8.x+-8892BF?style=flat&logo=php)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.x+-4479A1?style=flat&logo=mysql)](https://www.mysql.com/)

---

## 🎯 Purpose
This project demonstrates secure web development practices in PHP:
- ✅ **SQL injection prevention** via PDO prepared statements  
- ✅ **XSS protection** using `htmlspecialchars()`  
- ✅ **Secure password hashing** with `password_hash()`  
- ✅ **Role-based access control** (user / admin)  
- ✅ **Transaction-safe bookings**  
- ⚠️ CSRF protection is **not implemented** (educational scope)

> 🔒 **Not for production use** — built for learning secure coding.

---

## 🛠️ Tech Stack
- **Backend**: PHP 8+, PDO, `vlucas/phpdotenv`
- **Frontend**: HTML5, CSS3, vanilla JavaScript
- **Database**: MySQL 8+
- **Security**: OWASP-aligned practices

---

## 🖼️ Screenshots
<div align="center">
  <img src="screenshots/home.png" alt="Home Page" width="380"/>
  <img src="screenshots/account.png" alt="User Dashboard" width="380"/>
  <img src="screenshots/admin.png" alt="Admin Panel" width="380"/>
  <img src="screenshots/routes.png" alt="Routes & Schedule" width="380"/>
</div>

---

## 📦 Local Setup

### Prerequisites
- PHP 8.0+
- MySQL
- [Composer](https://getcomposer.org/)

### Steps
1. Clone the repository:
   ```bash
   git clone https://github.com/Apachiend/springool-transport.git
   cd springool-transport