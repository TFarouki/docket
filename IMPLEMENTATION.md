# Implementation Plan - Law Firm Management System (Docket)

## 1. Client Journey & Domain Logic

### A. The "Reference" (المراجع) to "Client" (الموكل) Flow
1.  **Entry Point**: A new person is added as a "Reference" (Visitor/Lead).
2.  **Interaction**: An Appointment (موعد) is scheduled or an immediate consultation occurs.
3.  **Conversion**: If a service is agreed upon, they become a "Client". Existing data is preserved.
4.  **Service Assignment**: A Client can have 0 to many Services (Matters) over time.

## 4. UI/UX Refinement (Hostinger Style)

### AppBar (Top Navbar) Redesign
*   **Centered Branding**: Current Logo and App Name ("Docket") will be centered in the `appBar`.
*   **Component Swap (RTL Context)**:
    *   **Right (Start)**: User Profile Dropdown and Language Switcher.
    *   **Center**: App Logo + Name.
    *   **Left (End)**: Hamburger Button.
*   **Visual Style**: Sleek white background, subtle bottom border, and shadow. Purple (`#7220fe`) accents for the logo and active states.

### Sidebar (Collapsible)
*   **Header Removal**: Branding removed from Sidebar header to avoid redundancy.
*   **Collapsible Mechanism**: Hamburger button in AppBar will toggle the `isSidebarCollapsed` state.
*   **Widths**: 256px (64) expanded | 80px (20) collapsed.
*   **RTL Support**: Sidebar stays on the right side for Arabic locale.

### B. Service Types (Quality of Service)
The "Service" (or Matter/File) is the central container. It is created *after* the appointment.
*   **Type 1: Litigation (دعوى جارية)**
    *   Linked to a specific Court Case.
    *   Has stages, hearings, and opponents.
*   **Type 2: New Litigation (رفع دعوى)**
    *   Preparation phase to file a new case.
    *   Becomes "Litigation" once filed.
*   **Type 3: Procedure/Consultation (مهمة خاصة)**
    *   No court case involved (e.g., Real Estate Registration, Advice).

### C. Task Management
Tasks are the actionable items derived from the Service.
*   Examples: "Attend Hearing", "Draft Contract", "File Motion".
*   Tasks are linked to the Service.

### E. Financial Module (المالية)
Crucial for law firm sustainability.
*   **Fees (الأتعاب)**: The cost of the professional service.
*   **Expenses (المصاريف/الرسوم)**: Court fees, expert fees, administrative costs.
*   **Payments**:
    *   **Partial/Full**: Tracking installments.
    *   **Paid by Firm**: Expenses paid by the firm on behalf of the client (Reimbursement needed).
*   **Analysis**: Profitability = Total Fees - (Expenses not reimbursed).

## 2. Proposed Database Schema

### Core Tables
*   `clients`
    *   `type`: 'lead' (مراجع) | 'client' (موكل)
    *   `full_name`, `phone`, `national_id`, etc.
*   `appointments`
    *   `client_id`
    *   `date_time`, `status`, `notes`.
*   `matters` (The "Service" or "File")
    *   `client_id`
    *   `title` (e.g., "قضية ضد شركة X")
    *   `type`: 'litigation', 'procedure'
    *   `status`: 'open', 'closed', 'pending'
*   `court_cases` (If type is litigation)
    *   `matter_id`
    *   `court_name`, `case_number`, `opponent_name`.
*   `tasks`
    *   `matter_id`
    *   `title`, `due_date`, `status`
*   `documents` (Digital Archive)
    *   `matter_id`
    *   `file_path`, `type`, `uploaded_at`.

### F. Flexible Role & Permission System (إدارة الموارد البشرية والصلاحيات)
*   **Roles (Update)**:
    *   **Owner (صاحب المكتب)**.
    *   **Colleague/Partner (زميل/شريك)**: Can be assigned as the "Responsible Lawyer" for specific files.
    *   **Associate/Staff**: Works on tasks but doesn't necessarily "own" the file's revenue.
*   **Attribution & Profit**:
    *   Every "Service/File" MUST have a `responsible_lawyer_id`.
    *   Financial reports will split revenue based on this ID (Profit Center).

### G. Appointment & Task Management (المواعيد والمهام)
*   **Appointments**:
    *   Linked to Google Calendar or Internal Calendar.
    *   Status: Scheduled, Completed, Cancelled, No Show.
    *   Outcome: Can convert to a new "Service/Case".
*   **Tasks**:
    *   Not just a checklist. Needs "Assignee" (User) and "Reviewer".
    *   Types: "Hearing Attendance", "Drafting", "Submission", "Payment Collection".

## 2. Proposed Database Schema

### Core Tables
*   `users` (Staff)
    *   Standard Laravel Auth.
*   `roles` & `permissions` (Spatie Package)
    *   Many-to-Many relationship with `users`.
*   `matters` (The "Service" or "File")
    *   `client_id`
    *   `responsible_lawyer_id` (User ID - for profit attribution)
    *   `title`, `type`, `status`.
    *   ...
*   `clients`

### H. Case Lifecycle (Hierarchy & Reactivation)
Crucial: "Archived" does not mean "Dead".
*   **Hierarchy (التسلسل القضائي)**:
    *   **Appeal (استئناف)**: Creates a NEW File/Matter but *linked* as a "Child" to the Primary Court case.
    *   **Cassation (نقض)**: Linked to the Appeal case.
    *   **Data Inheritance**: The new file inherits parties and documents but has its own separate financial tracking and tasks.
*   **Reactivation from Archive**:
    *   **Execute Procedure**: Run a new procedure based on an old judgment.
    *   **Re-open**: If a case is sent back from Cassation, for example.
    *   **Related Cases**: Link a new independent case to an old archived one for reference.

## 3. Tech Stack & Setup
*   **Backend**: Laravel 11.x
*   **Frontend**: Vue 3 + Inertia.js + Tailwind CSS (v3)
*   **Database**: MySQL / MariaDB
*   **Auth**: Laravel Breeze
*   **Packages**: `spatie/laravel-permission`, `barryvdh/laravel-dompdf` (for invoices).
