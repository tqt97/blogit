import { Pagination } from '@/types/pagination';
import { Link } from '@inertiajs/react';
import { Button } from './ui/button';

export default function TablePagination({
    links,
    // total,
    // to,
    // from,
}: Pagination) {
    return (
        <div className="flex items-center dark:bg-gray-900 flex flex-col justify-center gap-4 border-gray-200 bg-white px-3 py-3 sm:flex-row">
            {/* <div className="item-center flex justify-between">
                <span className="text-sm text-muted-foreground">
                    Showing {from} to {to} of {total} entries
                </span>
            </div> */}
            <div className="flex items-center space-x-2">
                {links.map((link, index) =>
                    link.url != null ? (
                        <Link href={link.url || '#'} key={index}>
                            <Button
                                variant={'outline'}
                                className={
                                    link.active
                                        ? 'bg-gray-400 text-white dark:bg-gray-700 dark:text-gray-200 dark:border-gray-700'
                                        : ''
                                }
                                size={'sm'}
                                disabled={link.active}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        </Link>
                    ) : (
                        <Button
                            key={index}
                            variant={'outline'}
                            size={'sm'}
                            disabled
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    ),
                )}
            </div>
        </div>
    );
}
