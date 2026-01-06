import { Button } from '@/components/ui/button';
import { SortColumnHeaderProps } from '@/types';
import { ArrowUpDown } from 'lucide-react';

export function SortableColumnHeader<TData, TValue>({
    column,
    title,
}: SortColumnHeaderProps<TData, TValue>) {
    return (
        <Button
            type="button"
            variant="ghost"
            size="sm"
            className="flex items-center gap-1 px-0 hover:cursor-pointer hover:bg-gray-100 hover:text-gray-950"
            onClick={() => {
                const current = column.getIsSorted();
                column.toggleSorting(current === 'asc');
            }}
        >
            <span className="font-bold">{title}</span>
            <ArrowUpDown className="h-4 w-4 opacity-50" />
        </Button>
    );
}
