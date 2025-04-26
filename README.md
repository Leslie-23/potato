# 🏋️‍♂️ Gym Management System in PHP MySQL

The **Gym Management System** is a web-based application developed using **PHP and MySQL**. It is designed to simplify the operational activities of a gym center by streamlining the management of customers, staff, payments, services, and equipment. The system includes three key access levels: **Admin**, **Staff**, and **Customer**, each with role-specific features to ensure efficient workflow and communication.

---

## 📌 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [System Architecture](#system-architecture)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [User Roles and Access](#user-roles-and-access)
- [Modules Overview](#modules-overview)
- [Reports and Analytics](#reports-and-analytics)
- [Screenshots](#screenshots)
- [License](#license)
- [Credits](#credits)

---

## 📖 About the Project

This Gym Management System allows gym administrators to manage customer registrations, track payments, assign packages, monitor equipment usage, maintain attendance records, and oversee staff activities. The system ensures centralized control with distinct modules for different users:

- **Customers** register online, manage their to-do list, view announcements, and monitor fitness progress.
- **Staff** handle member attendance, payment processing, and customer alerts.
- **Admins** oversee the entire system, manage equipment, generate reports, and broadcast announcements.

---

## 🚀 Features

### 🔐 Core Panels

- **Admin Panel**: Full system access and control.
- **Staff Panel**: Operational access with permission-based visibility.
- **Customer Panel**: Self-service access for members.

### 🧑‍🤝‍🧑 Member Management

- Online Registration (Approval Required)
- Track To-Do Lists & Reminders
- View Membership Status: Active, Expired, Pending

### 🧑‍💼 Staff Management

- Admin-Controlled Registration
- View Member Status, Equipment, Attendance
- Manage Payments and Alerts

### 💳 Payment & Subscription

- Customizable Plans (Monthly, Yearly)
- Receipt Generation with Print Option
- Alerts for Due Payments

### 📢 Communication

- System-wide Announcements
- Notifications & Alerts for Customers

### 📊 Reporting and Analytics

- Income, Expenses, and Service Reports
- Member and Progress Reports
- Bar Charts for Visual Insights

### 🏋️ Equipment and Attendance

- Add/Edit Gym Equipment
- Attendance Logs (Check-in/Check-out)
- Track Equipment Purchases & Vendors

### 📈 Customer Fitness Tracking

- Weight and Body Type Logging
- Calculate Progress (%)
- View Printable Reports

---

## 🧱 System Architecture

```text
Frontend: HTML, CSS (Bootstrap), JavaScript
Backend: PHP (Core PHP - No Framework)
Database: MySQL
Session Handling: PHP Sessions
```

---

## 💡 Technologies Used

- **PHP 7.x+**
- **MySQL 5.x+**
- **Bootstrap 4/5**
- **Vanilla JavaScript**
- **HTML5/CSS3**

---

## 🛠 Installation

1. **Clone or Download the Repository**

   ```bash
   git clone https://github.com/your-username/gym-management-system.git
   ```

2. **Set Up Database**

   - Import the provided `gym.sql` file into your MySQL server.

3. **Configure the Environment**

   - Open `config.php` and update database credentials:
     ```php
     $host = "localhost";
     $user = "root";
     $password = "";
     $dbname = "gym";
     ```

4. **Run the Project**
   - Place the project folder inside `htdocs/` (if using XAMPP).
   - Start Apache and MySQL services.
   - Access via `http://localhost/gym-management-system/`.

---

## 🔑 User Roles and Access

| Role     | Access Privileges                                      |
| -------- | ------------------------------------------------------ |
| Admin    | Full access to all system modules                      |
| Staff    | Limited access to manage members, payments, attendance |
| Customer | Register, view reports, to-do list, announcements      |

---

## 📦 Modules Overview

- **Login & Registration**
- **Dashboard with Key Metrics**
- **User Profile Management**
- **Attendance and Check-In**
- **Package and Service Selection**
- **Progress Tracking and Visualization**
- **Expense & Income Ledger**
- **Print-Ready Payment Receipts**

---

## 📉 Reports and Analytics

- **Overall Dashboard Summary**
- **Service and Package Subscription Stats**
- **Income vs Expense Charts**
- **Member Reports (Downloadable)**
- **Customer Progress Visualization (Graphs)**

---

## 🖼️ Screenshots

> _Screenshots will be available in `/screenshots/` folder if provided._

- Admin Dashboard
- Customer Registration Panel
- Attendance Tracking View
- Payment Receipts Generation
- Reports and Analytics Visualization

---

## 📄 License

This project is available under the **MIT License** – you are free to modify, distribute, and use the software for both commercial and non-commercial purposes.

---

## 🙏 Credits

- Project Source and Initial Design: [CodeAstro.com](https://codeastro.com)
- UI Framework: [Bootstrap](https://getbootstrap.com/)
- Chart Visualizations: Chart.js (if integrated)

---
