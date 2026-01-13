import { slugify } from '@/lib/slug';
import * as React from 'react';

type UseAutoSlugOptions = {
    initialSlug?: string;
    delay?: number;
};

export function useAutoSlug(name: string, options: UseAutoSlugOptions = {}) {
    const { initialSlug = '', delay = 150 } = options;

    const [slug, setSlug] = React.useState<string>(initialSlug);
    const [touched, setTouched] = React.useState(false);

    // Auto-generate slug when name changes (if not touched)
    React.useEffect(() => {
        if (touched) return;

        const t = window.setTimeout(() => {
            setSlug(name ? slugify(name) : '');
        }, delay);

        return () => window.clearTimeout(t);
    }, [name, touched, delay]);

    // Sync when initialSlug changes (edit / inertia reload)
    React.useEffect(() => {
        setSlug(initialSlug);
        setTouched(false);
    }, [initialSlug]);

    const resetToAuto = React.useCallback(() => {
        setTouched(false);
        setSlug(name ? slugify(name) : '');
    }, [name]);

    return {
        slug,
        setSlug,
        touched,
        setTouched,
        resetToAuto,
    };
}
