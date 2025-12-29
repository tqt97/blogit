# Global Search / Command Palette – Architecture & Techniques

This document describes the architecture, interaction flow, and techniques used to implement a **Global Search / Command Palette** feature in a React + Inertia.js application.

The goal is to provide a **fast, keyboard-first search experience** similar to tools like:

- VS Code Command Palette
- Linear / Slack ⌘K
- Spotlight Search

---

## 1. Feature Overview

The Global Search provides:

- ⌘ / Ctrl + K (and ⌘ / Ctrl + P) to open/close search
- Click or focus input to open search
- Client-side (static) or Server-side (Inertia) search
- Keyboard navigation (Arrow Up / Down)
- Enter to select result
- Grouped results (Pages, Posts, Tags, etc.)
- Highlighted text matches
- Scroll active item into view
- Clear search input (✕)
- Accessible and focus-safe interactions

The system is designed to be:

- **Composable**
- **Keyboard-friendly**
- **Performance-aware**
- **Strict-TypeScript & ESLint compliant**

---

## 2. Component Structure

The implementation is split into **two separate components**:

### 2.1 Static Search (Client-side)

- Uses in-memory (fake/demo) data
- Filters results locally

### 2.2 Server Search (Inertia)

- Uses Inertia router.get
- Debounced query
- Partial reload via only: ['searchItems']
- Preserves UI state

Both components share the same interaction model and UX.

## 3. Keyboard Interaction Model

### 3.1 Open / Close

| Action        | Behavior      |
| ------------- | ------------- |
| ⌘ / Ctrl + K  | Toggle search |
| ⌘ / Ctrl + P  | Toggle search |
| Click input   | Open          |
| Focus input   | Open          |
| Click outside | Close         |
| Esc (global)  | Close         |

Handled via a custom hook:

```ts
useCommandSearch(ref, { keys: ['k', 'p'] })
```

## 4. Keyboard Navigation (List Control)

### 4.1 Arrow Navigation

ArrowDown: move to next item
ArrowUp: move to previous item
Navigation wraps around (cyclic)

```ts
setActiveIndex((i) => (i + 1) % total)
```

### 4.2 Enter Key Behavior

| State                         | Result                |
| ----------------------------- | --------------------- |
| Query empty                   | Navigate to Dashboard |
| Query non-empty + active item | Navigate active item  |
| Query non-empty + no active   | Navigate first item   |

This logic guarantees:
Predictable behavior
No “Enter does nothing” state

## 5. Highlighting Search Matches

Text highlighting is done purely at render time, without mutating data:

```ts
highlightMatch(text, query)
```

Technique:

- Case-insensitive match
- Split into before / match / after
- Render ```<span class="font-semibold">``` for match

This keeps:

- Data immutable
- UI logic isolated
- Performance predictable

### 6. Grouping Results

Results are grouped using a simple Map-based reducer:

```ts
groupBy(items)
```

Groups are rendered via:

```ts
<CommandGroup heading="Pages">
```

Benefits:

- Visual hierarchy
- Scales well for large datasets
- Matches real-world search behavior

## 7. Scroll Active Item Into View

To ensure good UX with long lists:

- Each item stores a DOM ref in a Map
- On activeIndex change:

  - scrollIntoView({ block: 'nearest' })

Important detail:

- Refs are never mutated during render
- All mutations happen via callback refs or effects

This avoids React compiler and ESLint violations.

## 8. Clear Input Button (✕)

A clear button appears when query !== ''.

Behavior:

- Clears query
- Resets active index
- Keeps dropdown open
- Focuses input again

This allows:

- Rapid refinement of search
- Zero mouse travel UX
Optional enhancement:
- Esc clears input first, then closes on second press

## 9. Static vs Server-side Search

### 9.1 Static (Client-side)

```ts
useMemo(() => {
  return DEMO_ITEMS.filter(...)
}, [query])
```

Pros:

- Instant response
- No network calls
- Great for demos

Cons:

- Not suitable for large datasets

### 9.2 Server-side (Inertia)

```ts
router.get('/admin/search', { q }, {
  preserveState: true,
  only: ['searchItems']
})
```

- Key techniques:
- Debounce query
- Partial reload
- Preserve scroll & UI state

Typed ```usePage<Props>() (no any)```
