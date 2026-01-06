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
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import { CrudFormShellProps, SubmitIntent } from '@/types';
import { Link } from '@inertiajs/react';
import { Plus, RotateCcw, Save, X } from 'lucide-react';
import React, { useEffect, useState } from 'react';

export function CrudFormShell<TData extends { intent: SubmitIntent }>({
    mode,
    title,
    submitText,
    form,
    cancelHref,
    warnOnLeave = true,
    resetOnSuccess = [],
    inertia,
    children,
    onSuccessIntent,
    onResetExtras,
    headerRight,
}: CrudFormShellProps<TData>) {
    const [confirmLeaveOpen, setConfirmLeaveOpen] = useState(false);

    const { data, setData, processing, isDirty, clearErrors, reset, submit } =
        inertia;

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
                onSuccessIntent?.(data.intent);

                if (
                    mode === 'create' &&
                    data.intent === 'create_and_continue'
                ) {
                    if (resetOnSuccess.length) {
                        reset(...(resetOnSuccess as (keyof TData)[]));
                    }
                }
            },
            onFinish: () => {
                setData('intent', 'default' as TData['intent']);
            },
        });
    };

    const submitDisabled = processing || (mode === 'edit' && !isDirty);

    const onCancelClick = (e: React.MouseEvent) => {
        if (!warnOnLeave) return;
        if (processing) return;

        if (isDirty) {
            e.preventDefault();
            setConfirmLeaveOpen(true);
        }
    };

    return (
        <>
            <Card className="w-full overflow-hidden">
                <CardHeader>
                    <div className="flex items-center justify-between border-b pb-6">
                        <CardTitle className="text-lg font-bold">
                            {title}
                        </CardTitle>

                        {headerRight ?? (
                            <>
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
                            </>
                        )}
                    </div>
                </CardHeader>

                <form onSubmit={onSubmit} className="flex flex-col gap-6">
                    <CardContent>{children}</CardContent>

                    <CardFooter>
                        <div className="flex flex-wrap items-center gap-2">
                            <div className="flex items-center gap-2">
                                <Button
                                    type="submit"
                                    disabled={submitDisabled}
                                    className="gap-2 hover:cursor-pointer"
                                    onClick={() =>
                                        setData(
                                            'intent',
                                            'default' as TData['intent'],
                                        )
                                    }
                                >
                                    {processing ? (
                                        <Spinner />
                                    ) : (
                                        <Save className="h-4 w-4" />
                                    )}
                                    <span>
                                        {processing ? 'Saving...' : submitText}
                                    </span>
                                </Button>

                                {mode === 'create' && (
                                    <Button
                                        type="submit"
                                        variant="outline"
                                        disabled={processing}
                                        className="gap-2 hover:cursor-pointer"
                                        onClick={() =>
                                            setData(
                                                'intent',
                                                'create_and_continue' as TData['intent'],
                                            )
                                        }
                                    >
                                        {processing ? (
                                            <Spinner />
                                        ) : (
                                            <Plus className="h-4 w-4" />
                                        )}
                                        <span>Save & add another</span>
                                    </Button>
                                )}
                            </div>

                            <div className="mx-2 hidden h-6 w-px bg-border sm:block" />

                            <div className="flex items-center gap-2">
                                <Button
                                    asChild
                                    variant="secondary"
                                    disabled={processing}
                                >
                                    <Link
                                        href={cancelHref}
                                        onClick={onCancelClick}
                                        aria-disabled={processing}
                                        className="gap-2"
                                    >
                                        <X className="h-4 w-4" />
                                        <span>Cancel</span>
                                    </Link>
                                </Button>

                                {isDirty && (
                                    <Button
                                        type="button"
                                        variant="outline"
                                        disabled={processing}
                                        className="gap-2 hover:cursor-pointer"
                                        onClick={() => {
                                            reset();
                                            onResetExtras?.();
                                        }}
                                    >
                                        <RotateCcw className="h-4 w-4" />
                                        <span>Reset</span>
                                    </Button>
                                )}
                            </div>
                        </div>
                    </CardFooter>
                </form>
            </Card>

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
        </>
    );
}
