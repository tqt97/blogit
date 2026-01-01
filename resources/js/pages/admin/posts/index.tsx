import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: dashboard().url,
    },
];

type Option = { id: number; label: string };

export default function Index({
    posts,
    filters,
    categories,
    tags,
}: {
    posts: any; // Inertia paginator object
    filters: any;
    categories: Option[];
    tags: Option[];
}) {
    const apply = (next: any) => {
        router.get(route('posts.index'), { ...filters, ...next }, { preserveState: true, replace: true });
    };
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="max-w-6xl mx-auto p-6 space-y-4">
                    <div className="flex items-center justify-between">
                        <h1 className="text-xl font-semibold">Posts</h1>
                        <a className="border px-3 py-2" href={route('posts.create')}>Create</a>
                    </div>

                    {/* Filters */}
                    <div className="border p-4 space-y-3">
                        <div className="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <input
                                className="border p-2 md:col-span-2"
                                placeholder="Search title/slug..."
                                defaultValue={filters.q ?? ''}
                                onKeyDown={(e) => {
                                    if (e.key === 'Enter') apply({ q: (e.target as HTMLInputElement).value, page: 1 });
                                }}
                            />

                            <select
                                className="border p-2"
                                value={filters.status ?? ''}
                                onChange={(e) => apply({ status: e.target.value || null, page: 1 })}
                            >
                                <option value="">All status</option>
                                <option value="draft">draft</option>
                                <option value="pending">pending</option>
                                <option value="published">published</option>
                            </select>

                            <select
                                className="border p-2"
                                value={filters.category_id ?? ''}
                                onChange={(e) => apply({ category_id: e.target.value || null, page: 1 })}
                            >
                                <option value="">All categories</option>
                                {categories.map(c => <option key={c.id} value={c.id}>{c.label}</option>)}
                            </select>

                            <select
                                className="border p-2"
                                value={filters.tag_id ?? ''}
                                onChange={(e) => apply({ tag_id: e.target.value || null, page: 1 })}
                            >
                                <option value="">All tags</option>
                                {tags.map(t => <option key={t.id} value={t.id}>{t.label}</option>)}
                            </select>
                        </div>

                        <div className="flex items-center gap-3">
                            <select
                                className="border p-2"
                                value={filters.sort ?? 'published_at'}
                                onChange={(e) => apply({ sort: e.target.value, page: 1 })}
                            >
                                <option value="published_at">published_at</option>
                                <option value="created_at">created_at</option>
                                <option value="title">title</option>
                            </select>

                            <select
                                className="border p-2"
                                value={filters.direction ?? 'desc'}
                                onChange={(e) => apply({ direction: e.target.value, page: 1 })}
                            >
                                <option value="desc">desc</option>
                                <option value="asc">asc</option>
                            </select>

                            <select
                                className="border p-2"
                                value={filters.per_page ?? 15}
                                onChange={(e) => apply({ per_page: e.target.value, page: 1 })}
                            >
                                {[10, 15, 25, 50, 100].map(n => <option key={n} value={n}>{n} / page</option>)}
                            </select>

                            <button
                                type="button"
                                className="border px-3 py-2"
                                onClick={() => apply({ q: null, status: null, category_id: null, tag_id: null, sort: 'published_at', direction: 'desc', page: 1 })}
                            >
                                Reset
                            </button>
                        </div>
                    </div>

                    {/* Table */}
                    <div className="border">
                        <div className="grid grid-cols-12 gap-2 p-3 font-semibold border-b">
                            <div className="col-span-5">Title</div>
                            <div className="col-span-2">Status</div>
                            <div className="col-span-2">Category</div>
                            <div className="col-span-1">Likes</div>
                            <div className="col-span-1">Comments</div>
                            <div className="col-span-1">Actions</div>
                        </div>

                        {posts.data.map((p: any) => (
                            <div key={p.id} className="grid grid-cols-12 gap-2 p-3 border-b">
                                <div className="col-span-5">
                                    <div className="font-medium">{p.title}</div>
                                    <div className="text-sm text-gray-600">{p.slug}</div>
                                </div>
                                <div className="col-span-2">{p.status}</div>
                                <div className="col-span-2">{p.category?.name ?? '-'}</div>
                                <div className="col-span-1">{p.likes_count ?? 0}</div>
                                <div className="col-span-1">{p.comments_count ?? 0}</div>
                                <div className="col-span-1">
                                    <a className="underline" href={route('posts.edit', p.id)}>Edit</a>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination */}
                    <div className="flex flex-wrap gap-2">
                        {posts.links.map((l: any, idx: number) => (
                            <button
                                key={idx}
                                disabled={!l.url}
                                className={`border px-3 py-2 ${l.active ? 'bg-gray-200' : ''}`}
                                onClick={() => l.url && router.get(l.url, {}, { preserveState: true, replace: true })}
                                dangerouslySetInnerHTML={{ __html: l.label }}
                            />
                        ))}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
