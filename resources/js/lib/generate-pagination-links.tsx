import {
    PaginationEllipsis,
    PaginationItem,
    PaginationLink,
} from '@/components/ui/pagination';
import { JSX } from 'react';

type PaginationLink = {
    url: string;
    label: string;
};

type PaginationLinkType = {
    url: string | null;
    label: string;
    active: boolean;
};

export const generatePaginationLinks = (
    currentPage: number,
    totalPages: number,
    links: PaginationLinkType[],
) => {
    const pages: JSX.Element[] = [];

    const getLink = (page: number) =>
        links.find((l) => l.label === String(page));

    if (totalPages <= 6) {
        for (let i = 1; i <= totalPages; i++) {
            const link = getLink(i);
            if (!link?.url) continue;

            pages.push(
                <PaginationItem key={i}>
                    <PaginationLink
                        href={link.url}
                        isActive={i === currentPage}
                    >
                        {i}
                    </PaginationLink>
                </PaginationItem>,
            );
        }
    } else {
        // 1 2
        [1, 2].forEach((i) => {
            const link = getLink(i);
            if (!link?.url) return;

            pages.push(
                <PaginationItem key={i}>
                    <PaginationLink
                        href={link.url}
                        isActive={i === currentPage}
                    >
                        {i}
                    </PaginationLink>
                </PaginationItem>,
            );
        });

        if (currentPage > 2 && currentPage < totalPages - 1) {
            pages.push(<PaginationEllipsis key="ellipsis-start" />);
            pages.push(
                <PaginationItem key={currentPage}>
                    <PaginationLink isActive>{currentPage}</PaginationLink>
                </PaginationItem>,
            );
        }

        pages.push(<PaginationEllipsis key="ellipsis-end" />);

        [totalPages - 1, totalPages].forEach((i) => {
            const link = getLink(i);
            if (!link?.url) return;

            pages.push(
                <PaginationItem key={i}>
                    <PaginationLink
                        href={link.url}
                        isActive={i === currentPage}
                    >
                        {i}
                    </PaginationLink>
                </PaginationItem>,
            );
        });
    }

    return pages;
};
