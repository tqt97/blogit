import AppLayout from '@/layouts/app-layout';
import PostForm from '@/pages/admin/posts/partials/form';
import { index, store } from '@/routes/posts';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

export default function Create() {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Posts', href: '/admin/posts' },
        { title: 'Create', href: '/admin/posts/create' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Create Post" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <PostForm
                    mode="create"
                    title="Create post"
                    submitText="Create"
                    form={store.form()}
                    cancelHref={index.url()}
                    resetOnSuccess={['title', 'slug']}
                />
            </div>
        </AppLayout>
    );
}
