import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export type Tag = {
    id: number;
    name: string;
    slug: string;
    created_at?: string;
    updated_at?: string;
};

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

export type Paginated<T> = {
    data: T[];
    links: PaginationLink[];
    meta?: {
        current_page: number;
        from: number | null;
        last_page: number;
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
};

export type TagIndexFilters = {
    search?: string;
    per_page?: number;
    sort?: string;
    direction?: 'asc' | 'desc';
    page?: number;
};

export interface PaginatedResponse<T = unknown | null> {
    current_page: number;
    data: T[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export type TagIndexProps = { tags: Paginated<Tag>; filters: TagIndexFilters };

export type ConfirmDialogProps = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    title: React.ReactNode;
    description?: React.ReactNode;
    confirmText?: string;
    cancelText?: string;
    onConfirm: () => void;
    confirmDisabled?: boolean;
    variant?: 'default' | 'destructive' | 'outline' | 'secondary' | 'ghost';
};

export type Flash = {
    id?: string;
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
};

export type RouteForm = {
    action: string;
    method: 'post' | 'put' | 'patch' | 'delete' | 'get';
};

export type FormTagData = {
    name: string;
    slug: string;
    intent: 'default' | 'create_and_continue';
};

export type ListFilters = {
    search?: string | null;
    sort?: string | null;
    direction?: 'asc' | 'desc' | string | null;
    per_page?: number | string | null;
    page?: number | string | null;
};

export type ListPageShellProps = {
    title: string;
    headTitle?: string;
    breadcrumbs?: BreadcrumbItem[];
    createHref?: string;
    createText?: string;
    createIcon?: React.ReactNode;
    toolbar?: React.ReactNode;
    table: React.ReactNode;
    pagination?: React.ReactNode;
    dialogs?: React.ReactNode;
};

export type ListToolbarProps = {
    bulkMenuOpen: boolean;
    setBulkMenuOpen: (v: boolean) => void;
    hasSelection: boolean;
    selectedCount: number;
    onClearSelection: () => void;
    onBulkDeleteClick: () => void;
    searchKey: string;
    defaultSearch: string;
    searchInput: string;
    onSearchChange: (value: string) => void;
    onSearchKeyDown: (e: React.KeyboardEvent<HTMLInputElement>) => void;
    onClearSearch: () => void;
    placeholder?: string;
    perPage: number;
    perPageOptions?: number[];
    onPerPageChange: (value: number) => void;
    rightSlot?: React.ReactNode;
    loading?: boolean;
};

export type SortColumnHeaderProps<TData, TValue> = {
    column: Column<TData, TValue>;
    title: string;
};

export type InertiaFormProps<TData> = {
    data: TData;
    setData: <K extends keyof TData>(key: K, value: TData[K]) => void;
    processing: boolean;
    errors: Partial<Record<keyof TData, string>>;
    hasErrors: boolean;
    isDirty: boolean;
    clearErrors: () => void;
    reset: (...fields: (keyof TData)[]) => void;
    submit: (
        method: RouteForm['method'],
        url: string,
        options?: {
            preserveScroll?: boolean;
            onStart?: () => void;
            onSuccess?: () => void;
            onFinish?: () => void;
        },
    ) => void;
};

export type KeysExceptIntent<T> = Exclude<keyof T, 'intent'>;

export type SubmitIntent = 'default' | 'create_and_continue';

export type CrudFormShellProps<TData extends { intent: SubmitIntent }> = {
    mode: 'create' | 'edit';
    title: string;
    submitText: string;
    form: RouteForm;
    cancelHref: string;
    warnOnLeave?: boolean;
    resetOnSuccess?: KeysExceptIntent<TData>[];
    inertia: InertiaFormProps<TData>;
    children: React.ReactNode;
    onSuccessIntent?: (intent: SubmitIntent) => void;
    onResetExtras?: () => void;
    headerRight?: React.ReactNode;
};

export type DataTableState = {
    sorting: SortingState;
    rowSelection: RowSelectionState;
};

export type DataTableProps<TData> = {
    data: TData[];
    columns: ColumnDef<TData>[];
    emptyText?: string;
    loading?: boolean;
    loadingText?: string;
    manualSorting?: boolean;
    state: Partial<DataTableState>;
    onSortingChange?: OnChangeFn<SortingState>;
    onRowSelectionChange?: OnChangeFn<RowSelectionState>;
    onTableReady?: (table: Table<TData>) => void;
    getRowId?: (originalRow: TData, index: number) => string;
};
