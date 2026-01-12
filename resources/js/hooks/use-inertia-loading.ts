import { useEffect, useState } from 'react';

export function useInertiaLoading() {
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        const start = () => setIsLoading(true);
        const stop = () => setIsLoading(false);

        document.addEventListener('inertia:start', start);
        document.addEventListener('inertia:finish', stop);
        document.addEventListener('inertia:cancel', stop);
        document.addEventListener('inertia:error', stop);

        return () => {
            document.removeEventListener('inertia:start', start);
            document.removeEventListener('inertia:finish', stop);
            document.removeEventListener('inertia:cancel', stop);
            document.removeEventListener('inertia:error', stop);
        };
    }, []);

    return isLoading;
}
