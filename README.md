# EduPulse - School Management System

Welcome to EduPulse, a comprehensive, web-based school management system designed for Primary and Secondary schools in Uganda.

## 1. Prerequisites

- **XAMPP for Windows**: Ensure you have XAMPP installed, which includes Apache, MariaDB (MySQL), PHP, and phpMyAdmin.
- **Composer**: You will need Composer for PHP dependency management.

## 2. XAMPP Setup and Installation

**Step 1: Place Project Files**
- Place the entire `edupulse` project folder inside the `htdocs` directory of your XAMPP installation. The final path should be `C:\xampp\htdocs\edupulse`.

**Step 2: Configure `php.ini`**
- Open the XAMPP Control Panel.
- For the Apache module, click `Config` -> `PHP (php.ini)`.
- Uncomment (remove the semicolon `;`) the following extensions:
  ```ini
  extension=gd
  extension=zip
  extension=pdo_mysql
  extension=openssl
  ```
- Update the following values to handle file uploads:
  ```ini
  upload_max_filesize=10M
  post_max_size=10M
  memory_limit=256M
  ```
- Save and close the `php.ini` file.

**Step 3: Start Apache and MySQL**
- In the XAMPP Control Panel, click `Start` for both the **Apache** and **MySQL** modules.

**Step 4: Create and Import the Database**
- Open your web browser and go to `http://localhost/phpmyadmin`.
- Click on the `Databases` tab.
- In the "Create database" field, enter `edupulsedb` and click `Create`.
- Select the new `edupulsedb` database.
- Click on the `Import` tab.
- Click `Choose File` and select the `edupulsedb.sql` file from the project's root directory.
- Click `Go` to import the schema and sample data.

**Step 5: Install Backend Dependencies**
- Open a command prompt or terminal.
- Navigate to the project directory:
  ```sh
  cd C:\xampp\htdocs\edupulse
  ```
- Run the Composer installer:
  ```sh
  composer install
  ```
- This will create a `vendor` directory with all required PHP libraries.

## 3. Configuration

The main configuration file is `includes/config.php`. You must edit this file to match your environment.

- **Database Credentials**: Ensure `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` are correct for your XAMPP setup.
- **Pusher Credentials**: Add your keys for the real-time chat functionality.
- **SMTP Settings**: Configure for sending emails.

## 4. Accessing the Application

Once setup is complete, access the system at:
`http://localhost/edupulse`

**Sample Login Credentials:**
- **Superadmin**:
  - Email: `superadmin@edupulse.com`
  - Password: `password` (Note: The SQL file uses a placeholder hash. You may need to update the password hash in the `users` table manually for the first login).
- **Headteacher (St. John's)**:
  - Email: `headteacher.sjss@edupulse.com`
  - Password: `password`

## 5. License Enforcement

This is a commercial product. Access is controlled by a license key system managed in the `licenses` table in the database. The check is performed in `includes/config.php`. Ensure your school has a valid, active license key from the developer to use the software.
