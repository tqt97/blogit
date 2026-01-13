import AppLayout from '@/layouts/app-layout';
import PostForm from '@/pages/admin/posts/partials/form';
import { index, update } from '@/routes/posts';
import type { BreadcrumbItem, Post } from '@/types';
import { Head } from '@inertiajs/react';

type Props = {
    post: Post;
};

export default function Edit({ post }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Posts', href: '/admin/posts' },
        { title: `Edit #${post.id}`, href: `/admin/posts/${post.id}/edit` },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit Post`} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <PostForm
                    mode="edit"
                    title={`Edit post`}
                    submitText="Update"
                    form={update.form(post.id)}
                    cancelHref={index.url()}
                    defaultValues={{ title: post.title, slug: post.slug }}
                />
            </div>
        </AppLayout>
    );
}
