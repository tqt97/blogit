import { usePage } from '@inertiajs/react';
import * as React from 'react';
import { toast } from 'sonner';

type Flash = {
    success?: string;
    error?: string;
    message?: string;
};

export function useFlashToast() {
    const { props } = usePage<{ flash?: Flash }>();

    React.useEffect(() => {
        const flash = props.flash ?? {};

        if (flash.success) toast.success(flash.success);
        if (flash.error) toast.error(flash.error);
        if (flash.message) toast.message(flash.message);
    }, [props.flash]);
}
