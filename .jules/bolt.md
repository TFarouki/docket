## 2026-01-21 - Eloquent OR Precedence
**Learning:** When using `orWhere` clauses inside a `when` callback (or any conditional query building), they must be grouped in a closure. Otherwise, they operate at the top level of the query, potentially overriding other `where` clauses (like `type = 'client'`) due to SQL precedence rules (AND binds tighter than OR).

**Action:** Always wrap `orWhere` chains in a closure:
```php
$query->where(function ($q) {
    $q->where('a', 1)->orWhere('b', 1);
});
```
Instead of:
```php
$query->where('a', 1)->orWhere('b', 1);
```

## 2026-01-27 - Model Appends persist despite Select limiting
**Learning:** Limiting columns in eager loading (e.g. `with('user:id,name')`) does NOT prevent attributes defined in `$appends` (like `profile_photo_url` on `User`) from being calculated and serialized. This happens because the model instance is still hydrated and serialized.

**Action:** To fully optimize payload size for models with appends:
1. Use `makeHidden(['attr'])` on the collection/model after retrieval (e.g., `User::get()->makeHidden(...)`).
2. For relationships, it's harder to apply `makeHidden` without iterating.
3. In Inertia tests, use `etc()` when asserting relation structures if you don't want to strictly assert the presence of these appended attributes.
## 2026-02-05 - Hidden Attributes in Collections
**Learning:** When a model has attributes in `$appends`, `get(['id', 'name'])` is NOT sufficient to exclude them from serialization. The computed attributes are still appended to every model instance in the collection, causing performance overhead (especially if they involve logic like `asset()` or `urlencode()`) and increasing payload size.

**Action:** Use `->makeHidden(['attribute_name'])` on the collection or query result to explicitly exclude appended attributes when they are not needed (e.g., for dropdowns).
## 2026-01-25 - Hidden Appends Trap
**Learning:** Even when selecting specific columns (e.g., `get(['id', 'name'])`), Eloquent models will still serialize all attributes defined in `$appends`. This can lead to N+1 computation issues or bloated payloads (e.g., generating `profile_photo_url` for every user in a dropdown).

**Action:** When returning collections of models for simple lists (like dropdowns), always chain `->makeHidden(['appended_attribute'])` if the attribute is not needed.
## 2026-01-24 - Hidden Appends for Dropdowns
**Learning:** Models with `$appends` (like `User`'s `profile_photo_url`) force accessor execution and payload inclusion even when selecting specific columns (e.g., `get(['id', 'name'])`). This bloats JSON responses for simple dropdowns.

**Action:** Use `->makeHidden(['attribute'])` on the collection when fetching lists for dropdowns to exclude unneeded appended attributes:
```php
User::get(['id', 'name'])->makeHidden(['profile_photo_url'])
```
## 2026-01-23 - Global Appends Overhead
**Learning:** Models with global `$appends` (like `User::$appends = ['profile_photo_url']`) trigger accessor logic on *every* serialization, even for simple ID/Name dropdowns. This adds unnecessary computation and payload size.

**Action:** Use `->makeHidden(['attribute'])` on the collection or model when fetching lists where the appended attribute is unused.
## 2026-01-22 - Pruning Unused Eager Loads
**Learning:** Eager loading relationships that are unused by the frontend (like `assignee` in `Appointments/Index`) wastes database queries and memory (hydrating full User models). Also, loading full models (like `Party`) when only specific fields (`id`, `full_name`) are needed increases payload size.

**Action:**
1. Check Vue/Blade templates to see what data is actually used.
2. Remove unused relationships from `with()`.
3. Use `relation:id,name` syntax to select only necessary columns.
