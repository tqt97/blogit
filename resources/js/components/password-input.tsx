import { Input } from '@/components/ui/input';
import { PasswordInputProps } from '@/types/ui/password-input';
import { Eye, EyeOff } from 'lucide-react';
import { useEffect, useId, useState } from 'react';

export function PasswordInput({
    id,
    className,
    buttonClassName,
    showLabel = 'Show password',
    hideLabel = 'Hide password',
    defaultShow = false,
    autoHideWhen = false,
    onToggle,
    ...props
}: PasswordInputProps) {
    const generatedId = useId();
    const inputId = id ?? generatedId;
    const [show, setShow] = useState(defaultShow);

    // sync if defaultShow change
    useEffect(() => {
        setShow(defaultShow);
    }, [defaultShow]);

    // auto hide when submit / processing
    useEffect(() => {
        if (autoHideWhen && show) {
            setShow(false);
            onToggle?.(false);
        }
    }, [autoHideWhen, show, onToggle]);

    const toggle = () => {
        setShow((prev) => {
            const next = !prev;
            onToggle?.(next);
            return next;
        });
    };

    return (
        <div className="relative">
            <Input
                {...props}
                id={inputId}
                type={show ? 'text' : 'password'}
                className={['pr-10', className].filter(Boolean).join(' ')}
            />

            <button
                type="button"
                onMouseDown={(e) => e.preventDefault()}
                onClick={toggle}
                aria-label={show ? hideLabel : showLabel}
                aria-pressed={show}
                className={[
                    'absolute top-1/2 right-[1px] -translate-y-1/2 rounded-md p-2',
                    'text-muted-foreground hover:text-foreground',
                    'hover:cursor-pointer hover:bg-gray-900 hover:text-gray-50 focus:ring-2 focus:ring-ring focus:outline-none',
                    buttonClassName,
                ]
                    .filter(Boolean) // remove undefined|null|false => avoid "undefined" class
                    .join(' ')} // merge class- array to string
            >
                {show ? (
                    <EyeOff className="h-4 w-4" />
                ) : (
                    <Eye className="h-4 w-4" />
                )}
            </button>
        </div>
    );
}
