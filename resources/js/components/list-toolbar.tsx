import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { ListToolbarProps } from '@/types';
import {
    ChevronDownIcon,
    ListFilterIcon,
    SearchIcon,
    XIcon,
} from 'lucide-react';

export function ListToolbar({
    bulkMenuOpen,
    setBulkMenuOpen,
    hasSelection,
    selectedCount,
    onClearSelection,
    onBulkDeleteClick,

    searchKey,
    defaultSearch,
    searchInput,
    onSearchChange,
    onSearchKeyDown,
    onClearSearch,
    placeholder = 'Search...',

    perPage,
    perPageOptions = [5, 10, 15, 25, 50, 100],
    onPerPageChange,

    rightSlot,

    loading = false,
}: ListToolbarProps) {
    return (
        <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div className="flex items-center justify-end gap-2">
                {/* Bulk actions */}
                <DropdownMenu
                    open={bulkMenuOpen}
                    onOpenChange={setBulkMenuOpen}
                >
                    <DropdownMenuTrigger asChild>
                        <Button
                            variant="secondary"
                            disabled={!hasSelection}
                            className="gap-2 hover:cursor-pointer"
                        >
                            Bulk actions
                            <ChevronDownIcon className="size-4 opacity-70" />
                        </Button>
                    </DropdownMenuTrigger>

                    <DropdownMenuContent align="end">
                        <DropdownMenuItem
                            className="text-destructive hover:cursor-pointer focus:text-destructive"
                            onClick={onBulkDeleteClick}
                        >
                            Delete selected ({selectedCount})
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>

                {hasSelection ? (
                    <Button
                        variant="secondary"
                        className="text-destructive hover:cursor-pointer hover:bg-destructive hover:text-white"
                        onClick={onClearSelection}
                    >
                        Clear ({selectedCount})
                    </Button>
                ) : null}

                {/* Per page */}
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button
                            variant="secondary"
                            className="gap-2 hover:cursor-pointer"
                            disabled={loading}
                        >
                            <ListFilterIcon className="size-4 opacity-70" />
                            Show {perPage} records
                            <ChevronDownIcon className="size-4 opacity-70" />
                        </Button>
                    </DropdownMenuTrigger>

                    <DropdownMenuContent align="end">
                        {perPageOptions.map((n) => (
                            <DropdownMenuItem
                                key={n}
                                disabled={loading}
                                className={
                                    n === perPage ? 'font-medium' : undefined
                                }
                                onClick={() => onPerPageChange(n)}
                            >
                                Show {n} records
                            </DropdownMenuItem>
                        ))}
                    </DropdownMenuContent>
                </DropdownMenu>

                {rightSlot}
            </div>

            {/* Search */}
            <div className="relative flex-1 md:max-w-md">
                <SearchIcon className="pointer-events-none absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />

                <Input
                    key={searchKey}
                    defaultValue={defaultSearch}
                    placeholder={placeholder}
                    className="pr-9 pl-9"
                    onChange={(e) => onSearchChange(e.target.value)}
                    onKeyDown={onSearchKeyDown}
                />

                {(defaultSearch || searchInput) && (
                    <button
                        type="button"
                        aria-label="Clear search"
                        className="absolute top-1/2 right-0 -translate-y-1/2 rounded p-2 text-muted-foreground hover:cursor-pointer hover:bg-muted"
                        onClick={onClearSearch}
                    >
                        <XIcon className="h-4 w-4" />
                    </button>
                )}
            </div>
        </div>
    );
}
