import { Link } from '@inertiajs/react';
import { ColumnDef } from '@tanstack/react-table';
import { PencilIcon, TrashIcon } from 'lucide-react';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';

import { SortableColumnHeader } from '@/components/sortable-column-header';
import { formatIso } from '@/lib/parse-datetime';
import { edit } from '@/routes/posts';
import type { Post } from '@/types';

export function PostColumns(onDelete: (id: number) => void): ColumnDef<Post>[] {
    return [
        {
            id: 'select',
            minSize: 8,
            maxSize: 8,
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
            minSize: 12,
            maxSize: 12,
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="ID" />
            ),
            cell: ({ row }) => row.original.id,
        },
        {
            accessorKey: 'title',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="Title" />
            ),
            cell: ({ row }) => row.original.title,
        },
        {
            accessorKey: 'slug',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="Slug" />
            ),
            cell: ({ row }) => row.original.slug,
        },
        {
            accessorKey: 'viewCount',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="View" />
            ),
            cell: ({ row }) => row.original.viewCount,
        },
        {
            accessorKey: 'commentCount',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="View comment" />
            ),
            cell: ({ row }) => row.original.commentCount,
        },
        {
            accessorKey: 'likeCount',
            header: ({ column }) => (
                <SortableColumnHeader column={column} title="View like" />
            ),
            cell: ({ row }) => row.original.likeCount,
        },
        {
            id: 'createdAt',
            accessorFn: (row) => Date.parse(row.createdAt ?? '') || 0,

            header: ({ column }) => (
                <SortableColumnHeader column={column} title="Created At" />
            ),
            cell: ({ row }) => formatIso(row.original.createdAt, true),
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
                            className="text-blue-500 hover:cursor-pointer hover:bg-blue-500 hover:text-white dark:text-blue-400 dark:hover:bg-blue-500 dark:hover:text-white"
                        >
                            <Link href={edit.url(tag.id)}>
                                <PencilIcon className="size-4" />
                            </Link>
                        </Button>

                        <Button
                            variant="ghost"
                            size="icon"
                            className="text-destructive hover:cursor-pointer hover:bg-destructive hover:text-white dark:text-destructive dark:hover:bg-destructive dark:hover:text-white"
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
