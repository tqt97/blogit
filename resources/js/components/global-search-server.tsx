import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandItem,
    CommandList,
} from '@/components/ui/command';
import { Input } from '@/components/ui/input';
import {
    Popover,
    PopoverAnchor,
    PopoverContent,
} from '@/components/ui/popover';
import { useCommandSearch } from '@/hooks/use-command-search';
import { useDebounced } from '@/hooks/use-debounced';
import { SearchItemStatic } from '@/types/ui/search-item-static';
import { router, usePage } from '@inertiajs/react';
import { Command as CommandIcon, Search } from 'lucide-react';
import React, { useEffect, useMemo, useRef, useState } from 'react';

type SearchPageProps = {
    searchItems?: SearchItemStatic[];
};

function groupBy(items: SearchItemStatic[]) {
    const map = new Map<string, SearchItemStatic[]>();
    for (const it of items) {
        const key = it.group ?? 'Other';
        map.set(key, [...(map.get(key) ?? []), it]);
    }
    return Array.from(map.entries());
}

function highlightMatch(text: string, query: string) {
    const q = query.trim();
    if (!q) return text;

    const idx = text.toLowerCase().indexOf(q.toLowerCase());
    if (idx === -1) return text;

    const before = text.slice(0, idx);
    const match = text.slice(idx, idx + q.length);
    const after = text.slice(idx + q.length);

    return (
        <>
            {before}
            <span className="font-semibold">{match}</span>
            {after}
        </>
    );
}

export function GlobalSearchServer() {
    const wrapperRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);
    const itemRefs = useRef<Map<number, HTMLDivElement>>(new Map());

    const { open, setOpen, openDropdown, closeDropdown } = useCommandSearch(
        inputRef,
        {
            keys: ['k', 'p'],
        },
    );

    const [query, setQuery] = useState('');
    const debouncedQuery = useDebounced(query, 250);
    const [activeIndex, setActiveIndex] = useState(-1);

    // Read server results from Inertia props
    const page = usePage<SearchPageProps>();
    const results = useMemo<SearchItemStatic[]>(() => {
        return page.props.searchItems ?? [];
    }, [page.props.searchItems]);

    const grouped = useMemo(() => groupBy(results), [results]);
    const flatResults = results;

    const indexByHref = useMemo(() => {
        const map = new Map<string, number>();
        flatResults.forEach((it, idx) => map.set(it.href, idx));
        return map;
    }, [flatResults]);

    useEffect(() => {
        itemRefs.current.clear();
    }, [open, flatResults]);

    // Server-side search: debounce + preserveState
    useEffect(() => {
        if (!open) return;

        const q = debouncedQuery.trim();
        // optional: don't search when empty (you can still return "recent items" from server if you want)
        if (!q) return;

        router.get(
            '/admin/search',
            { q },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only: ['searchItems'],
            },
        );
    }, [debouncedQuery, open]);

    useEffect(() => {
        if (!open) return;
        if (activeIndex < 0) return;

        const el = itemRefs.current.get(activeIndex);
        if (!el) return;

        requestAnimationFrame(() => {
            el.scrollIntoView({ block: 'nearest' });
        });
    }, [open, activeIndex]);

    const selectHref = (href: string) => {
        closeDropdown();
        inputRef.current?.blur();
        router.visit(href);
    };

    const pickHrefOnEnter = () => {
        const q = query.trim();
        if (flatResults.length === 0) return undefined;

        if (q === '') {
            const dash =
                flatResults.find(
                    (it) => it.label.toLowerCase() === 'dashboard',
                ) ?? flatResults[0];
            return dash?.href;
        }

        return flatResults[activeIndex >= 0 ? activeIndex : 0]?.href;
    };

    const openAndInitActive = () => {
        openDropdown();
        setActiveIndex(flatResults.length > 0 ? 0 : -1);
    };

    const onInputKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
        if (!open) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (flatResults.length === 0) return;
            setActiveIndex((i) => (i < 0 ? 0 : (i + 1) % flatResults.length));
            return;
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (flatResults.length === 0) return;
            setActiveIndex((i) => (i <= 0 ? flatResults.length - 1 : i - 1));
            return;
        }

        if (e.key === 'Enter') {
            e.preventDefault();
            const href = pickHrefOnEnter();
            if (href) selectHref(href);
            return;
        }
    };

    return (
        <Popover
            open={open}
            onOpenChange={(v) => {
                setOpen(v);
                setActiveIndex(v && flatResults.length > 0 ? 0 : -1);
            }}
            modal={false}
        >
            <PopoverAnchor asChild>
                <div ref={wrapperRef} className="relative hidden md:block">
                    <Search className="pointer-events-none absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-muted-foreground" />

                    <Input
                        ref={inputRef}
                        id="search-global"
                        name="search"
                        placeholder="Search"
                        value={query}
                        autoComplete="off"
                        className="w-[320px] pr-14 pl-9 lg:w-[420px] xl:w-[520px] dark:bg-gray-900"
                        onChange={(e) => {
                            const val = e.target.value;
                            setQuery(val);
                            if (!open) openDropdown();
                            setActiveIndex(0);
                        }}
                        onKeyDown={onInputKeyDown}
                        onClick={openAndInitActive}
                        onFocus={openAndInitActive}
                    />

                    <div className="pointer-events-none absolute top-1/2 right-2 -translate-y-1/2">
                        <kbd className="inline-flex items-center gap-1 rounded bg-muted px-2 py-1 text-xs text-muted-foreground">
                            <CommandIcon className="h-3 w-3" /> K / P
                        </kbd>
                    </div>
                </div>
            </PopoverAnchor>

            <PopoverContent
                align="end"
                side="bottom"
                sideOffset={8}
                className="w-[320px] p-0 lg:w-[420px] xl:w-[520px]"
                onOpenAutoFocus={(e) => e.preventDefault()}
                onCloseAutoFocus={(e) => e.preventDefault()}
                onInteractOutside={(e) => {
                    const target = e.target as Node;
                    if (wrapperRef.current?.contains(target)) {
                        e.preventDefault();
                        return;
                    }
                    closeDropdown();
                }}
            >
                <Command shouldFilter={false}>
                    <CommandList className="max-h-80 overflow-auto">
                        <CommandEmpty>No results found.</CommandEmpty>

                        {grouped.map(([groupName, items]) => (
                            <CommandGroup key={groupName} heading={groupName}>
                                {items.map((item) => {
                                    const idx =
                                        indexByHref.get(item.href) ?? -1;

                                    return (
                                        <CommandItem
                                            key={item.href}
                                            value={item.label}
                                            onSelect={() =>
                                                selectHref(item.href)
                                            }
                                            onMouseEnter={() =>
                                                setActiveIndex(idx)
                                            }
                                            ref={(node) => {
                                                if (idx < 0) return;
                                                const map = itemRefs.current;
                                                if (!node) map.delete(idx);
                                                else map.set(idx, node);
                                            }}
                                            className={
                                                idx === activeIndex
                                                    ? 'bg-accent text-accent-foreground'
                                                    : undefined
                                            }
                                        >
                                            {highlightMatch(item.label, query)}
                                        </CommandItem>
                                    );
                                })}
                            </CommandGroup>
                        ))}
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
