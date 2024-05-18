# SACCO Members Management System

This project is a Members Management System for a local SACCO (Savings and Credit Cooperative Organization). The system manages member data and user management with role-based access control and audit trails.

## Technologies Used

- HTML
- CSS
- JavaScript
- Bootstrap
- PHP
- MySQL

## Features

- **User Management with Role-Based Access Control**:
  - Admins can create, update, and delete users.
  - Users have roles that control their access level.
- **Audit Trails**:
  - For tracking changes made within the system.
- **Secure Password Management**:
  - Passwords are hashed using bcrypt for security.
- **Branch Management**:
  - Likely handles multiple branches or locations.
- **Initial Setup Script**:
  - Simplifies first-time deployment.

## Installation

1. **Clone the repository:**

   ```
   git clone https://github.com/nikodimos/sacco-management-system.git
   cd sacco-management-system
   ```

2. **Create the Database:**
   - Create a MySQL database named `sacco_db`.
   - Update the `database.php` file with your database credentials.
   - Run the Initialization Script:
     - Open your browser and navigate to `index.php`.
     - Follow the prompts to set up the database and tables.
   - Secure the Initialization Script:
     - After running the initialization script, delete or rename `initialize.php`.

3. **Default Admin Account**:
   - Username: `admin`
   - Password: `123456`
   - Upon first login, you will be prompted to change the password.

## Usage

1. **Login**:
   - Navigate to the login page and use the default admin credentials.
   - Change the default password on first login.

2. **User Management**:
   - Admins can create, update, and delete users.
   - Users have roles that control their access level.

## License

This project is licensed under the MIT License.

## Contact

For questions or suggestions, please contact `nikodimosenyew@gmail.com`.
