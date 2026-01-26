# Palette's Journal

## 2025-02-18 - Missing Loading States
**Learning:** The application uses Inertia.js form processing but often manually implements loading states (opacity/disabled) on buttons without visual feedback like spinners. This leads to code duplication and a less responsive feel.
**Action:** Implement a reusable `loading` prop on `PrimaryButton` (and eventually others) that handles the disabled state and shows a spinner automatically. This simplifies consumer code and improves consistency.

## 2025-02-18 - Secondary Button Loading State
**Learning:** Secondary buttons often fade to 25% opacity when disabled. To make the loading spinner visible, we must use `!opacity-75` (important utility) to override the default disabled opacity while keeping the cursor as `wait`.
**Action:** When adding loading states to other components with default disabled styles, check for opacity conflicts and override if necessary for visibility.
