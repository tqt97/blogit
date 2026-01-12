export const PER_PAGE_OPTIONS = [10, 15, 25, 50, 100] as const;

export type PerPageOption = (typeof PER_PAGE_OPTIONS)[number];

export const DEFAULT_PER_PAGE: PerPageOption = 15;
