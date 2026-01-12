import { Button } from '@/components/ui/button';
import { router } from '@inertiajs/react';

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

type Props = {
    links?: PaginationLink[];
    className?: string;
    preserveState?: boolean;
    preserveScroll?: boolean;
    replace?: boolean;
    buttonClassName?: string;
};

export function TablePaginationLinks({
    links,
    className,
    preserveState = true,
    preserveScroll = true,
    replace = false,
    buttonClassName = 'hover:cursor-pointer hover:bg-gray-900 hover:text-gray-50',
}: Props) {
    if (!links?.length) return null;

    return (
        <div
            className={['flex items-center justify-end', className]
                .filter(Boolean)
                .join(' ')}
        >
            <div className="mt-4 flex flex-wrap gap-1">
                {links.map((link, i) => (
                    <Button
                        key={i}
                        className={buttonClassName}
                        variant={link.active ? 'default' : 'secondary'}
                        size="sm"
                        disabled={!link.url}
                        onClick={() => {
                            if (!link.url) return;
                            router.get(
                                link.url,
                                {},
                                {
                                    preserveState,
                                    preserveScroll,
                                    replace,
                                },
                            );
                        }}
                    >
                        <span
                            dangerouslySetInnerHTML={{
                                __html: link.label,
                            }}
                        />
                    </Button>
                ))}
            </div>
        </div>
    );
}
