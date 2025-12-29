// import { useEffect, useState } from 'react';

// type Options = {
//     allowInInputs?: boolean;
//     preventDefault?: boolean;
//     selectOnFocus?: boolean;
// };

// function isTypingTarget(target: EventTarget | null) {
//     if (!(target instanceof HTMLElement)) return false;
//     const tag = target.tagName.toLowerCase();
//     return tag === 'input' || tag === 'textarea' || tag === 'select' || target.isContentEditable;
// }

// export function useCommandSearch(
//     ref: React.RefObject<HTMLInputElement | null>,
//     options: Options = {}
// ) {
//     const { allowInInputs = false, preventDefault = true, selectOnFocus = true } = options;

//     const [open, setOpen] = useState(false);

//     const openDropdown = () => setOpen(true);
//     const closeDropdown = () => setOpen(false);

//     useEffect(() => {
//         const onKeyDown = (e: KeyboardEvent) => {
//             const el = ref.current;
//             if (!el) return;

//             const isFocused = document.activeElement === el;

//             // ESC closes + blur
//             if (e.key === 'Escape') {
//                 if (open) {
//                     if (preventDefault) e.preventDefault();
//                     setOpen(false);
//                 }
//                 if (isFocused) el.blur();
//                 return;
//             }

//             // Ctrl/⌘ + K toggle
//             const isK = (e.key || '').toLowerCase() === 'k';
//             const hasModifier = e.ctrlKey || e.metaKey;
//             if (!isK || !hasModifier) return;

//             if (!allowInInputs && isTypingTarget(e.target) && e.target !== el) return;
//             if (preventDefault) e.preventDefault();

//             if (isFocused && open) {
//                 setOpen(false);
//                 el.blur();
//                 return;
//             }

//             el.focus();
//             if (selectOnFocus) el.select?.();
//             setOpen(true);
//         };

//         window.addEventListener('keydown', onKeyDown);
//         return () => window.removeEventListener('keydown', onKeyDown);
//     }, [ref, open, allowInInputs, preventDefault, selectOnFocus]);

//     return { open, setOpen, openDropdown, closeDropdown };
// }

import { useEffect, useState } from 'react';

type Options = {
    allowInInputs?: boolean;
    preventDefault?: boolean;
    selectOnFocus?: boolean;
    /** Which keys open/toggle the palette with Ctrl/⌘ (default: ['k']) */
    keys?: string[];
};

function isTypingTarget(target: EventTarget | null) {
    if (!(target instanceof HTMLElement)) return false;
    const tag = target.tagName.toLowerCase();
    return (
        tag === 'input' ||
        tag === 'textarea' ||
        tag === 'select' ||
        target.isContentEditable
    );
}

export function useCommandSearch(
    ref: React.RefObject<HTMLInputElement | null>,
    options: Options = {},
) {
    const {
        allowInInputs = false,
        preventDefault = true,
        selectOnFocus = true,
        keys = ['k', 'p'], // support Cmd/Ctrl+K and Cmd/Ctrl+P by default
    } = options;

    const [open, setOpen] = useState(false);

    const openDropdown = () => setOpen(true);
    const closeDropdown = () => setOpen(false);

    useEffect(() => {
        const onKeyDown = (e: KeyboardEvent) => {
            const el = ref.current;
            if (!el) return;

            const isFocused = document.activeElement === el;

            // ESC closes + blur
            if (e.key === 'Escape') {
                if (open) {
                    if (preventDefault) e.preventDefault();
                    setOpen(false);
                }
                if (isFocused) el.blur();
                return;
            }

            // Ctrl/⌘ + (K/P/...) toggle
            const pressed = (e.key || '').toLowerCase();
            const hasModifier = e.ctrlKey || e.metaKey;
            const matches = hasModifier && keys.includes(pressed);
            if (!matches) return;

            if (!allowInInputs && isTypingTarget(e.target) && e.target !== el)
                return;
            if (preventDefault) e.preventDefault();

            if (isFocused && open) {
                setOpen(false);
                el.blur();
                return;
            }

            el.focus();
            if (selectOnFocus) el.select?.();
            setOpen(true);
        };

        window.addEventListener('keydown', onKeyDown);
        return () => window.removeEventListener('keydown', onKeyDown);
    }, [ref, open, allowInInputs, preventDefault, selectOnFocus, keys]);

    return { open, setOpen, openDropdown, closeDropdown };
}
