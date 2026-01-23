## 2026-02-12 - [Critical] IDOR in Document Downloads
**Vulnerability:** The `DocumentController::download` endpoint allowed any authenticated user to download any document by ID, bypassing all authorization checks. The `store` and `destroy` endpoints were also unrestricted.
**Learning:** Default resource controllers (or custom actions on them) often lack implicit authorization unless `authorizeResource` or specific middleware is used. Relying on "obscure" file paths (hashes) is insufficient if the retrieval endpoint exposes the file via predictable IDs.
**Prevention:** Always ensure that file download endpoints verify that the current user has permission to view the specific document or the entity it is attached to. Explicitly check permissions like `view documents` or `view matter`.

## 2026-02-12 - [Critical] Privilege Escalation in User Management
**Vulnerability:** The `users` resource route was missing authorization middleware, allowing any authenticated user to access user management functions (create, edit, delete users) and potentially escalate privileges to Admin/Root.
**Learning:** Resource controllers defined in `routes/web.php` do not inherit authorization checks automatically. Middleware must be explicitly applied either in the route definition or the controller constructor.
**Prevention:** Always verify that administrative routes (like `users`, `settings`) have explicit `middleware('can:permission_name')` or `middleware('role:role_name')` attached. Use automated tests to assert that unauthorized users receive 403 Forbidden on these routes.
