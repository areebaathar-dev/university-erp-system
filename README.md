# 🎓 University ERP System

A role-based University ERP (Enterprise Resource Planning) system for managing students, faculty, attendance, marks, and fee records — built with **PHP and MySQL**.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-8511FA?style=flat&logo=bootstrap&logoColor=white)

---

## 📖 Overview

University ERP System is a multi-role web application that centralizes academic and administrative operations for a university — covering admin, faculty, student, and accountant workflows in one system.

---

## ✨ Features

- Role-based login (Admin, Faculty, Student, Accountant)
- Student attendance management
- Marks and grading system
- Fee record management
- Centralized relational database
- Reporting modules per role

---

## 🛠️ Tech Stack

| Layer      | Technology         |
|------------|---------------------|
| Backend    | PHP                 |
| Database   | MySQL                |
| Frontend   | HTML, CSS, Bootstrap 5 |

---

## 🚀 Getting Started

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) or WAMP installed
- PHP 7.4+ and MySQL

### Installation
1. Clone the repo into your `htdocs` (XAMPP) or `www` (WAMP) folder:
```bash
   git clone https://github.com/areebaathar-dev/university-erp-system.git
```
2. Start Apache and MySQL from your XAMPP/WAMP control panel
3. Create a database and import the schema:
```bash
   mysql -u root -p university_erp < database/schema.sql
```
4. Copy the config template and add your local DB credentials:
```bash
   cp config/db.example.php config/db.php
```
5. Visit `http://localhost/university-erp-system` in your browser
6. Log in using a seeded role account (see `database/schema.sql` for default credentials)

---

## 📁 Project Structure
university-erp-system/
├── admin/           # Admin-only pages
├── faculty/         # Faculty dashboard & pages
├── student/         # Student dashboard & pages
├── accountant/      # Fee management pages
├── includes/        # Shared PHP components
├── assets/          # CSS, JS, images
└── database/        # SQL schema

---

## 📸 Screenshots

### Login Page
<img width="1920" height="955" alt="image" src="https://github.com/user-attachments/assets/6f01de31-e331-4bbc-95f0-fb30c79d959a" />

### Admin Dashboard
<img width="1899" height="962" alt="image" src="https://github.com/user-attachments/assets/6be96bfc-80d1-4d51-866d-09493512cb5e" />

### Accountant Dashboard
<img width="1893" height="958" alt="image" src="https://github.com/user-attachments/assets/adc24f7e-f5b4-42e2-ac85-cde83ffc6a0d" />

### Teacher Dashboard
<img width="1920" height="597" alt="image" src="https://github.com/user-attachments/assets/f92fd4d9-32d0-4457-9149-3f47c7afa6df" />

### Student Dashboard
<img width="1920" height="556" alt="image" src="https://github.com/user-attachments/assets/0c722223-44cf-48cf-87f1-c6def3aaf75f" />

---

## 🔭 Future Improvements

- Email/SMS notifications for fee due dates
- Exportable PDF report cards
- Two-factor authentication for admin accounts

---

## 📄 License

This project is licensed under the MIT License.

---

## 👩‍💻 Author

**Areeba Athar**
[LinkedIn](https://linkedin.com/in/areeba-athar) · [GitHub](https://github.com/areebaathar-dev)
