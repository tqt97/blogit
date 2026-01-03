import InputError from '@/components/input-error';
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
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { useAutoSlug } from '@/hooks/use-auto-slug';
import { Link, useForm } from '@inertiajs/react';
import React, { useEffect, useRef, useState } from 'react';

type RouteForm = {
    action: string;
    method: 'post' | 'put' | 'patch' | 'delete' | 'get';
};

type FormData = {
    name: string;
    slug: string;
};

type Props = {
    mode: 'create' | 'edit';
    title: string;
    submitText: string;
    form: RouteForm;
    cancelHref: string;
    defaultValues?: Partial<FormData>;
    resetOnSuccess?: (keyof FormData)[];
    warnOnLeave?: boolean;
};

export default function TagForm({
    mode,
    title,
    submitText,
    form,
    cancelHref,
    defaultValues,
    resetOnSuccess = [],
    warnOnLeave = true,
}: Props) {
    const nameRef = useRef<HTMLInputElement | null>(null);
    const slugRef = useRef<HTMLInputElement | null>(null);

    const [confirmLeaveOpen, setConfirmLeaveOpen] = useState(false);

    const {
        data,
        setData,
        processing,
        errors,
        hasErrors,
        isDirty,
        clearErrors,
        reset,
        submit,
    } = useForm<FormData>({
        name: defaultValues?.name ?? '',
        slug: defaultValues?.slug ?? '',
    });

    // ---- Auto slug hook (debounce + touched + reset-to-auto)
    const {
        slug,
        setSlug,
        touched: slugTouched,
        setTouched: setSlugTouched,
        resetToAuto,
    } = useAutoSlug(data.name, {
        initialSlug: defaultValues?.slug ?? '',
        delay: 250,
    });

    // Sync: defaultValues.name -> form data.name
    useEffect(() => {
        setData('name', defaultValues?.name ?? '');
    }, [defaultValues?.name, setData]);

    // Sync: defaultValues.slug -> form data.slug
    useEffect(() => {
        setData('slug', slug);
    }, [slug, setData]);

    // Focus first field with error
    useEffect(() => {
        if (!hasErrors) return;
        if (errors.name) nameRef.current?.focus();
        else if (errors.slug) slugRef.current?.focus();
    }, [hasErrors, errors.name, errors.slug]);

    // Warn on browser close/refresh
    useEffect(() => {
        if (!warnOnLeave) return;

        const beforeUnload = (e: BeforeUnloadEvent) => {
            if (!isDirty || processing) return;
            e.preventDefault();
            e.returnValue = '';
        };

        window.addEventListener('beforeunload', beforeUnload);
        return () => window.removeEventListener('beforeunload', beforeUnload);
    }, [isDirty, processing, warnOnLeave]);

    const onSubmit = (e: React.FormEvent) => {
        e.preventDefault();

        submit(form.method, form.action, {
            preserveScroll: true,
            onStart: () => clearErrors(),
            onSuccess: () => {
                if (resetOnSuccess.length) reset(...resetOnSuccess);

                if (mode === 'create') {
                    setSlugTouched(false);
                    setSlug(''); // resetToAuto()
                }
            },
        });
    };

    const submitDisabled = processing || (mode === 'edit' && !isDirty);

    const onCancelClick = (e: React.MouseEvent) => {
        if (!warnOnLeave) return;

        if (isDirty && !processing) {
            e.preventDefault();
            setConfirmLeaveOpen(true);
        }
    };

    return (
        <div className="overflow-hidden rounded border bg-card">
            <div className="flex items-center justify-between border-b px-6 py-4">
                <h1 className="text-lg font-semibold">{title}</h1>

                {mode === 'edit' && !processing && !isDirty && (
                    <span className="text-xs text-muted-foreground">
                        No changes
                    </span>
                )}
                {isDirty && !processing && (
                    <span className="text-xs text-muted-foreground">
                        Unsaved changes
                    </span>
                )}
            </div>

            <div className="p-6">
                <form onSubmit={onSubmit} className="flex flex-col gap-6">
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="name">Name</Label>
                            <Input
                                ref={nameRef}
                                id="name"
                                name="name"
                                required
                                autoFocus
                                placeholder="Laravel"
                                value={data.name}
                                onChange={(e) =>
                                    setData('name', e.target.value)
                                }
                            />
                            <InputError message={errors.name} />
                        </div>

                        <div className="grid gap-2">
                            <div className="flex items-center justify-between">
                                <Label htmlFor="slug">Slug</Label>

                                <button
                                    type="button"
                                    className="text-xs text-muted-foreground underline-offset-4 hover:underline"
                                    onClick={() => resetToAuto()}
                                >
                                    Auto from name
                                </button>
                            </div>

                            <Input
                                ref={slugRef}
                                id="slug"
                                name="slug"
                                placeholder="laravel"
                                value={slug}
                                onChange={(e) => {
                                    setSlugTouched(true);
                                    setSlug(e.target.value);
                                }}
                            />

                            <InputError message={errors.slug} />

                            {!slugTouched && (
                                <p className="text-xs text-muted-foreground">
                                    Slug is auto-generated from name. You can
                                    edit it anytime.
                                </p>
                            )}
                        </div>

                        <div className="flex items-center gap-2">
                            <Button
                                type="submit"
                                disabled={submitDisabled}
                                className="hover:cursor-pointer"
                            >
                                {processing && <Spinner />}
                                {submitText}
                            </Button>

                            <Button asChild variant="secondary">
                                <Link href={cancelHref} onClick={onCancelClick}>
                                    Cancel
                                </Link>
                            </Button>

                            {isDirty && (
                                <Button
                                    className="hover:cursor-pointer"
                                    type="button"
                                    variant="outline"
                                    disabled={processing}
                                    onClick={() => {
                                        reset();
                                        setData(
                                            'name',
                                            defaultValues?.name ?? '',
                                        );
                                        setSlug(defaultValues?.slug ?? '');
                                        setSlugTouched(false);
                                    }}
                                >
                                    Reset changes
                                </Button>
                            )}
                        </div>
                    </div>
                </form>
            </div>

            <AlertDialog
                open={confirmLeaveOpen}
                onOpenChange={setConfirmLeaveOpen}
            >
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Discard changes?</AlertDialogTitle>
                        <AlertDialogDescription>
                            You have unsaved changes. If you leave now, they
                            will be lost.
                        </AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel className="hover:cursor-pointer">
                            Stay
                        </AlertDialogCancel>
                        <AlertDialogAction
                            asChild
                            className="hover:cursor-pointer"
                        >
                            <Link href={cancelHref}>Leave</Link>
                        </AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
    );
}
