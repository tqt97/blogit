import { useEffect } from 'react';

type FocusableRef = React.RefObject<HTMLElement | null> | (() => void);

type ErrorBag = Record<string, unknown>;

type Options = {
    /** Ưu tiên focus theo thứ tự key này trước */
    order?: string[];
    /** Có auto focus không */
    enabled?: boolean;
    /** Debug */
    debug?: boolean;
};

export function useFormFocusOnError(
    params: {
        hasErrors: boolean;
        errors: ErrorBag | undefined;
        refMap: Record<string, FocusableRef>;
    },
    options: Options = {},
) {
    const { hasErrors, errors, refMap } = params;
    const { order = [], enabled = true, debug = false } = options;

    useEffect(() => {
        if (!enabled) return;
        if (!hasErrors) return;
        if (!errors) return;

        const keys = Object.keys(errors);

        // build priority list:
        // 1) options.order
        // 2) errors keys (server order not guaranteed)
        // 3) refMap keys
        const priority = [...order, ...keys, ...Object.keys(refMap)];

        const firstKey = priority.find((k) => k in errors && k in refMap);
        if (!firstKey) return;

        const refOrFn = refMap[firstKey];

        if (debug) console.log('[useFormFocusOnError] focus:', firstKey);

        if (typeof refOrFn === 'function') {
            refOrFn();
            return;
        }

        refOrFn.current?.focus?.();
    }, [enabled, hasErrors, errors, refMap, order, debug]);
}
