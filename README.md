em - Documentation

## 📌 Overview

The **Perfect Gym Management System** is a comprehensive **web-based application** designed to streamline gym operations including:

- Member management
- Equipment tracking
- Financial reporting
- Workout planning

It serves as a **data-driven platform** that connects a frontend landing page with backend administrative dashboards for staff and trainers.

---

## 👥 User Roles & Features

The system supports multiple user roles with tailored functionalities:

### 1. Members (Clients)

- **Registration & Profile Management**: Sign up, update personal details, and track fitness progress
- **Workout Plans**: Choose from predefined workout plans (`preferred_workout_plan_1`, `2`, `3`)
- **Attendance Tracking**: Check-in/check-out system with session logging
- **Payment & Subscriptions**: View payment history and manage plans (1, 3, 6, 12 months)

### 2. Trainers

- **Member Progress Tracking**: Monitor client weight, body changes, and fitness goals
- **Session Scheduling**: Assign workout plans and track training sessions
- **Equipment Usage**: Log usage and report maintenance issues

### 3. Cashiers / Front Desk

- **Membership Management**: Register members, renew subscriptions, process payments
- **Check-In System**: Record attendance and manage access

### 4. Managers / Admins

- **Financial Reporting**: View earnings vs expenses, revenue trends, payment methods
- **Equipment Maintenance**: Track repair costs and equipment ROI
- **Analytics Dashboard**: Gender distribution, service popularity, workout preferences
- **Staff Management**: Add/edit roles (admin, cashier, trainer)

---

## 🌐 Data-Driven Landing Page

The public landing page dynamically pulls data from the database to display:

- **Membership Plans** (pricing, features)
- **Popular Workout Programs** (based on user preference)
- **Trainer Profiles** (from `staffs` table)
- **Gym Statistics** (total members, success stories)
- **Announcements** (from `announcements` table)

### 📊 Example Query: Popular Workout Plans

```sql
-- Fetching popular workout plans for the homepage
SELECT w.workout_name, COUNT(mf.user_id) AS popularity
FROM workout_plan w
JOIN members_fitness mf ON w.table_id = mf.preferred_workout_plan_1
GROUP BY w.workout_name
ORDER BY popularity DESC
LIMIT 3;
```

---

## 📦 Database Structure

🗃️ Database Design & Best Practices
✅ Normalization (3NF Compliant)
1NF: Atomic values, no repeating groups

2NF: Full functional dependencies (e.g., members_fitness → user_id)

3NF: No transitive dependencies (equipment_repairs references equipment_id)

🔑 Indexing & Performance
Primary Keys: user_id, id

Foreign Keys: trainer_id, equipment_id

Indexes: Frequently queried fields (e.g., email, status)

📊 Optimized Queries
JOINs over subqueries where practical

COALESCE() for handling NULLs

Aggregate Functions: SUM, COUNT, GROUP BY for analytics

🔐 Security Measures
Hashed Passwords using password_hash() in PHP

SQL Injection Protection using mysqli_real_escape_string() or prepared statements

Session-Based Authentication for role access control

📈 Key Database Tables
Table Purpose
members Client profiles, subscriptions, attendance
members_fitness Fitness goals, workout preferences, progress
equipment Equipment status, purchase/maintenance costs
transactions Payments, refunds, renewals
training_sessions Trainer-client workout logs
workout_plan Definitions of workout programs (Cardio, Strength, etc.)

🚀 Future Improvements
📱 Mobile App Integration (check-ins, workout logs)

📧 Automated Email Reminders (payments, sessions)

📈 Predictive Analytics (e.g., forecasting growth)

🔗 Wearable Device API (Fitbit, Apple Health sync)

🔧 Setup Instructions
Database Import
Restore elitefit-23.sql to MySQL server

### Configuration

Edit dbcon.php with your MySQL credentials

### Deployment

Host via Apache/Nginx with PHP 8+

### Admin Login

`Use credentials in admin table`

Username: admin

Password: [hashed_password]

### 📜 Conclusion

The Perfect Gym Management System is a scalable, secure, and data-driven platform that unifies:

Member experiences

Staff workflows

Real-time analytics

With a normalized database structure and role-based security, it provides a strong foundation for digital gym operations and future integrations.

# Developed by: Leslie Paul Ajayi

Year: 2025
