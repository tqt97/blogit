# How to build `Tag` module

---

## For UI

### Add the new menu in the navigation

Edit `resources/js/components/app-header.tsx`

### Install Shadcn table component

```npx
npx shadcn@latest add table
```

### Notifications with Toast/Sonner

```npx
npx shadcn@latest add sonner
```

add to `app-sidebar-header.tsx`

```tsx
import { Toaster } from '@/components/ui/sonner';

...

<Toaster position={'top-right'} />
```

### Pagination Component from Shadcn

```npx
npx shadcn@latest add pagination
```
