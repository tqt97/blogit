import type { Input } from '@/components/ui/input';
import type { ComponentProps } from 'react';

/**
 * Props for PasswordInput component
 * - Inherits all Input props (except `type`)
 * - Adds extra options for password visibility toggle
 */
export type PasswordInputProps = Omit<ComponentProps<typeof Input>, 'type'> & {
    /** Custom class for the toggle button */
    buttonClassName?: string;

    /** aria-label when password is visible */
    showLabel?: string;

    /** aria-label when password is hidden */
    hideLabel?: string;

    /** Show password by default on mount */
    defaultShow?: boolean;

    /** Callback fired when visibility is toggled */
    onToggle?: (show: boolean) => void;

    /**
     * Automatically hide password when condition becomes true
     * Commonly used with form processing/submitting state
     */
    autoHideWhen?: boolean;
};
