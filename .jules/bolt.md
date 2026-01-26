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

## 2026-02-05 - Hidden Attributes in Collections
**Learning:** When a model has attributes in `$appends`, `get(['id', 'name'])` is NOT sufficient to exclude them from serialization. The computed attributes are still appended to every model instance in the collection, causing performance overhead (especially if they involve logic like `asset()` or `urlencode()`) and increasing payload size.

**Action:** Use `->makeHidden(['attribute_name'])` on the collection or query result to explicitly exclude appended attributes when they are not needed (e.g., for dropdowns).
