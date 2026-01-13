export type SortDirection =
    (typeof SORT_DIRECTION)[keyof typeof SORT_DIRECTION];
export const SORT_DIRECTION = { ASC: 'asc', DESC: 'desc' } as const;

export const DEFAULT_SORT_DIRECTION: SortDirection = 'desc';
