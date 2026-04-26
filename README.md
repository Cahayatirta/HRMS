# HRMS Application

Human Resource Management System built with Laravel, Livewire, and Filament.

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   npm install
   ```
3. Copy `.env.example` to `.env` and configure your database
4. Run migrations and seed the database:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. Start the development server:
   ```bash
   php artisan serve
   npm run dev
   ```

## Test Accounts

The following test accounts are created by the seeder:

| Email | Password | Role | Division | Employee Status |
|-------|----------|------|----------|-----------------|
| **admin@company.com** | admin123 | Admin | Project Manager | Active |
| **john.doe@company.com** | password123 | User | Project Manager | Active |
| **jane.smith@company.com** | password123 | User | Developer | Active |
| **bob@company.com** | password123 | User | Designer | Active |
| **alice@company.com** | password123 | User | QA Tester | Active |

## Features

- **User Authentication** - Login/logout functionality with role-based access
- **Employee Management** - Manage employee records with divisions
- **Attendance Tracking** - Check-in/check-out system with location tracking
- **Task Management** - Assign and track tasks for employees
- **Client Management** - Manage client information and services
- **Meeting Scheduling** - Schedule meetings with users and clients
- **Filament Admin Panel** - Full-featured admin interface at `/admin`
- **Dashboard** - User dashboard at `/dashboard`

## Access Levels

### Admin Users
- Full access to all features
- Can access Filament admin panel at `/admin`
- Can manage all system resources

### Regular Users
- Can access Filament admin panel if they have an active employee record
- Can view and manage their own tasks and attendance
- Access restricted based on division permissions

## Technology Stack

- **Backend:** Laravel 11
- **Frontend:** Livewire (Volt), TailwindCSS 4.0, Flux UI
- **Admin Panel:** Filament 3.x
- **Authentication:** Laravel Sanctum + Session-based auth
- **Database:** MySQL/SQLite