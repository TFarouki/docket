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

## 2026-01-25 - Hidden Appends Trap
**Learning:** Even when selecting specific columns (e.g., `get(['id', 'name'])`), Eloquent models will still serialize all attributes defined in `$appends`. This can lead to N+1 computation issues or bloated payloads (e.g., generating `profile_photo_url` for every user in a dropdown).

**Action:** When returning collections of models for simple lists (like dropdowns), always chain `->makeHidden(['appended_attribute'])` if the attribute is not needed.
