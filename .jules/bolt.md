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
