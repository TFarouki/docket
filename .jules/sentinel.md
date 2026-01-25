## 2026-02-12 - [Critical] IDOR in Document Downloads
**Vulnerability:** The `DocumentController::download` endpoint allowed any authenticated user to download any document by ID, bypassing all authorization checks. The `store` and `destroy` endpoints were also unrestricted.
**Learning:** Default resource controllers (or custom actions on them) often lack implicit authorization unless `authorizeResource` or specific middleware is used. Relying on "obscure" file paths (hashes) is insufficient if the retrieval endpoint exposes the file via predictable IDs.
**Prevention:** Always ensure that file download endpoints verify that the current user has permission to view the specific document or the entity it is attached to. Explicitly check permissions like `view documents` or `view matter`.

## 2026-02-12 - [Critical] Controller Constructor Middleware Unavailable
**Vulnerability:** `MatterController` exposed all actions to any authenticated user.
**Learning:** The project's base `Controller` class does not extend `Illuminate\Routing\Controller`, rendering `this->middleware()` in constructors ineffective/unavailable. This leads to missing auth checks if developers assume standard Laravel behavior.
**Prevention:** Use explicit inline checks (e.g., `abort_unless`) in every controller method or define permission middleware directly in route definitions (`routes/web.php`).
