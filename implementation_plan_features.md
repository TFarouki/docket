# Implementation Plan - Advanced Model Features & Searchable UI

## Objective
1.  Complete the implementation of searchable and filterable select components with "Add New" functionality.
2.  Implement a dynamic toggle system for **SoftDelete** and **UserTracking** (Blameable) features, manageable by the 'root' user.

## Proposed Changes

### 1. UI Enhancements (SearchableSelect)
-   Integrate `SearchableSelect.vue` into `Users/Edit.vue` for Document Categories.
-   Add frontend logic to dynamically add new categories via API.
-   Update Document components to use the new category IDs.

### 2. Feature Toggle Infrastructure
-   **Database Migration**: Create `model_settings` table to store which features are enabled for which models.
-   **Middleware/Middleware Service**: Implement a service/singleton to cache these settings for the current request.
-   **Dynamic Traits**:
    -   `DynamicSoftDeletes`: A trait that extends Laravel's `SoftDeletes` but conditionally applies the global scope based on `model_settings`.
    -   `UserTrackable`: A trait to handle `created_by`, `updated_by`, and `deleted_by` fields, conditionally active based on `model_settings`.

### 3. Database Updates for Tracking
-   Add `created_by`, `updated_by`, and `deleted_at` columns to relevant tables (Users, Matters, Parties, documents).

### 4. Admin UI (Root Only)
-   Create a "System Configuration" page where the root user can toggle these features per model.

## Detailed Steps

### Step 1: Complete SearchableSelect in Edit.vue
1.  Modify `Edit.vue` to import and use `SearchableSelect`.
2.  Add `createCategory` method to the script.
3.  Rewrite document listing to show `doc.category.name`.

### Step 2: Infrastructure for SoftDelete & Tracking
1.  Run migration for `model_settings`.
2.  Implement `app/Traits/HasDynamicFeatures.php`.
3.  Update models (User, Matter, Party) to use these traits.

### Step 3: Global Tracking Columns
1.  Create a migration to add `deleted_at`, `created_by`, `updated_by` to core tables.

### Step 4: Configuration UI
1.  Add `SystemSettingsController`.
2.  Create `Settings/Index.vue` with a matrix of Models vs Features.

## Verification Plan
1.  Test document category creation via SearchableSelect.
2.  Login as root, toggle SoftDelete for "Matters".
3.  Delete a matter and verify it remains in DB with `deleted_at`.
4.  Toggle off and verify it disappears (hard delete effectively).
5.  Verify `created_by` field is populated on new records.
