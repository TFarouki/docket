# Palette's Journal

## 2025-02-18 - Missing Loading States
**Learning:** The application uses Inertia.js form processing but often manually implements loading states (opacity/disabled) on buttons without visual feedback like spinners. This leads to code duplication and a less responsive feel.
**Action:** Implement a reusable `loading` prop on `PrimaryButton` (and eventually others) that handles the disabled state and shows a spinner automatically. This simplifies consumer code and improves consistency.

## 2025-02-18 - Inconsistent Loading States
**Learning:** While `PrimaryButton` had a `loading` prop, `DangerButton` did not, leading to inconsistent manual implementation of loading states (opacity/disabled) in consumers like `Edit.vue` and `DeleteUserForm.vue`.
**Action:** Standardize `loading` prop across all button components to encourage consistent, accessible loading feedback (spinner + disabled state) and reduce boilerplate in views.
## 2026-01-24 - Consistent Danger Button Feedback
**Learning:** Destructive actions (handled by DangerButton) lacked visual loading indicators, which is critical for preventing double-submissions on sensitive operations like deletion.
**Action:** Standardized `DangerButton` to match `PrimaryButton`'s API by adding a `loading` prop that automatically handles the spinner and disabled state.
## 2026-01-23 - Shared Form State
**Learning:** When using a single Inertia form instance for multiple actions (e.g., Update and Delete in the same view), the `processing` state is shared. This automatically synchronizes loading states (e.g., disabling both Save and Delete buttons when either is clicked), preventing conflicting actions and improving data integrity.
**Action:** Leverage this behavior in other forms with multiple actions instead of creating separate form instances, unless independent loading states are strictly required.
## 2026-01-27 - Button Loading States
**Learning:** `DangerButton` and `SecondaryButton` lacked the standard `loading` prop present in `PrimaryButton`, leading to inconsistent implementation of loading states across the app.
**Action:** Implemented `loading` prop in both components to match `PrimaryButton` and updated `Appointments/Edit.vue` to use it.
