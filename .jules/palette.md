# Palette's Journal

## 2025-02-18 - Missing Loading States
**Learning:** The application uses Inertia.js form processing but often manually implements loading states (opacity/disabled) on buttons without visual feedback like spinners. This leads to code duplication and a less responsive feel.
**Action:** Implement a reusable `loading` prop on `PrimaryButton` (and eventually others) that handles the disabled state and shows a spinner automatically. This simplifies consumer code and improves consistency.

## 2025-02-18 - DangerButton Consistency
**Learning:** While `PrimaryButton` supports a `loading` prop, `DangerButton` does not, leading to inconsistent implementations of delete actions where some manually handle opacity and others don't.
**Action:** Future task: Upgrade `DangerButton` to support the `loading` prop to match `PrimaryButton`'s API and behavior.
