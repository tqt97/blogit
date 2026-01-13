import { ConfirmDialog } from '@/components/confirm-dialog';
import { DataTable } from '@/components/data-table';
import { ListPageShell } from '@/components/list-page-shell';
import { ListToolbar } from '@/components/list-toolbar';
import { TablePaginationLinks } from '@/components/table-paginate-simple';
import { useInertiaLoading } from '@/hooks/use-inertia-loading';
import { useListPage } from '@/hooks/use-list-page';

import { bulkDestroy, create, destroy, index } from '@/routes/posts';
import type { BreadcrumbItem, PostIndexProps } from '@/types';
import { router } from '@inertiajs/react';
import { useCallback, useMemo, useState } from 'react';
import { PostColumns } from './partials/columns';

export default function Index({ posts, filters }: PostIndexProps) {
    const breadcrumbs: BreadcrumbItem[] = [{ title: 'Posts', href: '/admin/posts' }];
    console.log(posts)

    const list = useListPage({
        filters,
        indexUrl: index.url,
        defaults: { sort: 'id', direction: 'desc', per_page: 15, page: 1 },
    });

    const [bulkMenuOpen, setBulkMenuOpen] = useState(false);
    const [bulkDeleteOpen, setBulkDeleteOpen] = useState(false);
    const [deleteId, setDeleteId] = useState<number | null>(null);

    const onDelete = useCallback((id: number) => setDeleteId(id), []);
    const columns = useMemo(() => PostColumns(onDelete), [onDelete]);

    const confirmDelete = useCallback(() => {
        if (!deleteId) return;
        router.delete(destroy.url(deleteId), {
            preserveScroll: true,
            onFinish: () => setDeleteId(null),
        });
    }, [deleteId]);

    const confirmBulkDelete = useCallback(() => {
        if (!list.hasSelection) return;

        router.delete(bulkDestroy.url(), {
            data: { ids: list.selectedIds },
            preserveScroll: true,
            onFinish: () => {
                setBulkDeleteOpen(false);
                list.clearSelection();
                setBulkMenuOpen(false);
            },
        });
    }, [list]);

    const isLoading = useInertiaLoading();

    return (
        <ListPageShell
            title="Posts"
            breadcrumbs={breadcrumbs}
            createHref={create.url()}
            toolbar={
                <ListToolbar
                    bulkMenuOpen={bulkMenuOpen}
                    setBulkMenuOpen={setBulkMenuOpen}
                    hasSelection={list.hasSelection}
                    selectedCount={list.selectedIds.length}
                    onClearSelection={() => {
                        list.clearSelection();
                        setBulkMenuOpen(false);
                    }}
                    onBulkDeleteClick={() => {
                        setBulkMenuOpen(false);
                        setBulkDeleteOpen(true);
                    }}
                    searchKey={list.search.key}
                    defaultSearch={list.query.search}
                    searchInput={list.search.searchInput}
                    onSearchChange={list.search.onSearchChange}
                    onSearchKeyDown={list.search.onSearchKeyDown}
                    onClearSearch={() => {
                        list.search.setSearchInput('');
                        if (list.query.search !== '')
                            list.apply({ search: '' });
                    }}
                    perPage={list.query.per_page}
                    onPerPageChange={(n) =>
                        list.apply({ per_page: n, page: 1 })
                    }
                    placeholder="Search posts..."
                    loading={isLoading}
                />
            }
            table={
                <DataTable
                    data={posts.data}
                    columns={columns}
                    state={{
                        sorting: list.sorting,
                        rowSelection: list.rowSelection,
                    }}
                    onSortingChange={list.onSortingChange}
                    onRowSelectionChange={list.setRowSelection}
                    emptyText="No posts found."
                    getRowId={(row) => String(row.id)}
                    loading={isLoading}
                />
            }
            pagination={
                posts.links.length > 0 ? (
                    <TablePaginationLinks links={posts.links} />
                ) : null
            }
            dialogs={
                <>
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
                        title={`Delete ${list.selectedIds.length} tag(s)?`}
                        description="This action cannot be undone."
                        confirmText="Delete"
                        onConfirm={confirmBulkDelete}
                        variant="destructive"
                    />
                </>
            }
        />
    );
}
