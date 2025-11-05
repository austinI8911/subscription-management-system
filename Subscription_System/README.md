# Subscription Management System

A PHP + MySQL web app to manage subscriptions, payments, and audit logs.

## Setup
1. Create a MySQL database named `subscription_system`.
2. Import `subscription_serviceDB.sql` (MySQL Workbench).
3. Place the `Subcription_Service_php` folder in `C:\xampp\htdocs\subscription_system`.
4. Start Apache in XAMPP.
5. Start your MySQL server.
6. If your MySQL uses a password, update the DB connection line at the top of each PHP file.
7. Visit `http://localhost/subscription_system_php/index1.php`.

## Included
- PHP source files
- `subscription_serviceDB.sql` (schema setup)
- `sample_data.sql` (data to populate database)
- `Subscription_serviceDB.mwb` (EER relational diagram)
- `Subscription_serviceDB.png` (EER diagram Screenshot)
- `Subscription_Management_Report.pdf` (full project report)

## Features
- View / Add / Update / Cancel subscriptions
- Automatic payment on new subscription
- View/Add payments
- Audit log for created/updated/canceled actions
