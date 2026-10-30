# Car Cylinder Maintenance Workshop App

A complete PHP/MySQL web application for managing a car cylinder maintenance workshop, built for XAMPP.

## Features

### Core Business Processes
- **Customer Management** - Register customers, track contact details and service history
- **Vehicle Registration** - Manage vehicles linked to customers with engine/chassis details
- **Job Card Management** - Create work orders, track cylinder inspection and repairs, assign technicians
- **Technician Management** - Employee profiles, skills, certifications, workload tracking
- **Inventory Management** - Spare parts tracking (Cylinder Heads, Valves, Pistons, Gaskets, etc.) with low stock alerts
- **Billing & Invoicing** - Labor + parts costs, VAT/tax calculation, printable invoices
- **Equipment Maintenance** - Workshop equipment tracking with maintenance schedules and reminders
- **Reports Dashboard** - Daily jobs, revenue reports, inventory reports, technician performance, customer history

## Installation (XAMPP)

1. Copy the `cylinder` folder to `C:\xampp\htdocs\`
2. Start Apache and MySQL from XAMPP Control Panel
3. Open browser and go to `http://localhost/cylinder/install.php`
4. Click "Install Database" to create tables and sample data
5. Go to `http://localhost/cylinder/index.php` to use the app

## Default Login
Authentication and role-based access control are enabled. Log in at `http://localhost/cylinder/login.php`.

| Username | Password | Role |
|----------|----------|------|
| admin | admin123 | Admin |
| manager | manager123 | Manager |
| reception | reception123 | Receptionist |
| technician | tech123 | Technician |

Change these passwords after first login. For existing installs, run `database/users.sql` once to create the `users` table and default accounts.

New staff can self-register from the **Create Account** tab on the login page. Self-registration is limited to **Receptionist** and **Technician** roles; Admin and Manager accounts must be created by an Admin from the Users page.

## Roles & Permissions
- **Admin** - Full access, including user management.
- **Manager** - All workshop operations and reports; no user management.
- **Receptionist** - Customers, vehicles, job cards, and invoices.
- **Technician** - Job cards and read-only inventory.

Navigation links and direct page access are enforced per role.

## Technology Stack
- **Frontend:** HTML5, Bootstrap 5, Bootstrap Icons
- **Backend:** PHP 8+
- **Database:** MySQL (MariaDB)
- **Server:** XAMPP (Apache)

## Database Structure
- customers, vehicles, technicians
- work_orders, work_order_parts, work_order_labor
- parts (inventory), invoices
- equipment, maintenance_logs
- users (authentication and roles)

## Workflow
1. Customer Arrives → Register Customer
2. Register Vehicle
3. Create Work Order/Job Card
4. Assign Technician
5. Cylinder Inspection & Repair
6. Add Parts & Labor
7. Generate Invoice
8. Mark as Paid → Close Job
9. Service History Saved

## Notes
- All amounts in Birr
- Default tax rate 16% (can be adjusted per invoice)
- Low stock alerts when quantity <= reorder level
- Equipment maintenance reminders for due/overdue items
