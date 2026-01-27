## 2026-02-12 - [Critical] IDOR in Document Downloads
**Vulnerability:** The `DocumentController::download` endpoint allowed any authenticated user to download any document by ID, bypassing all authorization checks. The `store` and `destroy` endpoints were also unrestricted.
**Learning:** Default resource controllers (or custom actions on them) often lack implicit authorization unless `authorizeResource` or specific middleware is used. Relying on "obscure" file paths (hashes) is insufficient if the retrieval endpoint exposes the file via predictable IDs.
**Prevention:** Always ensure that file download endpoints verify that the current user has permission to view the specific document or the entity it is attached to. Explicitly check permissions like `view documents` or `view matter`.

## 2026-02-13 - [High] Missing Authorization in AppointmentController
**Vulnerability:** The `AppointmentController` was exposed via `Route::resource` without any middleware restrictions, allowing any authenticated user to create, edit, or delete appointments.
**Learning:** `Route::resource` in Laravel is convenient but dangerous if not paired with `authorizeResource` or wrapped in appropriate middleware groups. When explicit permissions exist (like `view appointments`, `edit appointments`), relying on a single group middleware is insufficient if granular control is needed.
**Prevention:** Always verify if a resource controller has authorization checks. If not, break the resource route into explicit routes with `can:permission` middleware or implement `authorizeResource` with a Policy.
