import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import type { ListPageShellProps } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { PlusIcon } from 'lucide-react';

export function ListPageShell({
    title,
    headTitle,
    breadcrumbs = [],
    createHref,
    createText = 'Create',
    createIcon,
    toolbar,
    table,
    pagination,
    dialogs,
}: ListPageShellProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={headTitle ?? title} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-xl font-semibold">{title}</h1>

                    {createHref ? (
                        <Button asChild>
                            <Link
                                href={createHref}
                                className="flex items-center gap-1"
                            >
                                {createIcon ?? <PlusIcon className="size-4" />}
                                {createText}
                            </Link>
                        </Button>
                    ) : null}
                </div>

                {toolbar ? (
                    <div className="rounded border bg-card p-4">{toolbar}</div>
                ) : null}

                {table}

                {pagination}

                {dialogs}
            </div>
        </AppLayout>
    );
}
