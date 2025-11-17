🏡 DrewEnt: Rural Property Management System (RPMS)

The DrewEnt system is a low-cost, low-resource Enterprise Information System (EIS) designed specifically to address the property and tenancy management needs of users in rural and low-infrastructure environments.

The primary goal of DrewEnt is to provide a highly accessible, affordable, and effective digital tool to automate rent tracking, tenancy records, and asset management without reliance on expensive licensing or high-bandwidth resources.

📦 Comprehensive Modules & Functionality

The DrewEnt system is a hybrid EIS designed to manage both property rentals and event/tent equipment rentals, structured into 18 detailed modules across four functional areas:

1. Authentication & User Management

Controls system access and roles.

User Registration / Login / Logout

Role-Based Access Control (Admin, Manager, Property Officer, Tent Officer)

Password Reset / Update, User Activation / Deactivation

Activity Logs & Audit Trails

2. Dashboard Module

Provides a quick overview of business performance.

Total Properties & Occupancy Status

Pending Rent Payments & Tent Bookings Overview

Upcoming Events & Quick Stats (Revenue, Expenses, Maintenance Requests)

🏠 Property Management Modules

3. Property Registration & Management

Handles all core property details.

Add/Edit Property Details (Category, Features)

Upload Images/Documents

Property Availability & Occupancy Status

4. Unit & Room Management

For multi-unit buildings (apartments, shops, etc.).

Add/Edit Units / Rooms

Assign Units to Properties, Unit Pricing / Rent Setup

Unit Status (Vacant, Occupied, Under Maintenance)

5. Tenant Management

Handles individuals occupying/renting properties.

Add Tenant, Tenant Profile, and History

Tenant Agreements / Contracts & ID Attachments

Move-in & Move-out Records

6. Rent & Billing Management

Covers all property rent and billing needs.

Generate Monthly Rent Invoices and Receipts

Record Rent Payments (Cash, Mobile Money, Bank)

Rent Arrears / Overdue Tracking and Automated Reminders

Rent Increment Management

7. Property Maintenance & Repairs

Ensures properties stay in good condition.

Log and Track Maintenance Requests (Assignment, Progress)

Contractor/Supplier Management

Maintenance Cost Recording and History

8. Property Inspections Module

Regular inspection management.

Pre-Move-in, Pre-Move-out, and Periodic Inspections

Inspection Report Uploads and Photo Documentation

9. Property Financials & Accounting

Tracks all money related to property.

Income Tracking (Rent, Service Fees) and Expense Tracking (Repairs, Utilities, Taxes)

Property Profit & Loss Statements and Cash Flow Reports

🎪 Tent & Event Management Modules

10. Tent Inventory Module

Handles tents and event equipment assets.

Add Tent, Categories (Small, Large, VIP), Sizes & Colors

Accessories Management (chairs, tables, decor, lights)

Tent Condition & Maintenance / Repairs

11. Tent Booking & Rental Module

Manages reservations and bookings.

Create, Edit/Cancel Booking (Event Date & Location)

Booking Calendar & Confirmation Slip

Damage Deposit Management and Transport/Delivery Fees

12. Tent Pricing & Quotations Module

Handles pricing structure.

Pricing Setup (per day / per size / per category)

Custom Quotes, Discount Management, Tax / Service Charge Setup

13. Tent Dispatch & Return Module

Tracks logistics of rented tents.

Dispatch Authorization & Delivery Team Assignment

Pickup/Return Confirmation

Tent Condition Checks, Damage Assessment, and Damage Charges Estimation

💼 Shared Business Management Modules

14. Customer Management Module

Common database for both property and tent clients.

Customer Profiles, Contacts & Addresses

Rental History (property + tent)

Blacklisting / Suspension Records

15. Payments & Receipts Module

Handles all incoming business payments.

Payment Entry and Receipt Generation

Support for Multi-Payment Channels (Cash, MoMo, Bank, POS)

Refund Management and Payment History

16. Reports & Analytics Module

Generates crucial business insights.

Rent Collection, Property Occupancy, Monthly Revenue Reports

Tent Booking, Inventory, Maintenance Reports

Customer Activity and Aging (Arrears) Reports

17. Document Management Module

Organized storage for important files.

Contracts & Leases, Tenant Agreements, Tent Booking Contracts

Payment Receipts, Upload/Download Functionality

18. Settings & Configurations Module

System-level controls.

General Business Settings (Currency, Taxes)

User Roles & Permissions (system setup)

Backup & Restore, Print/Receipt Templates, Notification Settings

🛠️ Technology Stack

DrewEnt is engineered using a lightweight stack, prioritizing simplicity, stability, and speed:

Frontend: HTML5, Bootstrap (for responsive design), and JavaScript (for client-side interactivity).

Backend: Procedural PHP (lightweight server-side logic).

Database: MySQL (reliable and scalable open-source data management).

🚀 Installation & Setup

These instructions assume you have a basic web server environment (e.g., XAMPP, WAMP, or a low-cost VPS) with PHP and MySQL installed.

1. Database Setup

Create a new MySQL database named drewent_db (or rpms_db).

Import the initial database structure (e.g., rpms_schema.sql) into this new database. (Note: This file should be provided in the repository root.)

Update the database connection parameters in the main PHP configuration file (e.g., config.php) with your database name, username, and password.

2. File Deployment

Clone this repository or download the source code files.

Place all files in your web server's root directory (e.g., htdocs/ or www/).

3. Access

Open your web browser.

Navigate to the deployed location (e.g., http://localhost/ or your domain name).

📞 Support & Contribution

If you have suggestions, encounter bugs, or wish to contribute to the project's further development, please feel free to submit an issue or pull request.
