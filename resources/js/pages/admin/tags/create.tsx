import AppLayout from '@/layouts/app-layout';
import TagForm from '@/pages/admin/tags/partials/form';
import { index, store } from '@/routes/tags';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

export default function Create() {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tags', href: '/admin/tags' },
        { title: 'Create', href: '/admin/tags/create' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Tag" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <TagForm
                    mode="create"
                    title="Create tag"
                    submitText="Create"
                    form={store.form()}
                    cancelHref={index.url()}
                    resetOnSuccess={['name', 'slug']}
                />
            </div>
        </AppLayout>
    );
}
