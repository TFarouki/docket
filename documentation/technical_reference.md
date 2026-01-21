# Docket - Technical Reference & Project Status

## 1. Project Overview
**Docket** is a comprehensive Law Firm Management System built with **Laravel 10+**, **Vue 3**, and **Inertia.js**. It manages the entire lifecycle of legal matters, from client intake (Parties) to case management (Matters), hearings, documents, and billing.

## 2. System Architecture

### 2.1 Technology Stack
-   **Backend**: Laravel Framework
-   **Frontend**: Vue.js 3 (Composition API) via Inertia.js
-   **Styling**: Tailwind CSS
-   **Authentication**: Laravel Breeze / Sanctum
-   **Authorization**: Spatie Laravel Permission
-   **Database**: MySQL / MariaDB

### 2.2 Directory Structure Highlights
-   `app/Extensions/`: Contains isolated logic for advanced optional features (e.g., `DynamicFeatures`).
-   `app/Models/`: Eloquent models with standardized traits.
-   `resources/js/Pages/`: Vue page components organized by module.
-   `resources/js/Components/`: Reusable UI components (buttons, inputs, `SearchableSelect`).

## 3. Core Modules

### 3.1 User Management & Roles
-   **System**: Role-based Access Control (RBAC).
-   **Roles**:
    -   `root` (Super Admin - System Config)
    -   `admin`, `owner`, `partner` (Firm Management)
    -   `employed-lawyer`, `trainee-lawyer`, `secretary`, `clerk` (Operational)
-   **Features**:
    -   Profile management with professional file upload.
    -   "Professional File" supports document categorization (e.g., CV, Contracts).

### 3.2 Parties (Clients & Opponents)
-   **Definition**: Individuals or organizations involved in legal matters.
-   **Types**: Client, Opponent, Other.
-   **Key Attributes**: Name, Phone, National ID (CNIE), Address.

### 3.3 Matters (Legal Files)
-   **Definition**: The central record for a legal case or file.
-   **Relationships**: Has many Parties (Clients/Opponents), Documents, Hearings.
-   **Attributes**: Reference No, Title, Year, Responsible Lawyer, Status, Type (Litigation, Procedure, Consultation).

### 3.4 Documents (Polymorphic)
-   **Architecture**: Uses a polymorphic `documents` table (`documentable_id`, `documentable_type`) to attach files to any model (Users, Matters, etc.).
-   **Categories**: Dynamic `DocumentCategory` model. Users can create new categories on the fly via the UI.
-   **Storage**: Files stored in `storage/app/public` (linked to `public/storage`).
-   **Validation**:
    -   Max size: 10MB.
    -   Allowed types: PDF, Images.
    -   UI Feedback: Real-time size display and error warnings.

## 4. Advanced Features Extension

### 4.1 Concept
To ensure security and modularity, "optional" high-level features are isolated in `app/Extensions/DynamicFeatures`. This prevents bloating the core models and allows the `root` user to toggle functionality at runtime without code changes.

### 4.2 Dynamic Features
Controlled via the **System Settings** page (Root only).

1.  **Soft Deletes**:
    -   **Behavior**: When enabled, records are not removed from the DB but marked with `deleted_at`.
    -   **Toggle**: Per-model (e.g., enable for Matters, disable for Logs).
2.  **User Tracking (Blameable)**:
    -   **Behavior**: Automatically fills `created_by` and `updated_by` columns with the current user ID.
    -   **Toggle**: Per-model.

### 4.3 Implementation Details
-   **Namespace**: `App\Extensions\DynamicFeatures`
-   **Trait**: `HasDynamicFeatures`
    -   Intercepts `boot` method of models.
    -   Checks `model_settings` table via `FeatureManager`.
    -   Dynamically applies `SoftDeletes` scope and Model Observers for tracking.
-   **Service**: `FeatureManager`
    -   Handles logic for checking feature status (Cached for performance).

## 5. UI Components

### 5.1 SearchableSelect (.vue)
A custom dropdown component that supports:
-   **Filtering**: Type to search within options.
-   **Dynamic Addition**: If a term is not found, an "Add [Term]" option appears (configurable via `allow-add` prop).
-   **Events**: Emits `add` event for parent to handle API creation of the new item.

### 5.2 Application Logo
-   Custom branding (`Docket_Logo.png`) integrated into the `AuthenticatedLayout`.
-   Typography: "D" is centrally overlaid on the icon, "ocket" follows with tight spacing.

## 6. Database Schema Summary
-   `users`: Core authentication + professional details.
-   `parties`: Client database.
-   `matters`: Case files.
-   `documents`: Polymorphic file attachments.
-   `document_categories`: Lookup table for file types.
-   `model_settings`: Configuration for dynamic features (`model_class`, `feature_name`, `is_enabled`).
-   `roles/permissions`: Spatie tables.
