# New Feature: Advanced Model Settings & Document Management

## Overview
We have implemented advanced system configuration capabilities that allow the `root` user to dynamically enable or disable features like **Soft Deletes** and **User Tracking** (Blameable) for specific models. Additionally, the Document Management system has been upgraded with a searchable, dynamic category selection.

## 1. System Settings (Root Only)
A new "System Settings" page is available for users with the `root` role.

### Features
-   **Soft Delete Toggle**: Enable/Disable soft deletes per model.
    -   *Enabled*: Records are flagged as deleted (`deleted_at`) but remains in DB.
    -   *Disabled*: Deletions are permanent.
-   **User Tracking Toggle**: Enable/Disable automated tracking of `created_by` and `updated_by`.

### Usage
1.  Navigate to **Administration > System Settings** in the sidebar (visible only to Root).
2.  Toggle the switches for desired models (e.g., Users, Matters, Documents).
3.  Changes take effect immediately for all subsequent operations.

## 2. Professional File Documents
The User Edit page now includes an enhanced "Professional File" section.

### Features
-   **Searchable Select**: The "Document Category" dropdown is now searchable.
-   **Add New Category**: If a category doesn't exist (e.g., "Medical Certificate"), type it in the search box and click **Add "Medical Certificate"**. It will be created instantly and selected.
-   **Validation**: Categories are unique.

## 3. Technical Implementation
-   **Start/Stop Features**: A customized `HasDynamicFeatures` trait intercepts model booting and applies Global Scopes based on the `model_settings` table.
-   **Performance**: Feature flags are cached per request to minimize DB queries.

## 4. Fixes
-   Resolved a "Blank Page" issue caused by missing role data in the initial page load payload.
-   Standardized `deleted_at`, `created_by`, `updated_by` columns across core tables.
