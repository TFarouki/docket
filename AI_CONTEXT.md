# AI Agent Context: Project "Docket"

This document serves as a high-level briefing for any AI assistant resuming work on this project. It defines the core philosophy, domain logic, and architectural patterns of **Docket**.

## 🎯 Core Philosophy
Docket isn't just a CRM; it's a **Profit-Centric Legal Operating System**. 
- Every "Service" must be attributed to a "Responsible Lawyer".
- Financials are split between **Fees** (revenue) and **Expenses** (pass-through costs).
- Profitability = Fees - (Unreimbursed Expenses).

## 🏛️ Domain Entities & Flow

### 1. The Conversion Funnel
- **Reference (مراجع)**: A potential lead or one-time visitor.
- **Appointment (موعد)**: The intermediate step.
- **Client (موكل)**: A converted person who has an active service.
- **Matter (قضية/خدمة)**: The container for all work (Tasks, Documents, Finances).

### 2. Matter Types
- **Litigation (قضية)**: Linked to a `court_cases` record. Has a court number, opponent, and court stages.
- **Procedure (مسطرة/مهمة)**: Non-litigation legal work (e.g., Drafting a contract, Real estate filing).

### 3. Hierarchy
- Litigation cases are hierarchical. An **Appeal** is a child matter of a **First Instance** case. It should inherit parties and documents but maintain separate financial records.

## 🎨 UI/UX Guidelines
- **Theme**: Hostinger-inspired sleek UI.
- **Brand Color**: `#7220fe` (Purple) used for buttons, icons, and active states.
- **Font**: Inter / Figtree.
- **Layout**: Fixed **LTR** layout. Even in Arabic mode, the sidebar remains on the left, but the text content aligns accordingly.
- **Navigation**: Full-width AppBar with centered branding. Collapsible sidebar below the AppBar.

## 🛠️ Code Patterns
- **Inertia.js**: Used for all frontend interactions.
- **Laravel Settings**: System-wide settings (like `system_locale`) are stored in a `settings` table and cached.
- **Translations**: Managed via JSON files in `lang/`. Use the `$t()` helper in Vue templates.
- **Permissions**: Rely on Spatie's roles and permissions.

## 🏗️ Next Steps for the AI
Focus on the **Client Management Module**:
1. Implement the Client List with advanced filtering (Reference vs Client).
2. Create the "Add Client" modal with validation for local ID, phone, and type.
3. Link Clients to Appointments.
