import { Link } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { PencilIcon, TrashIcon } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';

import { SortableColumnHeader } from '@/components/sortable-column-header';
import { formatIso } from '@/lib/parse-datetime';
import { edit } from '@/routes/tags';
import type { Tag } from '@/types';

export function tagColumns(onDelete: (id: number) => void): ColumnDef<Tag>[] {
    return [
        {
            id: 'select',
            header: ({ table }) => (
                <Checkbox
                    className="mx-3 hover:cursor-pointer"
                    checked={
                        table.getIsAllPageRowsSelected() ||
                        (table.getIsSomePageRowsSelected() && 'indeterminate')
                    }
                    onCheckedChange={(v) =>
                        table.toggleAllPageRowsSelected(!!v)
                    }
                    aria-label="Select all"
                />
            ),
            cell: ({ row }) => (
                <Checkbox
                    className="hover:cursor-pointer"
                    checked={row.getIsSelected()}
                    onCheckedChange={(v) => row.toggleSelected(!!v)}
                    aria-label="Select row"
                />
            ),
            enableSorting: false,
            enableHiding: false,
        },
        {
            accessorKey: 'id',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="ID" />
            ),
            cell: ({ row }) => row.original.id,
        },
        {
            accessorKey: 'name',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="Name" />
            ),
            cell: ({ row }) => row.original.name,
        },
        {
            accessorKey: 'slug',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="Slug" />
            ),
            cell: ({ row }) => row.original.slug,
        },
        {
            id: 'created_at',
            accessorFn: (row) => Date.parse(row.created_at ?? '') || 0,

            header: ({ column }) => (
                <SortableColumnHeader column={column} title="Created At" />
            ),
            cell: ({ row }) => formatIso(row.original.created_at, true),
        },
        {
            id: 'actions',
            header: () => <div className="text-right" />,
            enableSorting: false,
            cell: ({ row }) => {
                const tag = row.original;

                return (
                    <div className="flex justify-end gap-1">
                        <Button
                            asChild
                            variant="ghost"
                            size="icon"
                            className="text-blue-500 hover:cursor-pointer hover:bg-blue-500 hover:text-white"
                        >
                            <Link href={edit.url(tag.id)}>
                                <PencilIcon className="size-4" />
                            </Link>
                        </Button>

                        <Button
                            variant="ghost"
                            size="icon"
                            className="text-destructive hover:cursor-pointer hover:bg-destructive hover:text-white"
                            onClick={() => onDelete(tag.id)}
                        >
                            <TrashIcon className="size-4" />
                        </Button>
                    </div>
                );
            },
        },
    ];
}
