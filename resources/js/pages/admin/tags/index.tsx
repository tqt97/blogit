import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

import AppLayout from '@/layouts/app-layout';
import { create, destroy, edit, index } from '@/routes/tags';
import type { BreadcrumbItem, Paginated, Tag, TagIndexFilters } from '@/types';
import { Head, Link, router } from '@inertiajs/react';

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { PencilIcon, TrashIcon } from 'lucide-react';
import { useState } from 'react';

type Props = { tags: Paginated<Tag>; filters: TagIndexFilters };

export default function Index({ tags, filters }: Props) {
    const q = {
        search: filters.search ?? '',
        per_page: Number(filters.per_page ?? 15),
        sort: filters.sort ?? 'id',
        direction: (filters.direction ?? 'desc') as 'asc' | 'desc',
    };

    const apply = (next: Partial<typeof q>) => {
        router.get(
            index.url({
                query: { ...q, ...next },
            }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };

    const [deleteId, setDeleteId] = useState<number | null>(null);

    const confirmDelete = () => {
        if (!deleteId) return;
        router.delete(destroy.url(deleteId), {
            preserveScroll: true,
        });
        setDeleteId(null);
    };

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Tags',
            href: 'tags',
        },
    ];

    const per_pages = ['10', '15', '25', '50', '100'];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Tags" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="mb-4 flex items-center justify-between">
                    <h1 className="text-xl font-semibold">Tags</h1>
                    <Button asChild>
                        <Link href={create.url()}>Create</Link>
                    </Button>
                </div>

                <div className="mb-4 rounded border bg-card p-4">
                    <div className="grid gap-4 md:grid-cols-4">
                        <div className="md:col-span-2">
                            <Input
                                defaultValue={q.search}
                                placeholder="Search tags..."
                                onKeyDown={(e) => {
                                    if (e.key === 'Enter')
                                        apply({
                                            search: (
                                                e.target as HTMLInputElement
                                            ).value,
                                        });
                                }}
                                onBlur={(e) =>
                                    apply({ search: e.target.value })
                                }
                            />
                        </div>

                        <div>
                            <Select
                                value={q.per_page.toString()}
                                onValueChange={(e) =>
                                    apply({ per_page: Number(e) })
                                }
                            >
                                <SelectTrigger className="">
                                    <SelectValue placeholder="Per page" />
                                </SelectTrigger>

                                <SelectContent>
                                    {per_pages.map((page) => (
                                        <SelectItem key={page} value={page}>
                                            {page} / page
                                        </SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <div className="grid grid-cols-2 gap-2">
                                <Select
                                    defaultValue={q.sort ?? 'id'}
                                    onValueChange={(value) =>
                                        apply({ sort: value })
                                    }
                                >
                                    <SelectTrigger className="w-[140px]1 h-9">
                                        <SelectValue placeholder="Sort by" />
                                    </SelectTrigger>

                                    <SelectContent>
                                        <SelectItem value="id">ID</SelectItem>
                                        <SelectItem value="name">
                                            Name
                                        </SelectItem>
                                        <SelectItem value="slug">
                                            Slug
                                        </SelectItem>
                                        <SelectItem value="created_at">
                                            Created
                                        </SelectItem>
                                    </SelectContent>
                                </Select>

                                <Select
                                    defaultValue={q.direction ?? 'desc'}
                                    onValueChange={(value) =>
                                        apply({
                                            direction: value as 'asc' | 'desc',
                                        })
                                    }
                                >
                                    <SelectTrigger className="w-[110px]1 h-9">
                                        <SelectValue placeholder="Direction" />
                                    </SelectTrigger>

                                    <SelectContent>
                                        <SelectItem value="desc">
                                            Desc
                                        </SelectItem>
                                        <SelectItem value="asc">Asc</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="overflow-hidden rounded border bg-card">
                    <Table className="w-full border-collapse rounded-lg">
                        <TableHeader className="bg-gray-900 text-gray-50">
                            <TableRow>
                                <TableHead className="w-[100px] px-4 py-3 text-left text-sm font-semibold text-gray-50">
                                    ID
                                </TableHead>
                                <TableHead className="px-4 py-3 text-left text-sm font-semibold text-gray-50">
                                    Name
                                </TableHead>
                                <TableHead className="px-4 py-3 text-left text-sm font-semibold text-gray-50">
                                    Slug
                                </TableHead>
                                <TableHead className="px-4 py-3 text-right text-sm font-semibold text-gray-50">
                                    Actions
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {tags.data.length === 0 ? (
                                <TableRow>
                                    <TableCell
                                        colSpan={4}
                                        className="px-4 py-10 text-center text-sm text-muted-foreground"
                                    >
                                        No tags found.
                                    </TableCell>
                                </TableRow>
                            ) : (
                                tags.data.map((tag: Tag) => (
                                    <TableRow key={tag.id} className="border-t">
                                        <TableCell className="px-4 py-3 text-sm">
                                            {tag.id}
                                        </TableCell>
                                        <TableCell className="px-4 py-3 text-sm">
                                            {tag.name}
                                        </TableCell>
                                        <TableCell className="px-4 py-3 text-sm">
                                            {tag.slug}
                                        </TableCell>
                                        <TableCell className="px-4 py-3 text-right text-sm">
                                            <div className="inline-flex items-center gap-2">
                                                <Button
                                                    asChild
                                                    variant="secondary"
                                                    size="sm"
                                                >
                                                    <Link
                                                        href={edit.url(tag.id)}
                                                    >
                                                        <PencilIcon className="text-blue-500 hover:cursor-pointer hover:text-blue-700" />
                                                    </Link>
                                                </Button>
                                                <Button
                                                    className="hover:cursor-pointer"
                                                    variant="destructive"
                                                    size="sm"
                                                    onClick={() =>
                                                        setDeleteId(tag.id)
                                                    }
                                                >
                                                    <TrashIcon className="hover:cursor-pointer" />
                                                </Button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))
                            )}
                        </TableBody>
                    </Table>
                </div>
                {tags.links?.length > 0 && (
                    <div className="flex items-center justify-end">
                        <div className="mt-4 flex flex-wrap gap-1">
                            {tags.links.map((link, i) => (
                                <Button
                                    className="hover:cursor-pointer hover:bg-gray-900 hover:text-gray-50"
                                    key={i}
                                    variant={
                                        link.active ? 'default' : 'secondary'
                                    }
                                    size="sm"
                                    disabled={!link.url}
                                    onClick={() =>
                                        link.url &&
                                        router.get(
                                            link.url,
                                            {},
                                            {
                                                preserveState: true,
                                                preserveScroll: true,
                                            },
                                        )
                                    }
                                >
                                    <span
                                        dangerouslySetInnerHTML={{
                                            __html: link.label,
                                        }}
                                    />
                                </Button>
                            ))}
                        </div>
                    </div>
                )}

                <AlertDialog
                    open={deleteId !== null}
                    onOpenChange={(open) => !open && setDeleteId(null)}
                >
                    <AlertDialogContent>
                        <AlertDialogHeader>
                            <AlertDialogTitle>Delete tag?</AlertDialogTitle>
                            <AlertDialogDescription>
                                This action cannot be undone.
                            </AlertDialogDescription>
                        </AlertDialogHeader>
                        <AlertDialogFooter>
                            <AlertDialogCancel className="hover:cursor-pointer">
                                Cancel
                            </AlertDialogCancel>
                            <AlertDialogAction
                                className="hover:cursor-pointer"
                                onClick={confirmDelete}
                            >
                                Delete
                            </AlertDialogAction>
                        </AlertDialogFooter>
                    </AlertDialogContent>
                </AlertDialog>
            </div>
        </AppLayout>
    );
}
