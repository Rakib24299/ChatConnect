# ChatConnect 💬

A modern, lightweight, and responsive **Messenger & Social Networking Web Application** built with **PHP, MySQL, and Tailwind CSS**. Designed with a clean, flat beginner-friendly architecture featuring user chat, friend management, calling simulations, advertisement postings, and a dedicated admin control panel.

---

## 📌 Features Overview

### 👤 User Module
- **Authentication & Security:** User registration, secure sign-in with session management, and logout redirect to the public home page.
- **User Dashboard (`singinconnection.php`):** Central dashboard with easy navigation to all core features.
- **Messenger Chat (`chatt.php`):** Real-time conversational interface to chat with connected friends.
- **Discover & Add Friends (`addfriend.php` / `viewaddfriend.php`):** Search and discover members across the platform to send connection requests.
- **Friends List (`viewfriend.php`):** View all accepted friends and start direct conversations.
- **Video & Voice Calling (`callingpp.php`):** Interactive video and audio call stage.
- **Advertisements Hub (`ads.php`):** Browse and post marketplace ads with image uploads and price details.
- **Mutual Connections (`muatual.php`):** Explore mutual friendship networks and common contacts.
- **Theme Toggle:** Persistent Dark Mode & Light Mode switcher stored in `localStorage`.

### 🛡️ Admin Control Panel
- **Admin Authentication (`adminlogin.php`):** Dedicated secure login area for system administrators.
- **Admin Dashboard (`adminpanel.php` / `adminconnect.php`):** Real-time metrics overview (Total Registered Members, Active Ads, Total Messages).
- **Admin Information (`amininfo.php`):** View system administrator credentials and access authority.
- **View All Members (`view.php`):** View, filter, and inspect registered member records.
- **Search Member (`search.php`):** Direct member lookup by registered email address.

---

## 🛠️ Technology Stack

| Technology | Purpose |
|---|---|
| **PHP (>= 7.4 / 8.x)** | Backend logic, session handling, database querying (MySQLi & Prepared Statements) |
| **MySQL / MariaDB** | Relational Database Management (`messenger_db`) |
| **Tailwind CSS (CDN)** | Styling and responsive flat UI design (`rounded-none`, zero heavy shadows) |
| **JavaScript (ES6)** | Dark/Light theme switching, mobile sidebar toggles, interactive DOM handling |
| **Apache (XAMPP)** | Local web server environment |

---

## 📂 Project Structure

```text
ChatConnect/
│
├── index.php                 # Public entry point (redirects to desh.php)
├── desh.php                  # Public landing / home page
├── about.html                # About platform overview
├── singin.php                # User Sign In page
├── singup.php                # User Sign Up page
├── singupconnect.php         # User registration submission processor
├── singinconnection.php      # Authenticated User Dashboard
├── logout.php                # Session destruction and logout handler
│
├── chatt.php                 # Messenger Chat Interface
├── addfriend.php             # Discover & Add Friends page
├── addfriendprocess.php      # Friend request handling processor
├── viewfriend.php            # Friends list view
├── callingpp.php             # Voice & Video call interface
├── ads.php                   # Advertisements and marketplace postings
├── muatual.php               # Mutual friends network view
├── viewaddfriend.php         # Member search & discovery view
│
├── adminlogin.php            # Admin login authentication
├── adminpanel.php            # Admin main dashboard
├── amininfo.php              # Administrator profile info
├── view.php                  # Admin: View all registered members
├── search.php                # Admin: Search member by email
├── adminconnect.php          # Admin auth processor & stats hub
│
├── connection.php            # Database connection configuration (mysqli)
├── coon.php                  # Secondary database connection helper
├── README.md                 # Project documentation
└── UploadImageFile/          # Uploaded media and images directory
```

---

## 🚀 Installation & Setup Guide

### 1. Prerequisites
- Install **[XAMPP](https://www.apachefriends.org/)** (with PHP 7.4+ and MySQL).

### 2. Clone or Copy Project
Place the `ChatConnect` folder inside your XAMPP `htdocs` directory:
```text
C:\xampp\htdocs\ChatConnect
```

### 3. Start Apache & MySQL
- Open **XAMPP Control Panel**.
- Click **Start** for both **Apache** and **MySQL**.

### 4. Database Setup
1. Open your browser and navigate to **phpMyAdmin**: `http://localhost/phpmyadmin/`
2. Create a new database named **`messenger_db`**.
3. Create the necessary tables using the SQL statements below:

```sql
CREATE DATABASE IF NOT EXISTS messenger_db;
USE messenger_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    gender VARCHAR(20) DEFAULT 'Not specified',
    birthdate DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Default Admin (Password: admin123)
INSERT INTO admins (username, email, password) 
VALUES ('admin', 'admin@chatconnect.com', 'admin123')
ON DUPLICATE KEY UPDATE username=username;

-- 3. Messages / Chats Table
CREATE TABLE IF NOT EXISTS chats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    outgoing_msg_id INT NOT NULL,
    incoming_msg_id INT NOT NULL,
    msg TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 4. Advertisements Table
CREATE TABLE IF NOT EXISTS tb_ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    person_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    name VARCHAR(200) NOT NULL,
    price VARCHAR(50) NOT NULL,
    fees VARCHAR(50) DEFAULT '0',
    image VARCHAR(255) DEFAULT '',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 5. Access the Application
Open your web browser and visit:
```text
http://localhost/ChatConnect/
```

---

## 🔑 Default Credentials

### Admin Login
- **URL:** `http://localhost/ChatConnect/adminlogin.php`
- **Username / Email:** `admin` or `admin@chatconnect.com`
- **Password:** `admin123`

### User Login
- Register a new account via `singup.php` or sign in with your registered email and password via `singin.php`.

---

## 🎨 UI & Design Principles
- **Flat Beginner-Friendly Aesthetic:** Completely clean, rectangular elements with `rounded-none` borders and no drop-shadows.
- **High Contrast & Readability:** Standard slate/blue color scheme with dark mode support.
- **Consistent Footers & Headers:**
  - Left: `© 2024 ChatConnect. All rights reserved.`
  - Right: `Developed by Rakib`

---

## 📄 License & Credits
- **Project Name:** ChatConnect
- **Developed by:** Rakib
- **License:** Open Source for educational and personal use.
