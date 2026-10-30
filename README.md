# Smart Visitor Pass

## Project Description

Smart Visitor Pass is a web-based visitor registration and gate management system for organizations. It helps staff register visitors, generate QR-based visitor passes, record entry and exit activity, and monitor visitors currently inside the premises.

The system replaces manual visitor books with a centralized digital process that is faster, easier to search, and more reliable for security monitoring.

## Problem Statement

Many organizations manage visitors using paper-based registers or disconnected records. This can cause:

- Slow visitor registration at the gate
- Difficulty verifying visitor identity and purpose
- Inaccurate or incomplete entry and exit records
- Difficulty identifying who is currently inside the premises
- Time-consuming manual searches for visitor history
- Limited accountability for staff handling visitor access

Smart Visitor Pass addresses these problems through a secure digital visitor management workflow.

## Project Objectives

- Digitize visitor registration and record management
- Generate a unique pass for every registered visitor
- Support QR-code and manual NIC-based entry and exit verification
- Provide a live view of visitors currently inside the premises
- Maintain visit history for security and administrative review
- Separate Admin and Security staff access
- Improve the speed, accuracy, and accountability of gate operations

## Main Features

- Staff login with password authentication
- Role-based access for Admin and Security users
- Visitor registration with name, NIC, phone, email, purpose, and host details
- Automatic unique visitor pass generation
- Printable visitor pass with QR code
- QR-code gate scanning for entry and exit
- Manual NIC lookup as an alternative to QR scanning
- Prevention of duplicate open entries
- Validation of visitor details and NIC formats
- Live dashboard showing visitors currently inside
- Daily entry and exit statistics
- Visitor profile and pass viewing
- Visitor details editing before the first visit is recorded
- Visit history with entry and exit methods
- Admin management of staff accounts
- Responsive interface for desktop and smaller screens

## Technologies and Tools Used

- PHP
- MySQL
- PDO for database access
- HTML5
- CSS3
- JavaScript
- QRCode.js for QR-code generation
- XAMPP for local Apache and MySQL hosting
- phpMyAdmin for database administration
- Git and GitHub for version control and project submission

## Project Scope

### In Scope

- Visitor registration and profile management
- Digital pass generation and printing
- QR and manual gate verification
- Entry and exit tracking
- Dashboard monitoring and visit history
- Staff authentication and role-based access
- Admin staff account management
- MySQL database storage

### Out of Scope

- SMS or email notifications
- Biometric authentication
- Cloud hosting and multi-branch synchronization
- Automated identity-card scanning
- Mobile applications for Android or iOS
- Integration with external access-control hardware

## System Roles

### Administrator

- View the dashboard
- Register and manage visitors
- View visitor passes and history
- Manage staff accounts
- Access administrative visitor records

### Security Staff

- Log in to the system
- Register visitors
- View visitor passes
- Scan or manually verify visitor entry and exit
- Monitor current visitors and visit history

## Installation and Setup

### Requirements

- XAMPP or another PHP and MySQL server environment
- PHP 7.4 or later recommended
- MySQL or MariaDB
- A modern web browser
- Internet access for the QRCode.js CDN and Google Fonts used by the interface

### Steps

1. Copy the project folder into the XAMPP `htdocs` directory.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open the installation page in a browser:

   `http://localhost/visitor_pass/install.php`

4. Open the login page:

   `http://localhost/visitor_pass/index.php`

5. Use one of the default accounts created by the installer:

   | Role | Email | Password |
   | --- | --- | --- |
   | Administrator | `admin@vpass.com` | `admin123` |
   | Security | `guard@vpass.com` | `guard123` |

6. Change or disable default accounts before deploying the system in a real environment.

> The installer creates the `visitor_pass` database and the required `staff`, `visitors`, and `visits` tables. Run the installer only when setting up the project for the first time.

## Project Structure

```text
visitor_pass/
├── action.php       # Processes QR and manual entry/exit requests
├── admin.php        # Admin staff account management
├── config.php       # Database connection, sessions, and shared layout helpers
├── dashboard.php    # Live dashboard and visitor statistics
├── history.php      # Visit history
├── index.php        # Staff login page
├── install.php      # Database and initial account installer
├── logout.php       # Ends the current session
├── pass.php         # Displays and prints a visitor QR pass
├── register.php     # Registers or edits visitor details
├── scan.php         # Gate scanning interface
├── visitor.php      # Visitor profile view
├── visitors.php     # Admin visitor records
├── assets/
│   └── style.css    # Application styling
└── README.md        # Project documentation
```

## Security Notes

- Passwords are stored using PHP password hashing.
- Database operations use PDO prepared statements.
- User sessions are required for protected pages.
- Access to administrator functions is restricted by role.
- Default credentials should be changed before production use.
- The application should be deployed over HTTPS in a production environment.







## License

This project was developed for academic purposes.
