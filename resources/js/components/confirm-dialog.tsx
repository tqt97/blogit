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
import { ACTION_TEXT, CONFIRM_TEXT } from '@/constants/ui';
import { ConfirmDialogProps } from '@/types';

export function ConfirmDialog({
    open,
    onOpenChange,
    title,
    description,
    confirmText = CONFIRM_TEXT,
    cancelText = ACTION_TEXT.action_cancel,
    onConfirm,
    confirmDisabled = false,
    variant = 'destructive',
}: ConfirmDialogProps) {
    return (
        <AlertDialog open={open} onOpenChange={onOpenChange}>
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>{title}</AlertDialogTitle>

                    {description ? (
                        <AlertDialogDescription>
                            {description}
                        </AlertDialogDescription>
                    ) : null}
                </AlertDialogHeader>

                <AlertDialogFooter>
                    <AlertDialogCancel className="hover:cursor-pointer">
                        {cancelText}
                    </AlertDialogCancel>

                    <AlertDialogAction
                        className={[
                            'hover:cursor-pointer',
                            variant === 'destructive'
                                ? 'bg-destructive text-white hover:bg-destructive/90'
                                : '',
                        ].join(' ')}
                        onClick={onConfirm}
                        aria-disabled={confirmDisabled}
                        style={
                            confirmDisabled
                                ? { pointerEvents: 'none', opacity: 0.6 }
                                : undefined
                        }
                    >
                        {confirmText}
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    );
}
