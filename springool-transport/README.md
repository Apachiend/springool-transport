# 🚌 Springool — Bus Booking System

> A PHP/MySQL web application for intercity bus ticket booking: route search, seat reservation, personal account and an admin panel. Plain PHP (no framework) with a clean structure and a strong focus on secure coding and data consistency.

[![PHP](https://img.shields.io/badge/PHP-8.0+-8892BF?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![No framework](https://img.shields.io/badge/framework-none-lightgrey?style=flat)](#-architecture)

---

## ✨ Features

**Passengers**
- Search trips by departure city, arrival city and date (only upcoming trips with free seats are shown)
- Book seats (1–10 passengers per booking) — the seats are reserved immediately
- Personal account with booking history and statuses: *pending*, *confirmed*, *cancelled*
- Cancel a pending booking — seats go back to the trip exactly once

**Administrators**
- Dashboard with counters (users, trips, bookings, bookings awaiting a decision)
- Confirm / set pending / cancel / delete bookings — seat counts stay correct on every transition
- Create, edit and delete users and trips (with times and prices)
- Guard rails: an admin can't delete or demote themselves; trips with bookings can't be deleted

---

## 🔒 Security

| Threat | Protection |
|--------|-----------|
| SQL injection | PDO with **native** prepared statements (`ATTR_EMULATE_PREPARES = false`); no user input is concatenated into SQL |
| XSS | Every dynamic value is escaped with `e()` (`htmlspecialchars`, `ENT_QUOTES`); strict **Content-Security-Policy** (no inline scripts/styles) |
| CSRF | Per-session token on **every** state-changing form; all such actions are `POST` (booking, cancel, logout, all admin actions) |
| Password storage | `password_hash()` / `password_verify()`, automatic rehash, min 8 / max 72 bytes |
| Session fixation / hijacking | `session_regenerate_id(true)` on login/logout, `HttpOnly` + `SameSite=Lax` (+ `Secure` on HTTPS) cookies, strict mode, idle timeout |
| Brute force | Failed logins are counted per IP (8 attempts / 15 min); constant-time-ish response for unknown logins |
| Broken access control | Admin pages check the role **from the database on every request**, so a deleted or demoted user loses access immediately |
| Open redirect | The post-login `next` target is whitelisted to internal `.php` pages |
| Clickjacking & sniffing | `X-Frame-Options`, `frame-ancestors 'none'`, `X-Content-Type-Options`, `Referrer-Policy` |
| Information leaks | Errors are logged, users see a neutral 500 page; `debug` mode is opt-in |
| Secrets in git | DB credentials live in `config/config.php`, which is git-ignored |

---

## 🧠 Booking logic

Seats are the only shared, contended resource, so all seat accounting lives in `src/orders.php` and follows one invariant:

> **A booking holds its seats for as long as its status is not _cancelled_.**

- Every operation runs in a **transaction** with row locks (`SELECT … FOR UPDATE`), so two simultaneous requests can't oversell a trip.
- Cancelling (by the user or by an admin) returns the seats **once**; cancelling an already cancelled booking is rejected.
- Restoring a cancelled booking re-takes the seats and fails cleanly if they are gone.
- Deleting a booking or a user releases the seats of every non-cancelled booking.

### Statuses

| Value | Meaning |
|------:|---------|
| `-1` | Cancelled |
| `0` | Pending (default after booking) |
| `1` | Confirmed |

---

## 🏗️ Architecture

```
springool-transport/
├── public/                  # ← web root (the only folder exposed to the web)
│   ├── index.php            # Home: search form, upcoming trips
│   ├── routes.php           # Schedule + booking forms
│   ├── booking.php          # POST: create booking
│   ├── cancel_booking.php   # POST: cancel own pending booking
│   ├── account.php          # User dashboard
│   ├── login.php            # Login (form + handler)
│   ├── register.php         # Registration (form + handler)
│   ├── logout.php           # POST: logout
│   ├── admin/
│   │   ├── index.php        # Admin dashboard
│   │   ├── actions.php      # POST: status changes and deletions
│   │   ├── route_form.php   # Add / edit trip
│   │   └── user_form.php    # Add / edit user
│   └── assets/              # style.css, app.js
├── src/                     # Application code (not web-accessible)
│   ├── bootstrap.php        # Includes, error handler, security headers, session
│   ├── helpers.php          # e(), url(), flash, CSRF, input parsing, formatting
│   ├── db.php               # PDO connection + transaction helper
│   ├── auth.php             # Sessions, roles, login throttling
│   ├── users.php            # User validation and CRUD
│   ├── orders.php           # Booking service (seat accounting)
│   ├── trips.php            # Trip validation
│   └── layout.php           # Shared header/footer
├── config/
│   └── config.example.php   # Copy to config.php
├── database/
│   ├── schema.sql           # Tables
│   ├── seed.sql             # Demo trips (dates relative to today)
│   └── upgrade.sql          # For databases from the first version
└── bin/
    └── create_admin.php     # CLI: create / promote an administrator
```

Design choices: one shared layout instead of copy-pasted HTML/CSS, one DB connection, business rules separated from page templates, and every page starts with a single `require 'bootstrap.php'`.

---

## 🚀 Local setup

### Requirements
- PHP **8.0+** with `pdo_mysql` and `mbstring`
- MySQL 8+ (or MariaDB 10.4+)

### Steps

1. **Clone**
   ```bash
   git clone https://github.com/Apachiend/springool-transport.git
   cd springool-transport
   ```

2. **Create the database and tables**
   ```bash
   mysql -u root -p -e "CREATE DATABASE springool CHARACTER SET utf8mb4;"
   mysql -u root -p springool < database/schema.sql
   mysql -u root -p springool < database/seed.sql     # optional demo trips
   ```

3. **Configure**
   ```bash
   cp config/config.example.php config/config.php
   ```
   Edit `config/config.php`: database credentials, timezone, `base_url`.

4. **Create an administrator**
   ```bash
   php bin/create_admin.php
   ```
   (Or promote an existing user: `php bin/create_admin.php --promote LOGIN`.)

5. **Run**
   ```bash
   php -S localhost:8000 -t public
   ```
   Open <http://localhost:8000>.

### XAMPP / Apache
Point the virtual host's `DocumentRoot` to the `public/` folder. If you just drop the project into `htdocs`, set `'base_url' => '/springool-transport/public'` in `config/config.php` (the `.htaccess` files block direct access to `src/`, `config/`, `database/`, `bin/`).

### Upgrading a database from the first version
Back it up, then follow the comments in `database/upgrade.sql` (adds the `login_attempts` table and unique indexes). Note that trips in the past are no longer shown or bookable — update their dates in the admin panel.

---


## 🗺️ Roadmap

- [ ] Automated tests (PHPUnit) for the booking service
- [ ] Store the price on the booking (currently derived from the trip)
- [ ] Pagination in the admin tables
- [ ] E-mail notifications on booking confirmation
- [ ] Trust-proxy setting for correct client IPs behind a reverse proxy

---

## 📄 License

Educational project — free to use as a learning reference.
