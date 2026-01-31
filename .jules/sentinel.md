## 2026-02-12 - [Critical] IDOR in Document Downloads
**Vulnerability:** The `DocumentController::download` endpoint allowed any authenticated user to download any document by ID, bypassing all authorization checks. The `store` and `destroy` endpoints were also unrestricted.
**Learning:** Default resource controllers (or custom actions on them) often lack implicit authorization unless `authorizeResource` or specific middleware is used. Relying on "obscure" file paths (hashes) is insufficient if the retrieval endpoint exposes the file via predictable IDs.
**Prevention:** Always ensure that file download endpoints verify that the current user has permission to view the specific document or the entity it is attached to. Explicitly check permissions like `view documents` or `view matter`.

## 2026-02-13 - [High] Missing Authorization in AppointmentController
**Vulnerability:** The `AppointmentController` was exposed via `Route::resource` without any middleware restrictions, allowing any authenticated user to create, edit, or delete appointments.
**Learning:** `Route::resource` in Laravel is convenient but dangerous if not paired with `authorizeResource` or wrapped in appropriate middleware groups. When explicit permissions exist (like `view appointments`, `edit appointments`), relying on a single group middleware is insufficient if granular control is needed.
**Prevention:** Always verify if a resource controller has authorization checks. If not, break the resource route into explicit routes with `can:permission` middleware or implement `authorizeResource` with a Policy.
## 2026-01-26 - [Critical] Privilege Escalation in User Management
**Vulnerability:** The `UserController` resource routes were exposed to all authenticated users without role checks. This allowed any logged-in user (e.g., a clerk) to create, modify, or delete other users, including administrators.
**Learning:** Middleware applied to a group (like `auth`) does not imply authorization for specific resources. Developers might assume that "admin-like" controllers are safe because they are "admin features", but routes are public by default to any authenticated user unless restricted.
**Prevention:** Enforce `can:permission` middleware directly on resource route definitions in `routes/web.php` (e.g., `Route::resource(...)->middleware('can:manage users')`). Implement comprehensive feature tests that explicitly assert `403 Forbidden` for unauthorized roles.
## 2026-02-12 - [Critical] Controller Constructor Middleware Unavailable
**Vulnerability:** `MatterController` exposed all actions to any authenticated user.
**Learning:** The project's base `Controller` class does not extend `Illuminate\Routing\Controller`, rendering `this->middleware()` in constructors ineffective/unavailable. This leads to missing auth checks if developers assume standard Laravel behavior.
**Prevention:** Use explicit inline checks (e.g., `abort_unless`) in every controller method or define permission middleware directly in route definitions (`routes/web.php`).
## 2026-01-24 - [Critical] Missing Authorization in Resource Controllers
**Vulnerability:** Core resource controllers like `PartyController`, `MatterController`, and `UserController` were completely unprotected, allowing any authenticated user (regardless of role) to perform CRUD operations.
**Learning:** The project relies on manual authorization checks inside controller methods rather than route-based middleware or global policies. Simply protecting routes with `auth` middleware is insufficient for Role-Based Access Control (RBAC).
**Prevention:** Explicitly enforce permissions at the beginning of every controller method (e.g., `if (!user->can('permission')) abort(403);`) or use `authorizeResource` in the constructor if policies are defined.
## 2026-02-12 - [Critical] Privilege Escalation in User Management
**Vulnerability:** The `users` resource route was missing authorization middleware, allowing any authenticated user to access user management functions (create, edit, delete users) and potentially escalate privileges to Admin/Root.
**Learning:** Resource controllers defined in `routes/web.php` do not inherit authorization checks automatically. Middleware must be explicitly applied either in the route definition or the controller constructor.
**Prevention:** Always verify that administrative routes (like `users`, `settings`) have explicit `middleware('can:permission_name')` or `middleware('role:role_name')` attached. Use automated tests to assert that unauthorized users receive 403 Forbidden on these routes.
## 2026-10-24 - [Critical] Missing Authorization in User Management
**Vulnerability:** The `UserController` resource routes were accessible to any authenticated user, allowing privilege escalation.
**Learning:** Placing routes inside an `auth` middleware group only checks for authentication, not authorization. Resource controllers do not automatically restrict access based on roles.
**Prevention:** Always attach specific permission middleware (e.g., `can:manage users`) to sensitive resource routes or use `authorizeResource` within the controller.

## 2026-02-14 - [High] Missing Authorization in Sub-Resource Controllers
**Vulnerability:** Helper controllers for Matters (`CourtCaseController` and `HearingController`) were exposed via `Route::resource` without authorization checks, allowing any authenticated user to modify legal cases.
**Learning:** Developers often secure the "parent" resource (Matters) but forget to secure the "child" resources that are managed separately via their own controllers.
**Prevention:** Audit all `Route::resource` definitions in `routes/web.php` to ensure they have appropriate `middleware('can:...')` attached, especially for sub-resources that modify critical data.

## 2026-02-14 - [Critical] Missing Authorization in DocumentCategoryController
**Vulnerability:** The `DocumentCategoryController` exposed `store` and `index` endpoints to any authenticated user, allowing potentially malicious or confused users to spam or pollute global document categories.
**Learning:** Even auxiliary resources like "categories" or "tags" that seem low-risk must have authorization checks if they modify global state. Do not assume that because a feature is "small" or "just a dropdown" it doesn't need protection.
**Prevention:** Apply `can:permission` middleware or manual authorization checks to all controller methods that modify state (`store`, `update`, `destroy`). Review all controllers in `routes/web.php` that lack specific permission middleware.

## 2026-02-14 - [High] Unrestricted Polymorphic File Upload
**Vulnerability:** `DocumentController::store` accepted arbitrary `documentable_type` and `documentable_id` without validation. This allowed users to attach files to any model in the system (e.g., restricted `SystemSetting` or other users' data) and potentially create arbitrary directories via `class_basename`.
**Learning:** Polymorphic relationships are powerful but dangerous if inputs are blindly trusted. Standard validation rules often miss dynamic class checks.
**Prevention:** Always whitelist allowed `documentable_type` values using `Rule::in()`. Validate that the referenced `documentable_id` actually exists in the corresponding table using a custom closure or explicit check.
