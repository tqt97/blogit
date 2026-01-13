import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { BookOpen, Boxes, FileText, Folder, LayoutGrid, MessageSquareCodeIcon, Newspaper, Tag } from 'lucide-react';
import AppLogo from './app-logo';
import { NavCollapse } from './nav-collapse';

const singleNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
        isActive: false,
    },
];

const blogNavItems: NavItem[] = [
    {
        title: 'Blogs',
        href: '#',
        icon: Newspaper,
        isActive: true,
        items: [
            {
                title: 'Tags',
                href: '/admin/tags',
                icon: Tag,
            },
            {
                title: 'Categories',
                href: '/admin/categories',
                icon: Boxes,
            },
            {
                title: 'Comments',
                href: '/admin/comments',
                icon: MessageSquareCodeIcon,
            },
            {
                title: 'Posts',
                href: '/admin/posts',
                icon: FileText,
            },
        ],
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={singleNavItems} />
                <NavCollapse items={blogNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
