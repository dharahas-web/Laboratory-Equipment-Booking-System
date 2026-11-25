# Laboratory Equipment Booking and Maintenance System

A PHP + MySQL starter application with separate Admin and Student portals, a shared database and a consistent premium dark interface.

## Requirements
- XAMPP (Apache + MySQL), PHP 8.1+ recommended
- VS Code or another editor
- Browser

## Installation
1. Extract `laboratory-booking-system.zip`.
2. Copy the `laboratory-booking-system` folder into `C:\xampp\htdocs\`.
3. Start **Apache** and **MySQL** in XAMPP.
4. Open `http://localhost/phpmyadmin`.
5. Choose **Import**, select `database/laboratory_booking_system.sql`, and run it.
6. Check `config/database.php`. This workspace uses MySQL port `3307` because port `3306` is already occupied. Default XAMPP credentials are user `root` and a blank password. Set `DB_PORT=3306` if your XAMPP MySQL uses its default port instead.
7. Open `http://localhost/laboratory-booking-system/`.

The database schema is MySQL/MariaDB SQL. The workspace treats the schema dump as plain text so IBM's Db2-only SQL checker does not report incompatible-dialect errors; import it with MySQL or phpMyAdmin as described above.

If port 3306 is already in use, this workspace includes VS Code tasks to start XAMPP MySQL on port 3307 and the PHP app server with `DB_PORT=3307`. Run **Run XAMPP MySQL on 3307** first. For a first-time database setup on that port, import the schema from PowerShell in the project folder:
```powershell
Get-Content -Raw .\database\laboratory_booking_system.sql | & 'C:\xampp\mysql\bin\mysql.exe' --protocol=tcp --host=127.0.0.1 --port=3307 --user=root
```
Then run **Run Laboratory Booking System**.

## Demo accounts
- Admin: `admin@labdesk.local` / `Admin@123`
- Student: `student@labdesk.local` / `Student@123`

If the demo login fails because your PHP password implementation differs, create a fresh admin password hash using the secure helper below and update the row.

## Create a secure admin account
After importing the SQL, run this one-time command from a terminal in the project directory:
```bash
php -r "echo password_hash('YourStrongPasswordHere', PASSWORD_DEFAULT), PHP_EOL;"
```
Copy the generated hash and execute in phpMyAdmin:
```sql
UPDATE users
SET password_hash = 'PASTE_GENERATED_HASH_HERE'
WHERE email = 'admin@labdesk.local';
```
Delete or change the demo accounts before deploying this project beyond a local classroom demonstration.

## Main modules included
- Role-based login and student registration
- Admin dashboard with database-derived counts and a chart
- Equipment create/list/delete
- Booking request submission, admin approval/rejection/completion/cancellation
- Booking time-slot quantity check during approval
- Student booking list and pending-request cancellation
- Student CSV export of booked equipment and booking details
- Student issue reports and admin maintenance status updates
- Notifications, laboratory records, student listing and CSV booking report
- CSRF tokens, PDO prepared statements, password hashing, output escaping

## Important project notes
This is a classroom-ready foundation, not a production-certified system. Review and test all workflows on your XAMPP/PHP/MySQL versions. The demo intentionally keeps the code understandable. Image upload, PDF export, QR generation, advanced profile administration, and full maintenance audit history are represented in the project specification but are not fully implemented in this starter build. Booking requests are checked again on approval; have your instructor review simultaneous multi-admin approval testing before real deployment.
