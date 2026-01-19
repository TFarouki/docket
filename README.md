# Docket - Law Firm Management System

Docket is a comprehensive, modern management system designed specifically for law firms. It streamlines the lifecycle of client intake, case management, digital archiving, and financial tracking with a focus on usability and professional aesthetics.

## 🚀 Tech Stack

- **Backend**: Laravel 11.x
- **Frontend**: Vue 3 + Inertia.js
- **Styling**: Tailwind CSS (Hostinger-inspired design)
- **Database**: MySQL / MariaDB
- **Auth**: Laravel Breeze (Interia/Vue)
- **Permissions**: Spatie Laravel-Permission
- **Internationalization**: Support for Arabic, English, and French.

## ✨ Key Features

- **Client Journey**: Manage the flow from initial lead (Reference/مراجع) to Appointment, then Conversion to Client and Service assignment.
- **Matter Management**: Separate flows for Litigation (Court Cases) and Procedures (Consultations/Legal Services).
- **Hierarchical Cases**: Track appeals (استئناف) and cassations (نقض) as child matters of a primary case.
- **Financial Module**: Track professional fees vs. administrative expenses, payment installments, and net profitability per case.
- **Digital Archive**: Secure electronic storage for case-related documents.
- **Role-Based Access**: Flexible role system (Owner, Partner, Associate, Secretary, etc.) with granular permissions.

## ⚙️ Setup Instructions

### Prerequisites
- PHP 8.2+
- Node.js & NPM
- Composer
- MySQL/MariaDB

### Installation

1. **Clone the repository**:
   ```bash
   git clone [repository-url]
   cd docket
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Install JS Dependencies**:
   ```bash
   npm install --legacy-peer-deps
   ```

4. **Environment Configuration**:
   ```bash
   cp .env.example .env
   # Update DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
   ```

5. **Generate App Key**:
   ```bash
   php artisan key:generate
   ```

6. **Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

7. **Run Development Servers**:
   ```bash
   # Terminal 1
   php artisan serve
   
   # Terminal 2
   npm run dev
   ```

## 🌐 Localization
The system is configured to support AR/EN/FR. To facilitate management, the layout is fixed to **LTR (Left-to-Right)** even for Arabic, ensuring a consistent design experience across all languages.
