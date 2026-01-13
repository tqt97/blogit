import AppLayout from '@/layouts/app-layout';
import { create, destroy, index } from '@/routes/tags';
import type { BreadcrumbItem, TagIndexProps } from '@/types';
import { Head, Link, router } from '@inertiajs/react';

import type { RowSelectionState, SortingState } from '@tanstack/react-table';
import { useCallback, useMemo, useRef, useState } from 'react';

import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';

import { ConfirmDialog } from '@/components/confirm-dialog';
import { DataTable } from '@/components/data-table';
import { TablePaginationLinks } from '@/components/table-paginate-simple';

import { useInertiaLoading } from '@/hooks/use-inertia-loading';
import { bulkDestroy } from '@/routes/tags';
import { ChevronDownIcon, PlusIcon, SearchIcon, XIcon } from 'lucide-react';
import { tagColumns } from '../partials/columns';

function sortingToQuery(s: SortingState): {
    sort?: string;
    direction?: 'asc' | 'desc';
} {
    const first = s[0];
    if (!first) return {};
    return { sort: first.id, direction: first.desc ? 'desc' : 'asc' };
}

export default function Index2({ tags, filters }: TagIndexProps) {
    const breadcrumbs: BreadcrumbItem[] = [{ title: 'Tags', href: 'tags' }];

    const query = useMemo(
        () => ({
            search: filters.search ?? '',
            sort: filters.sort ?? 'id',
            direction: (filters.direction ?? 'desc') as 'asc' | 'desc',
            per_page: Number(filters.per_page ?? 15),
            page: Number(filters.page ?? 1),
        }),
        [filters],
    );

    const [bulkMenuOpen, setBulkMenuOpen] = useState(false);
    const [bulkDeleteOpen, setBulkDeleteOpen] = useState(false);
    const [deleteId, setDeleteId] = useState<number | null>(null);
    const [rowSelection, setRowSelection] = useState<RowSelectionState>({});

    const selectedIds = useMemo(() => {
        return Object.entries(rowSelection)
            .filter(([, v]) => v)
            .map(([k]) => Number(k))
            .filter(Number.isFinite);
    }, [rowSelection]);

    const hasSelection = selectedIds.length > 0;

    const clearSelection = useCallback(() => {
        setRowSelection({});
        setBulkMenuOpen(false);
    }, []);

    const apply = useCallback(
        (next: Partial<typeof query>) => {
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
                index.url({
                    query: {
                        ...merged,
                        page: next.page ?? 1,
                    },
                }),
                {},
                { preserveState: true, preserveScroll: true, replace: true },
            );
        },
        [query, clearSelection],
    );

    const [searchInput, setSearchInput] = useState(query.search);
    const searchTimerRef = useRef<number | null>(null);

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

            if (searchTimerRef.current) {
                window.clearTimeout(searchTimerRef.current);
            }

            searchTimerRef.current = window.setTimeout(() => {
                flushSearch(value);
            }, 350);
        },
        [flushSearch],
    );

    const onSearchKeyDown = useCallback(
        (e: React.KeyboardEvent<HTMLInputElement>) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (searchTimerRef.current)
                    window.clearTimeout(searchTimerRef.current);
                flushSearch(searchInput);
            }

            if (e.key === 'Escape') {
                e.preventDefault();
                if (searchTimerRef.current)
                    window.clearTimeout(searchTimerRef.current);
                setSearchInput('');
                if (query.search !== '') apply({ search: '' });
            }
        },
        [apply, flushSearch, query.search, searchInput],
    );

    const searchInputKey = query.search;

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

    const onDelete = useCallback((id: number) => setDeleteId(id), []);
    const columns = useMemo(() => tagColumns(onDelete), [onDelete]);

    const confirmDelete = useCallback(() => {
        if (!deleteId) return;
        router.delete(destroy.url(deleteId), {
            preserveScroll: true,
            onFinish: () => setDeleteId(null),
        });
    }, [deleteId]);

    const confirmBulkDelete = useCallback(() => {
        if (!hasSelection) return;

        router.delete(bulkDestroy.url(), {
            data: { ids: selectedIds },
            preserveScroll: true,
            onFinish: () => {
                setBulkDeleteOpen(false);
                clearSelection();
            },
        });
    }, [clearSelection, hasSelection, selectedIds]);
    const isLoading = useInertiaLoading();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tags" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold">Tags</h1>
                    <Button asChild>
                        <Link
                            href={create.url()}
                            className="flex items-center gap-1"
                        >
                            <PlusIcon className="size-4" />
                            Create
                        </Link>
                    </Button>
                </div>

                <div className="rounded border bg-card p-4">
                    <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div className="flex items-center justify-end gap-2">
                            <DropdownMenu
                                open={bulkMenuOpen}
                                onOpenChange={setBulkMenuOpen}
                            >
                                <DropdownMenuTrigger asChild>
                                    <Button
                                        variant="secondary"
                                        disabled={!hasSelection}
                                        className="gap-2 hover:cursor-pointer"
                                    >
                                        Bulk actions
                                        <ChevronDownIcon className="size-4 opacity-70" />
                                    </Button>
                                </DropdownMenuTrigger>

                                <DropdownMenuContent align="end">
                                    <DropdownMenuItem
                                        className="text-destructive hover:cursor-pointer focus:text-destructive"
                                        onClick={() => {
                                            setBulkMenuOpen(false);
                                            setBulkDeleteOpen(true);
                                        }}
                                    >
                                        Delete selected ({selectedIds.length})
                                    </DropdownMenuItem>
                                </DropdownMenuContent>
                            </DropdownMenu>

                            {hasSelection ? (
                                <Button
                                    variant="secondary"
                                    className="text-destructive hover:cursor-pointer hover:bg-destructive hover:text-white"
                                    onClick={clearSelection}
                                >
                                    Clear ({selectedIds.length})
                                </Button>
                            ) : null}
                        </div>

                        <div className="relative flex-1 md:max-w-md">
                            <SearchIcon className="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />

                            <Input
                                key={searchInputKey}
                                defaultValue={query.search}
                                placeholder="Search tags..."
                                className="pr-9 pl-9"
                                onChange={(e) => onSearchChange(e.target.value)}
                                onKeyDown={onSearchKeyDown}
                            />

                            {(query.search || searchInput) && (
                                <button
                                    type="button"
                                    aria-label="Clear search"
                                    className="absolute top-1/2 right-0 -translate-y-1/2 rounded p-2 text-muted-foreground hover:cursor-pointer hover:bg-muted"
                                    onClick={() => {
                                        if (searchTimerRef.current)
                                            window.clearTimeout(
                                                searchTimerRef.current,
                                            );

                                        setSearchInput('');
                                        if (query.search !== '') {
                                            apply({ search: '' });
                                        }
                                    }}
                                >
                                    <XIcon className="h-4 w-4" />
                                </button>
                            )}
                        </div>
                    </div>
                </div>

                <DataTable
                    data={tags.data}
                    columns={columns}
                    state={{ sorting, rowSelection }}
                    onSortingChange={onSortingChange}
                    onRowSelectionChange={setRowSelection}
                    emptyText="No tags found."
                    getRowId={(row) => String(row.id)}
                    loading={isLoading}
                />

                {tags.links.length > 0 && (
                    <TablePaginationLinks links={tags.links} />
                )}

                <ConfirmDialog
                    open={deleteId !== null}
                    onOpenChange={(open) => !open && setDeleteId(null)}
                    title="Delete Tag?"
                    description="This action cannot be undone."
                    confirmText="Delete"
                    onConfirm={confirmDelete}
                    variant="destructive"
                />

                <ConfirmDialog
                    open={bulkDeleteOpen}
                    onOpenChange={setBulkDeleteOpen}
                    title={`Delete ${selectedIds.length} tag(s)?`}
                    description="This action cannot be undone."
                    confirmText="Delete"
                    onConfirm={confirmBulkDelete}
                    variant="destructive"
                />
            </div>
        </AppLayout>
    );
}
