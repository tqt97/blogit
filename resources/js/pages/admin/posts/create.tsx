import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Posts',
        href: dashboard().url,
    },
];

type Option = { id: number; label: string };

export default function Create({ categories, tags }: { categories: Option[]; tags: Option[] }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        slug: '',
        content: '',
        excerpt: '',
        category_id: '',
        tag_ids: [] as number[],
        status: 'draft',
        published_at: '',
    });

    const toggleTag = (id: number) => {
        setData('tag_ids', data.tag_ids.includes(id)
            ? data.tag_ids.filter(x => x !== id)
            : [...data.tag_ids, id]
        );
    };
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                {/* <div className="max-w-3xl mx-auto p-6"> */}
                <h1 className="text-xl font-semibold mb-4">Create Post</h1>

                <form onSubmit={(e) => { e.preventDefault(); post(route('posts.store')); }} className="space-y-4">

                    <div>
                        <label>Title</label>
                        <input className="w-full border p-2"
                            value={data.title}
                            onChange={(e) => setData('title', e.target.value)}
                        />
                        {errors.title && <div className="text-red-600">{errors.title}</div>}
                    </div>

                    <div>
                        <label>Slug</label>
                        <input className="w-full border p-2"
                            value={data.slug}
                            onChange={(e) => setData('slug', e.target.value)}
                        />
                        {errors.slug && <div className="text-red-600">{errors.slug}</div>}
                    </div>

                    <div>
                        <label>Category</label>
                        <select className="w-full border p-2"
                            value={data.category_id}
                            onChange={(e) => setData('category_id', e.target.value)}
                        >
                            <option value="">-- none --</option>
                            {categories.map(c => <option key={c.id} value={c.id}>{c.label}</option>)}
                        </select>
                        {errors.category_id && <div className="text-red-600">{errors.category_id}</div>}
                    </div>

                    <div>
                        <label>Tags</label>
                        <div className="flex flex-wrap gap-2">
                            {tags.map(t => (
                                <button type="button" key={t.id}
                                    className={`border px-2 py-1 ${data.tag_ids.includes(t.id) ? 'bg-gray-200' : ''}`}
                                    onClick={() => toggleTag(t.id)}
                                >
                                    {t.label}
                                </button>
                            ))}
                        </div>
                        {errors.tag_ids && <div className="text-red-600">{errors.tag_ids}</div>}
                    </div>

                    <div>
                        <label>Status</label>
                        <select className="w-full border p-2"
                            value={data.status}
                            onChange={(e) => setData('status', e.target.value)}
                        >
                            <option value="draft">draft</option>
                            <option value="pending">pending</option>
                            <option value="published">published</option>
                        </select>
                        {errors.status && <div className="text-red-600">{errors.status}</div>}
                    </div>

                    <div>
                        <label>Content</label>
                        <textarea className="w-full border p-2" rows={8}
                            value={data.content}
                            onChange={(e) => setData('content', e.target.value)}
                        />
                        {errors.content && <div className="text-red-600">{errors.content}</div>}
                    </div>

                    <button disabled={processing} className="border px-4 py-2">
                        {processing ? 'Saving...' : 'Save'}
                    </button>
                </form>
                {/* </div> */}
            </div>
        </AppLayout>
    );
}
