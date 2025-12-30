export interface Links {
    url: string | null | undefined;
    label: string;
    active: boolean;
}

export interface Pagination {
    from: number | null;
    links: Links[];
    to: number | null;
    total: number;
}
