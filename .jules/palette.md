# Palette's Journal

## 2025-02-18 - Missing Loading States
**Learning:** The application uses Inertia.js form processing but often manually implements loading states (opacity/disabled) on buttons without visual feedback like spinners. This leads to code duplication and a less responsive feel.
**Action:** Implement a reusable `loading` prop on `PrimaryButton` (and eventually others) that handles the disabled state and shows a spinner automatically. This simplifies consumer code and improves consistency.

## 2025-02-18 - Inconsistent Loading States
**Learning:** While `PrimaryButton` had a `loading` prop, `DangerButton` did not, leading to inconsistent manual implementation of loading states (opacity/disabled) in consumers like `Edit.vue` and `DeleteUserForm.vue`.
**Action:** Standardize `loading` prop across all button components to encourage consistent, accessible loading feedback (spinner + disabled state) and reduce boilerplate in views.
