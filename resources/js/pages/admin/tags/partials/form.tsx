import { CrudFormShell } from '@/components/forms/crud-form-shell';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useAutoSlug } from '@/hooks/use-auto-slug';
import { useFormFocusOnError } from '@/hooks/use-form-focus-on-error';
import type { FormTagData, RouteForm } from '@/types';
import { useForm } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export default function TagForm(props: {
    mode: 'create' | 'edit';
    title: string;
    submitText: string;
    form: RouteForm;
    cancelHref: string;
    defaultValues?: Partial<Omit<FormTagData, 'intent'>>;
    resetOnSuccess?: (keyof Omit<FormTagData, 'intent'>)[];
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

    const nameRef = useRef<HTMLInputElement | null>(null);
    const slugRef = useRef<HTMLInputElement | null>(null);

    const inertia = useForm<FormTagData>({
        name: defaultValues?.name ?? '',
        slug: defaultValues?.slug ?? '',
        intent: 'default',
    });

    const { data, setData, processing, errors, hasErrors } = inertia;

    const { slug, setSlug, touched, setTouched, resetToAuto } = useAutoSlug(
        data.name,
        {
            initialSlug: defaultValues?.slug ?? '',
            delay: 250,
        },
    );

    useEffect(
        () => setData('name', defaultValues?.name ?? ''),
        [defaultValues?.name, setData],
    );
    useEffect(() => setData('slug', slug), [slug, setData]);
    useFormFocusOnError(
        {
            hasErrors: hasErrors,
            errors: errors,
            refMap: { name: nameRef, slug: slugRef },
        },
        { order: ['name', 'slug'] },
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
                    nameRef.current?.focus();
                }
            }}
            onResetExtras={() => {
                setData('name', defaultValues?.name ?? '');
                setSlug(defaultValues?.slug ?? '');
                setTouched(false);
            }}
        >
            <div className="grid gap-6">
                <div className="grid gap-2">
                    <Label htmlFor="name">Name</Label>
                    <Input
                        ref={nameRef}
                        id="name"
                        required
                        autoFocus
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                    />
                    <InputError message={errors.name} />
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
            </div>
        </CrudFormShell>
    );
}
