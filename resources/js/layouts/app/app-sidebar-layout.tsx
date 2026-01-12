import { AppContent } from '@/components/app-content';
import { AppShell } from '@/components/app-shell';
import { AppSidebar } from '@/components/app-sidebar';
import { AppSidebarHeader } from '@/components/app-sidebar-header';
import { Toaster } from '@/components/ui/sonner';
import { useFlashToast } from '@/hooks/use-flash-toast';
import { type BreadcrumbItem } from '@/types';
import {
    CircleCheckIcon,
    InfoIcon,
    Loader2Icon,
    OctagonXIcon,
    TriangleAlertIcon,
} from 'lucide-react';
import { type PropsWithChildren } from 'react';

export default function AppSidebarLayout({
    children,
    breadcrumbs = [],
}: PropsWithChildren<{ breadcrumbs?: BreadcrumbItem[] }>) {
    useFlashToast();

    return (
        <AppShell variant="sidebar">
            <AppSidebar />
            <AppContent variant="sidebar" className="overflow-x-hidden">
                <AppSidebarHeader breadcrumbs={breadcrumbs} />
                {children}
                <Toaster
                    position="top-right"
                    richColors
                    duration={3500}
                    visibleToasts={2}
                    icons={{
                        success: (
                            <CircleCheckIcon className="size-4 text-emerald-600" />
                        ),
                        info: <InfoIcon className="size-4 text-blue-600" />,
                        warning: (
                            <TriangleAlertIcon className="size-4 text-amber-600" />
                        ),
                        error: <OctagonXIcon className="size-4 text-red-600" />,
                        loading: (
                            <Loader2Icon className="size-4 animate-spin text-muted-foreground" />
                        ),
                    }}
                />
            </AppContent>
        </AppShell>
    );
}
