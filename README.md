# University ERP System

A multi-role Enterprise Resource Planning (ERP) system for university administration, built with PHP and MySQL. Supports distinct dashboards and permissions for admins, staff, teachers, and students, with a focus on secure data handling and a clean, modern interface.

## Features

- **Multi-role access control** — separate dashboards and permissions for Admin, Teacher, and Student roles
- **Fee management** — printable, styled fee receipts
- **Academic tracking** — student records, results, and course management
- **Interactive data tables** — sortable, searchable tables via DataTables
- **Data visualization** — charts and analytics via Chart.js
- **Modern UI** — dark sidebar navigation built on Bootstrap 5
- **User-friendly alerts** — SweetAlert2 for confirmations and notifications
- **Secure database access** — parameterized/prepared statements throughout to prevent SQL injection

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP |
| Database | MySQL |
| Frontend | Bootstrap 5, HTML, CSS, JavaScript |
| UI Libraries | Chart.js, DataTables, SweetAlert2 |

## Getting Started

### Prerequisites
- PHP 7.4+ (or XAMPP/WAMP/MAMP for local development)
- MySQL 5.7+
- A web server (Apache/Nginx) or PHP's built-in server

### Installation

1. Clone the repository
   ```bash
   git clone https://github.com/areebaathar-dev/university-erp-system.git
   cd university-erp-system
   ```

2. Import the database
   - Create a MySQL database
   - Import the provided `.sql` schema file into it

3. Configure database credentials
   - Copy the sample config file and update it with your database name, username, and password

4. Run the app
   - Point your local server (Apache/XAMPP) to the project folder, or run:
     ```bash
     php -S localhost:8000
     ```
   - Visit `http://localhost:8000` in your browser

## Project Structure

```
university-erp-system/
├── admin/          # Admin dashboard and controls
├── teacher/        # Teacher dashboard and controls
├── student/         # Student dashboard and controls
├── assets/          # CSS, JS, images
├── includes/        # Shared PHP includes (DB connection, auth, helpers)
└── uploads/          # User-uploaded content
```

## Security Notes

This project uses prepared statements for all database queries to prevent SQL injection, and validates/escapes user input across forms.

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.
