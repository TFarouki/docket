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

## 2026-01-23 - Global Appends Overhead
**Learning:** Models with global `$appends` (like `User::$appends = ['profile_photo_url']`) trigger accessor logic on *every* serialization, even for simple ID/Name dropdowns. This adds unnecessary computation and payload size.

**Action:** Use `->makeHidden(['attribute'])` on the collection or model when fetching lists where the appended attribute is unused.
