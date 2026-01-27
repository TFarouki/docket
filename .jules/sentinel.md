## 2026-02-12 - [Critical] IDOR in Document Downloads
**Vulnerability:** The `DocumentController::download` endpoint allowed any authenticated user to download any document by ID, bypassing all authorization checks. The `store` and `destroy` endpoints were also unrestricted.
**Learning:** Default resource controllers (or custom actions on them) often lack implicit authorization unless `authorizeResource` or specific middleware is used. Relying on "obscure" file paths (hashes) is insufficient if the retrieval endpoint exposes the file via predictable IDs.
**Prevention:** Always ensure that file download endpoints verify that the current user has permission to view the specific document or the entity it is attached to. Explicitly check permissions like `view documents` or `view matter`.

## 2026-10-24 - [Critical] Missing Authorization in User Management
**Vulnerability:** The `UserController` resource routes were accessible to any authenticated user, allowing privilege escalation.
**Learning:** Placing routes inside an `auth` middleware group only checks for authentication, not authorization. Resource controllers do not automatically restrict access based on roles.
**Prevention:** Always attach specific permission middleware (e.g., `can:manage users`) to sensitive resource routes or use `authorizeResource` within the controller.
