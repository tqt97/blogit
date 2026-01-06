import { DataTableProps } from '@/types';
import {
    flexRender,
    getCoreRowModel,
    getSortedRowModel,
    useReactTable,
} from '@tanstack/react-table';
import { Loader } from 'lucide-react';
import { useEffect } from 'react';

export function DataTable<TData>({
    loading = false,
    loadingText = 'Loading...',
    data,
    columns,
    emptyText = 'No data.',
    manualSorting = true,
    state,
    onSortingChange,
    onRowSelectionChange,
    onTableReady,
    getRowId,
}: DataTableProps<TData>) {
    // eslint-disable-next-line react-hooks/incompatible-library
    const table = useReactTable({
        data,
        columns,
        state: {
            sorting: state.sorting ?? [],
            rowSelection: state.rowSelection ?? {},
        },
        enableRowSelection: true,
        onSortingChange,
        onRowSelectionChange,
        getCoreRowModel: getCoreRowModel(),
        ...(manualSorting ? {} : { getSortedRowModel: getSortedRowModel() }),
        manualSorting,
        getRowId,
    });

    useEffect(() => {
        onTableReady?.(table);
    }, [table, onTableReady]);

    const rows = table.getRowModel().rows;

    return (
        <div className="relative rounded-md border">
            <table className="w-full">
                <thead
                    className={`rounded-t-md ${loading ? 'opacity-50' : ''}`}
                >
                    {table.getHeaderGroups().map((hg) => (
                        <tr key={hg.id} className="rounded">
                            {hg.headers.map((header) => (
                                <th
                                    key={header.id}
                                    style={{ width: header.getSize() }}
                                    className="py-2 text-left text-gray-950"
                                >
                                    {header.isPlaceholder
                                        ? null
                                        : flexRender(
                                              header.column.columnDef.header,
                                              header.getContext(),
                                          )}
                                </th>
                            ))}
                        </tr>
                    ))}
                </thead>

                <tbody className="relative">
                    {rows.length ? (
                        rows.map((row) => (
                            <tr
                                key={row.id}
                                className={loading ? 'opacity-10' : 'border-t'}
                            >
                                {row.getVisibleCells().map((cell) => (
                                    <td
                                        key={cell.id}
                                        style={{ width: cell.column.getSize() }}
                                        className="px-3 py-3 text-sm"
                                    >
                                        {flexRender(
                                            cell.column.columnDef.cell,
                                            cell.getContext(),
                                        )}
                                    </td>
                                ))}
                            </tr>
                        ))
                    ) : (
                        <tr>
                            <td
                                className={`p-4 text-center text-muted-foreground ${loading ? 'opacity-10' : ''}`}
                                colSpan={columns.length}
                            >
                                {emptyText}
                            </td>
                        </tr>
                    )}

                    {loading && (
                        <tr>
                            <td colSpan={columns.length} className="p-0">
                                <div className="pointer-events-none absolute inset-0 flex items-center justify-center">
                                    <div className="flex items-center gap-2 text-muted-foreground">
                                        <Loader className="h-4 w-4 animate-spin" />
                                        <span className="text-sm">
                                            {loadingText}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>
        </div>
    );
}
