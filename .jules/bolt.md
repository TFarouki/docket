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
