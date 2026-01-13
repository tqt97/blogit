import { CrudFormShell } from '@/components/forms/crud-form-shell';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { useAutoSlug } from '@/hooks/use-auto-slug';
import { useFormFocusOnError } from '@/hooks/use-form-focus-on-error';
import type { FormPostData, RouteForm } from '@/types';
import { useForm } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export default function PostForm(props: {
    mode: 'create' | 'edit';
    title: string;
    submitText: string;
    form: RouteForm;
    cancelHref: string;
    defaultValues?: Partial<Omit<FormPostData, 'intent'>>;
    resetOnSuccess?: (keyof Omit<FormPostData, 'intent'>)[];
}) {
    const {
        mode,
        title,
        submitText,
        form,
        cancelHref,
        defaultValues,
        resetOnSuccess,
    } = props;

    const titleRef = useRef<HTMLInputElement | null>(null);
    const slugRef = useRef<HTMLInputElement | null>(null);

    const inertia = useForm<FormPostData>({
        title: defaultValues?.title ?? '',
        slug: defaultValues?.slug ?? '',
        excerpt: defaultValues?.excerpt ?? '',
        content: defaultValues?.content ?? '',
        category_id: defaultValues?.categoryId ?? '',
        tag_ids: defaultValues?.tagIds ?? [],
        status: defaultValues?.status ?? '',
        intent: 'default',
    });

    const { data, setData, processing, errors, hasErrors } = inertia;

    const { slug, setSlug, touched, setTouched, resetToAuto } = useAutoSlug(
        data.title,
        {
            initialSlug: defaultValues?.slug ?? '',
            delay: 250,
        },
    );

    useEffect(
        () => setData('title', defaultValues?.title ?? ''),
        [defaultValues?.title, setData],
    );
    useEffect(() => setData('slug', slug), [slug, setData]);
    useFormFocusOnError(
        {
            hasErrors: hasErrors,
            errors: errors,
            refMap: { title: titleRef, slug: slugRef },
        },
        { order: ['title', 'slug'] },
    );

    return (
        <CrudFormShell
            mode={mode}
            title={title}
            submitText={submitText}
            form={form}
            cancelHref={cancelHref}
            resetOnSuccess={resetOnSuccess}
            inertia={inertia}
            onSuccessIntent={(intent) => {
                if (mode === 'create' && intent === 'create_and_continue') {
                    setTouched(false);
                    setSlug('');
                    titleRef.current?.focus();
                }
            }}
            onResetExtras={() => {
                setData('title', defaultValues?.title ?? '');
                setSlug(defaultValues?.slug ?? '');
                setTouched(false);
            }}
        >
            <div className="grid gap-6">
                <div className="grid gap-2">
                    <Label htmlFor="title">Title <span className='text-red-500'>*</span></Label>
                    <Input
                        ref={titleRef}
                        id="title"
                        required
                        autoFocus
                        value={data.title}
                        onChange={(e) => setData('title', e.target.value)}
                    />
                    <InputError message={errors.title} />
                </div>

                <div className="grid gap-2">
                    <div className="flex items-center justify-between">
                        <Label htmlFor="slug">Slug</Label>
                        <button
                            type="button"
                            disabled={processing}
                            className="text-xs text-muted-foreground underline-offset-4 hover:underline"
                            onClick={resetToAuto}
                        >
                            Auto from name
                        </button>
                    </div>

                    <Input
                        ref={slugRef}
                        id="slug"
                        value={slug}
                        onChange={(e) => {
                            setTouched(true);
                            setSlug(e.target.value);
                        }}
                    />
                    <InputError message={errors.slug} />

                    {!touched && (
                        <p className="text-xs text-muted-foreground">
                            Slug is auto-generated from name. You can edit it
                            anytime.
                        </p>
                    )}
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="excerpt">Excerpt</Label>
                    <Textarea id='excerpt' placeholder="Type excerpt here..."/>
                    <InputError message={errors.excerpt} />
                </div>

                <div className="grid gap-2">
                    <Label htmlFor="content">Content</Label>
                    <Textarea id='content' placeholder="Type content here..."/>
                    <InputError message={errors.content} />
                </div>
            </div>
        </CrudFormShell>
    );
}
