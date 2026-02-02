# Palette's Journal

## 2025-02-18 - Missing Loading States
**Learning:** The application uses Inertia.js form processing but often manually implements loading states (opacity/disabled) on buttons without visual feedback like spinners. This leads to code duplication and a less responsive feel.
**Action:** Implement a reusable `loading` prop on `PrimaryButton` (and eventually others) that handles the disabled state and shows a spinner automatically. This simplifies consumer code and improves consistency.

## 2025-02-18 - Secondary Button Loading State
**Learning:** Secondary buttons often fade to 25% opacity when disabled. To make the loading spinner visible, we must use `!opacity-75` (important utility) to override the default disabled opacity while keeping the cursor as `wait`.
**Action:** When adding loading states to other components with default disabled styles, check for opacity conflicts and override if necessary for visibility.
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
## 2026-01-28 - Consistent Empty States
**Learning:** Users often land on lists with no data (e.g., Appointments, Matters). A generic "No data" row is uninviting and offers no guidance.
**Action:** Implemented a reusable `EmptyState` component with an icon, encouraging message, and a clear call-to-action button to guide users to the "create" flow immediately. This reduces friction for new users or empty filters.
## 2026-02-17 - Immediate Search Feedback
**Learning:** Users often lack feedback during server-side filtering (debounced or not), leading to uncertainty if the search is working. Adding a loading spinner directly inside the search input provides immediate, localized feedback that is superior to a global progress bar for this context.
**Action:** Use a dedicated `SearchInput` component with a loading state prop connected to Inertia visit events (`onStart`/`onFinish`) to provide clear visual feedback during data fetching.

## 2026-02-18 - Searchable Select Accessibility
**Learning:** Custom select components often lack basic keyboard accessibility (tab to focus, enter to toggle). Adding these along with proper ARIA roles (combobox, listbox) transforms the component from 'mouse-only' to fully accessible.
**Action:** Ensure all custom form controls (Dropdowns, MultiSelects) implement `tabindex="0"`, proper role definitions, and keyboard event handlers for interaction.
