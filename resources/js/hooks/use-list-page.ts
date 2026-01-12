import { ListFilters } from '@/types';
import { router } from '@inertiajs/react';
import type { RowSelectionState, SortingState } from '@tanstack/react-table';
import { useCallback, useMemo, useRef, useState } from 'react';

type Direction = 'asc' | 'desc';

export type ListQuery = {
    search: string;
    sort: string;
    direction: Direction;
    per_page: number;
    page: number;
};

export function sortingToQuery(s: SortingState): {
    sort?: string;
    direction?: Direction;
} {
    const first = s[0];
    if (!first) return {};
    return { sort: first.id, direction: first.desc ? 'desc' : 'asc' };
}

export function useListPage<TFilters extends ListFilters>(params: {
    filters: TFilters;
    indexUrl: (options?: { query?: ListFilters }) => string;
    defaults?: Partial<ListQuery>;
    preserveState?: boolean;
    debounceMs?: number;
}) {
    const {
        filters,
        indexUrl,
        defaults,
        preserveState = true,
        debounceMs = 350,
    } = params;

    const query = useMemo<ListQuery>(
        () => ({
            search: filters.search ?? defaults?.search ?? '',
            sort: filters.sort ?? defaults?.sort ?? 'id',
            direction: (filters.direction ??
                defaults?.direction ??
                'desc') as Direction,
            per_page: Number(filters.per_page ?? defaults?.per_page ?? 15),
            page: Number(filters.page ?? defaults?.page ?? 1),
        }),
        [filters, defaults],
    );

    const [rowSelection, setRowSelection] = useState<RowSelectionState>({});
    const selectedIds = useMemo(
        () =>
            Object.entries(rowSelection)
                .filter(([, v]) => v)
                .map(([k]) => Number(k))
                .filter(Number.isFinite),
        [rowSelection],
    );

    const hasSelection = selectedIds.length > 0;

    const clearSelection = useCallback(() => setRowSelection({}), []);

    const apply = useCallback(
        (next: Partial<ListQuery>) => {
            // change filter/sort/page/per_page/search => clear selection
            if (
                next.search !== undefined ||
                next.sort !== undefined ||
                next.direction !== undefined ||
                next.per_page !== undefined ||
                next.page !== undefined
            ) {
                clearSelection();
            }

            const merged = { ...query, ...next };
            router.get(
                indexUrl({ query: { ...merged, page: next.page ?? 1 } }),
                {},
                { preserveState, preserveScroll: true, replace: true },
            );
        },
        [clearSelection, indexUrl, preserveState, query],
    );

    // search (debounce + enter/esc)
    const [searchInput, setSearchInput] = useState(query.search);
    const timerRef = useRef<number | null>(null);

    const flushSearch = useCallback(
        (value: string) => {
            if (value === query.search) return;
            apply({ search: value });
        },
        [apply, query.search],
    );

    const onSearchChange = useCallback(
        (value: string) => {
            setSearchInput(value);
            if (timerRef.current) window.clearTimeout(timerRef.current);
            timerRef.current = window.setTimeout(
                () => flushSearch(value),
                debounceMs,
            );
        },
        [debounceMs, flushSearch],
    );

    const onSearchKeyDown = useCallback(
        (e: React.KeyboardEvent<HTMLInputElement>) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (timerRef.current) window.clearTimeout(timerRef.current);
                flushSearch(searchInput);
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                if (timerRef.current) window.clearTimeout(timerRef.current);
                setSearchInput('');
                if (query.search !== '') apply({ search: '' });
            }
        },
        [apply, flushSearch, query.search, searchInput],
    );

    const sorting = useMemo<SortingState>(
        () => [{ id: query.sort, desc: query.direction === 'desc' }],
        [query.sort, query.direction],
    );

    const onSortingChange = useCallback(
        (updater: SortingState | ((old: SortingState) => SortingState)) => {
            const next =
                typeof updater === 'function' ? updater(sorting) : updater;
            const { sort, direction } = sortingToQuery(next);
            if (!sort || !direction) return;
            if (sort === query.sort && direction === query.direction) return;
            apply({ sort, direction });
        },
        [apply, query.direction, query.sort, sorting],
    );

    return {
        query,
        apply,

        // search helpers
        search: {
            searchInput,
            setSearchInput,
            onSearchChange,
            onSearchKeyDown,
            clear: () => apply({ search: '' }),
            key: query.search,
        },

        // sorting helpers
        sorting,
        onSortingChange,

        // selection helpers
        rowSelection,
        setRowSelection,
        selectedIds,
        hasSelection,
        clearSelection,
    };
}
