export interface Links {
    url: string | null | undefined;
    label: string;
    active: boolean;
}

export interface Pagination {
    links: Links[];
    // from: number | null;
    // to: number | null;
    // total: number;
}
