import { cn } from '@/lib/utils';
import * as React from 'react';

function Select({
    className,
    children,
    ...props
}: React.ComponentProps<'select'>) {
    return (
        <select
            data-slot="select"
            className={cn(
                'border-input bg-transparent text-foreground placeholder:text-muted-foreground',
                'flex h-9 w-full min-w-0 rounded-md border px-3 py-1 text-base shadow-xs',
                'transition-[color,box-shadow] outline-none',
                'focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50',
                'aria-invalid:border-destructive aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40',
                'disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50',
                'md:text-sm',
                className,
            )}
            {...props}
        >
            {children}
        </select>
    );
}

export { Select };
