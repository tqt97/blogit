import AppLayout from '@/layouts/app-layout';
import TagForm from '@/pages/admin/tags/partials/form';
import { index, update } from '@/routes/tags';
import type { BreadcrumbItem, Tag } from '@/types';
import { Head } from '@inertiajs/react';

type Props = {
    tag: Tag;
};

export default function Edit({ tag }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tags', href: '/tags' },
        { title: `Edit #${tag.id}`, href: `/tags/${tag.id}/edit` },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Edit Tag`} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <TagForm
                    mode="edit"
                    title={`Edit tag`}
                    submitText="Update"
                    form={update.form(tag.id)}
                    cancelHref={index.url()}
                    defaultValues={{ name: tag.name, slug: tag.slug }}
                />
            </div>
        </AppLayout>
    );
}
