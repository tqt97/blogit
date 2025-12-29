# Password Input Component

A reusable password input component with built-in visibility toggle, designed for authentication forms (Login, Register, Reset Password, etc.).

This component extends the base `Input` component and adds a secure, accessible password visibility toggle with optional advanced behaviors.

---

## Overview

`PasswordInput` is a wrapper around the standard `Input` component that:

- Toggles password visibility (`password` ↔ `text`)
- Preserves full compatibility with existing `Input` props
- Improves UX and security for authentication forms
- Supports form submission states (auto-hide on submit)

---

## Core Principles

- **Composable**: Inherits all props from `Input` (except `type`)
- **Accessible**: Uses proper `aria-*` attributes
- **Secure by default**: Password is hidden initially
- **Production-ready**: Supports advanced control via props

---

## How It Works

Internally, the component manages a local `show` state:

- `show = false` → input type is `password`
- `show = true` → input type is `text`

A toggle button (eye icon) updates this state without stealing focus from the input.
The input field always reserves space for the toggle icon to avoid layout shifting.

---

## Example (Register Form)

```tsx
<PasswordInput
  id="password"
  name="password"
  required
  placeholder="Password"
  autoComplete="new-password"
  autoHideWhen={processing}
/>

<PasswordInput
  name="password_confirmation"
  placeholder="Confirm password"
  autoComplete="new-password"
  autoHideWhen={processing}
/>
```

---

## Basic Usage

```tsx
import { PasswordInput } from '@/components/ui/password-input';

<PasswordInput
  name="password"
  placeholder="Password"
  autoComplete="current-password"
  required
/>
```

This renders:

- A password input
- A toggle icon to show/hide the password
- Secure default behavior (hidden on mount)

## Usage with Forms (Inertia / React)

Auto-hide on Submit (Recommended)
To prevent password exposure after form submission:

```tsx
<PasswordInput
  name="password"
  autoHideWhen={processing}
/>
```

When processing === true, the password will automatically be hidden.

## Props Reference

PasswordInput inherits all props from the base Input component, such as:

`name`, `id`, `placeholder`, `value`, `defaultValue`, `onChange`, `disabled`, `required`, `autoComplete`, `tabIndex`, `className`

>Note: The type prop is intentionally omitted and controlled internally.

## Custom Props

`buttonClassName?: string`
Custom class for styling the toggle button (eye icon).

```tsx
<PasswordInput buttonClassName="text-red-500" />
```

`defaultShow?: boolean`

Controls whether the password is visible on initial render.
Default: false

```tsx
<PasswordInput defaultShow />
```

`onToggle?: (show: boolean) => void`

Callback fired whenever the visibility state changes.

```tsx
<PasswordInput
  onToggle={(show) => {
    console.log('Password visible:', show);
  }}
/>
```

## Accessibility Considerations

- Uses aria-label and aria-pressed on the toggle button
- Toggle button does not interfere with keyboard tab order
- Input focus is preserved when clicking the toggle icon
