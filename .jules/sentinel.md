## 2026-02-12 - [Critical] IDOR in Document Downloads
**Vulnerability:** The `DocumentController::download` endpoint allowed any authenticated user to download any document by ID, bypassing all authorization checks. The `store` and `destroy` endpoints were also unrestricted.
**Learning:** Default resource controllers (or custom actions on them) often lack implicit authorization unless `authorizeResource` or specific middleware is used. Relying on "obscure" file paths (hashes) is insufficient if the retrieval endpoint exposes the file via predictable IDs.
**Prevention:** Always ensure that file download endpoints verify that the current user has permission to view the specific document or the entity it is attached to. Explicitly check permissions like `view documents` or `view matter`.

## 2026-01-26 - [Critical] Privilege Escalation in User Management
**Vulnerability:** The `UserController` resource routes were exposed to all authenticated users without role checks. This allowed any logged-in user (e.g., a clerk) to create, modify, or delete other users, including administrators.
**Learning:** Middleware applied to a group (like `auth`) does not imply authorization for specific resources. Developers might assume that "admin-like" controllers are safe because they are "admin features", but routes are public by default to any authenticated user unless restricted.
**Prevention:** Enforce `can:permission` middleware directly on resource route definitions in `routes/web.php` (e.g., `Route::resource(...)->middleware('can:manage users')`). Implement comprehensive feature tests that explicitly assert `403 Forbidden` for unauthorized roles.
