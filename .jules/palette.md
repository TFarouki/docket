# Palette's Journal

## 2025-02-18 - Missing Loading States
**Learning:** The application uses Inertia.js form processing but often manually implements loading states (opacity/disabled) on buttons without visual feedback like spinners. This leads to code duplication and a less responsive feel.
**Action:** Implement a reusable `loading` prop on `PrimaryButton` (and eventually others) that handles the disabled state and shows a spinner automatically. This simplifies consumer code and improves consistency.

## 2026-01-24 - Consistent Danger Button Feedback
**Learning:** Destructive actions (handled by DangerButton) lacked visual loading indicators, which is critical for preventing double-submissions on sensitive operations like deletion.
**Action:** Standardized `DangerButton` to match `PrimaryButton`'s API by adding a `loading` prop that automatically handles the spinner and disabled state.
