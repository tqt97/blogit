import { Flash } from '@/types';
import { usePage } from '@inertiajs/react';
import { useEffect, useRef } from 'react';
import { toast } from 'sonner';

export function useFlashToast() {
    const { flash } = usePage().props as { flash?: Flash };
    const lastFlashId = useRef<string | null>(null);

    useEffect(() => {
        const id = flash?.id;

        if (!id) {
            const msg =
                flash?.success ??
                flash?.error ??
                flash?.warning ??
                flash?.info ??
                null;

            if (!msg) return;
            if (lastFlashId.current === msg) return;
            lastFlashId.current = msg;

            if (flash?.success) toast.success(flash.success);
            if (flash?.error) toast.error(flash.error);
            if (flash?.warning) toast.warning(flash.warning);
            if (flash?.info) toast.info(flash.info);

            return;
        }

        if (lastFlashId.current === id) return;
        lastFlashId.current = id;

        if (flash?.success) toast.success(flash.success);
        if (flash?.error) toast.error(flash.error);
        if (flash?.warning) toast.warning(flash.warning);
        if (flash?.info) toast.info(flash.info);
    }, [flash?.id, flash?.success, flash?.error, flash?.warning, flash?.info]);
}
