# VintageTask

A group task management system to smooth your group work experience.

## Features

- User authentication (login/signup)
- Task management
- Project collaboration
- Clean, vintage-inspired design

## Setup Instructions

### Prerequisites

- XAMPP or any PHP server with MySQL
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Installation

1. Clone or download this project to your XAMPP `htdocs` folder

2. Start Apache and MySQL from XAMPP Control Panel

3. Create the database:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the `database_setup.sql` file
   - Or run the SQL script manually

4. Configure database connection:
   - Check `config/database.php` for database settings
   - Default settings: host=localhost, username=root, password=(empty)

5. Access the application:
   - Open browser and go to: http://localhost/vintagetask
   - Or: http://localhost/[your-folder-name]

## Usage

1. Visit the landing page
2. Click "Sign Up" to create a new account
3. Login with your credentials
4. Start managing your group tasks

## Project Structure

```
vintagetask/
├── assets/
│   ├── css/
│   │   ├── index.css      (Landing page styles)
│   │   └── auth.css       (Login/Signup styles)
│   └── images/
├── config/
│   └── database.php       (Database configuration)
├── actions/               (Backend logic)
├── includes/              (Reusable components)
├── index.php             (Landing page)
├── login.php             (Login page)
├── signup.php            (Registration page)
└── database_setup.sql    (Database schema)
```