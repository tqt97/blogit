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
import { SearchItemStatic } from '@/types/ui/search-item-static';
import { router } from '@inertiajs/react';
import { Command as CommandIcon, Search, X } from 'lucide-react';
import React, { useEffect, useMemo, useRef, useState } from 'react';

function groupBy(items: SearchItemStatic[]) {
    const map = new Map<string, SearchItemStatic[]>();
    for (const it of items) {
        const key = it.group ?? 'Other';
        map.set(key, [...(map.get(key) ?? []), it]);
    }
    return Array.from(map.entries());
}

// Fake data for local demo
const DEMO_ITEMS: SearchItemStatic[] = [
    { label: 'Dashboard', href: '/dashboard', group: 'Pages' },
    { label: 'Posts', href: '/admin/posts', group: 'Pages' },
    { label: 'Categories', href: '/admin/categories', group: 'Pages' },
    { label: 'Tags', href: '/admin/tags', group: 'Pages' },
];

export function GlobalSearchStatic() {
    const wrapperRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);
    const itemRefs = useRef<Map<number, HTMLDivElement>>(new Map());

    const { open, setOpen, openDropdown, closeDropdown } = useCommandSearch(
        inputRef,
        {
            keys: ['k', 'p'], // Ctrl/⌘+K or Ctrl/⌘+P
        },
    );

    const [query, setQuery] = useState('');
    const [activeIndex, setActiveIndex] = useState(-1);

    // Client-side filtering (static)
    const results = useMemo(() => {
        const q = query.trim().toLowerCase();
        if (!q) return DEMO_ITEMS;

        return DEMO_ITEMS.filter((it) => {
            const label = it.label.toLowerCase();
            const group = (it.group ?? '').toLowerCase();
            return label.includes(q) || group.includes(q);
        });
    }, [query]);

    const grouped = useMemo(() => groupBy(results), [results]);
    const flatResults = results;

    const indexByHref = useMemo(() => {
        const map = new Map<string, number>();
        flatResults.forEach((it, idx) => map.set(it.href, idx));
        return map;
    }, [flatResults]);

    // Clear stored DOM refs when list changes (outside render)
    useEffect(() => {
        itemRefs.current.clear();
    }, [open, flatResults]);

    // Scroll active item into view
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

        // query empty -> prefer Dashboard
        if (q === '') {
            const dash =
                flatResults.find(
                    (it) => it.label.toLowerCase() === 'dashboard',
                ) ?? flatResults[0];
            return dash?.href;
        }

        // query not empty -> active if exists else first
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
            setActiveIndex((i) => (i < 0 ? 0 : (i + 1) % flatResults.length)); // wrap
            return;
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (flatResults.length === 0) return;
            setActiveIndex((i) => (i <= 0 ? flatResults.length - 1 : i - 1)); // wrap
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

                    {/* RIGHT ACTIONS */}
                    <div className="absolute top-1/2 right-2 flex -translate-y-1/2 items-center gap-1">
                        {/* Clear button */}
                        {query && (
                            <button
                                type="button"
                                aria-label="Clear search"
                                className="rounded p-1 text-muted-foreground hover:bg-accent hover:text-foreground focus:ring-2 focus:ring-ring focus:outline-none"
                                onClick={() => {
                                    setQuery('');
                                    setActiveIndex(
                                        flatResults.length > 0 ? 0 : -1,
                                    );
                                    inputRef.current?.focus();
                                }}
                            >
                                <X className="h-4 w-4" />
                            </button>
                        )}

                        {/* Cmd K badge */}
                        <kbd className="pointer-events-none inline-flex items-center gap-1 rounded bg-muted px-2 py-1 text-xs text-muted-foreground">
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
                                            {/* {highlightMatch(item.label, query)}
                                             */}
                                            {item.label}
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
