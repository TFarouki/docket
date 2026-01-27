## 2026-02-12 - [Critical] IDOR in Document Downloads
**Vulnerability:** The `DocumentController::download` endpoint allowed any authenticated user to download any document by ID, bypassing all authorization checks. The `store` and `destroy` endpoints were also unrestricted.
**Learning:** Default resource controllers (or custom actions on them) often lack implicit authorization unless `authorizeResource` or specific middleware is used. Relying on "obscure" file paths (hashes) is insufficient if the retrieval endpoint exposes the file via predictable IDs.
**Prevention:** Always ensure that file download endpoints verify that the current user has permission to view the specific document or the entity it is attached to. Explicitly check permissions like `view documents` or `view matter`.

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
