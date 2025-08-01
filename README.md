# EduPulse - School Management System

Welcome to EduPulse, a comprehensive, web-based school management system designed for Primary and Secondary schools in Uganda. This system is built with PHP, MySQL, and Bootstrap 5 to provide a modern, mobile-friendly experience for administrators, teachers, students, and parents.

## Table of Contents
1. [Features](#features)
2. [Technical Specifications](#technical-specifications)
3. [Prerequisites](#prerequisites)
4. [XAMPP Setup and Installation](#xampp-setup-and-installation)
5. [Configuration](#configuration)
6. [Accessing the Application](#accessing-the-application)
7. [License](#license)

---

### Features
EduPulse is packed with features to streamline school management, including:
- **Role-Based Dashboards**: Tailored interfaces for Superadmins, Headteachers, Teachers, Bursars, Students, and Parents.
- **Student & Teacher Management**: Easy-to-use forms and bulk upload options.
- **Academic Management**: Handle classes, subjects, marks, and generate custom report cards and student IDs.
- **Financial Tools**: Fee management with payment tracking and integration into student reports.
- **Real-time Communication**: A built-in chat system for Headteachers and Teachers, complete with stories.
- **AI-Powered Insights**: Predict student performance to identify at-risk and high-achieving students.
- **QR Code Attendance**: A modern solution for tracking student attendance.
- **And much more**: E-learning, parent portal, announcements, timetabling, and extracurricular activity management.

### Technical Specifications
- **Backend**: PHP 8.x
- **Database**: MySQL 8.x
- **Frontend**: HTML, CSS, JavaScript, Bootstrap 5, Chart.js, Font Awesome 6
- **Backend Dependencies**: Composer, PHPMailer, TCPDF, Pusher, Spout, FastRoute, PHP-ML

---

### Prerequisites
- **XAMPP for Windows**: A web server solution that includes Apache, MariaDB (MySQL compatible), PHP, and Perl. Download from [Apache Friends](https://www.apachefriends.org/index.html).

---

### XAMPP Setup and Installation
Follow these steps carefully to set up the EduPulse system on your local machine.

**Step 1: Install XAMPP**
- Install XAMPP to its default location at `C:\xampp`.

**Step 2: Place Project Files**
- Place the entire `edupulse` project folder inside the `htdocs` directory of your XAMPP installation. The final path should be `C:\xampp\htdocs\edupulse`.

**Step 3: Configure `php.ini`**
- Open the XAMPP Control Panel.
- For the Apache module, click `Config` -> `PHP (php.ini)`.
- This will open the `php.ini` file in a text editor.
- Uncomment (remove the semicolon `;` from the beginning) the following extensions:
  ```ini
  extension=gd
  extension=zip
  extension=pdo_mysql
  ```
- Update the following values to handle large file uploads. You can use the search function (Ctrl+F) to find them.
  ```ini
  upload_max_filesize=10M
  post_max_size=10M
  memory_limit=256M
  ```
- Save and close the `php.ini` file.

**Step 4: Start Apache and MySQL**
- In the XAMPP Control Panel, click `Start` for both the **Apache** and **MySQL** modules.

**Step 5: Create the Database**
- Open your web browser and go to `http://localhost/phpmyadmin`.
- Click on the `Databases` tab.
- In the "Create database" field, enter `edupulsedb` and click `Create`.

**Step 6: Import the SQL Database**
- Select the newly created `edupulsedb` database from the left-hand sidebar.
- Click on the `Import` tab.
- Click `Choose File` and select the `edupulsedb.sql` file located in the project's root directory (`C:\xampp\htdocs\edupulse\edupulsedb.sql`).
- Scroll down and click `Go`. This will create all the necessary tables and populate them with sample data.

**Step 7: Install Backend Dependencies**
- Open a command prompt or terminal.
- Navigate to the project directory:
  ```sh
  cd C:\xampp\htdocs\edupulse
  ```
- Run the Composer installer. You will need your XAMPP PHP executable to run it:
  ```sh
  C:\xampp\php\php.exe composer.phar install
  ```
  *Note: If you have Composer installed globally, you can simply run `composer install`.*
- This will create a `vendor` directory containing all the required PHP libraries.

---

### Configuration
The main configuration file is located at `includes/config.php`. You will need to edit this file to match your local environment and third-party service credentials.

- **Database Credentials**: Update the `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` constants.
- **Pusher Credentials**: Add your keys for the real-time chat functionality.
- **SMTP Settings**: Configure for sending emails (e.g., using a Gmail account).

---

### Accessing the Application
Once the setup is complete, you can access the EduPulse system by navigating to:
`http://localhost/edupulse`

**Sample Login Credentials:**
- **Superadmin**:
  - Email: `superadmin@edupulse.com`
  - Password: `password`
- **Headteacher (St. John's)**:
  - Email: `headteacher.sjss@edupulse.com`
  - Password: `password`

---

### License
This is a commercial product distributed under a proprietary license. Please see the `LICENSE` file for more details.

**Powered by EduPulse**
Copyright © 2025 [Your Developer Name/Organization]
