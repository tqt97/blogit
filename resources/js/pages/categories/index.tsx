import { Button } from '@/components/ui/button';

import {
    Card,
    CardAction,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import AppLayout from '@/layouts/app-layout';
import { flattenCategories } from '@/lib/category-utils';

import TablePagination from '@/components/table-pagination';
import { BreadcrumbItem } from '@/types';
import { Category, SingleCategory } from '@/types/category';
import { Head, usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';
import CategoriesTable from './partials/CategoriesTable';
import CreateCategoryDialog from './partials/CreateCategoryDialog';
import EditCategoryDialog from './partials/EditCategoryDialog';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Category',
        href: '/categories',
    },
];

export default function CategoryPage() {
    const { categories: paginatedCategories } = usePage<{
        categories: Category;
    }>().props;

    const { data: categories } = paginatedCategories;

    const [showCreateDialog, setShowCreateDialog] = useState(false);
    const [showEditDialog, setShowEditDialog] = useState(false);
    const [editingCategory, setEditingCategory] =
        useState<SingleCategory | null>(null);

    const flatCategories = useMemo(() => {
        if (!Array.isArray(categories)) return [];
        return flattenCategories(categories);
    }, [categories]);

    const handleEdit = (category: SingleCategory) => {
        setEditingCategory(category);
        setShowEditDialog(true);
    };

    const handleCloseEditDialog = (open: boolean) => {
        if (!open) {
            setEditingCategory(null);
        }
        setShowEditDialog(open);
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <Head title="Category" />

                <Card>
                    <CardHeader className="flex flex-row items-center justify-between">
                        <CardTitle>Category Management</CardTitle>
                        <CardAction>
                            <Button onClick={() => setShowCreateDialog(true)}>
                                Add new
                            </Button>
                        </CardAction>
                    </CardHeader>
                    <hr />
                    <CardContent>
                        <CategoriesTable
                            categories={categories}
                            onEdit={handleEdit}
                        />
                        <TablePagination
                            // total={paginatedCategories.total}
                            // from={paginatedCategories.from}
                            // to={paginatedCategories.to}
                            links={paginatedCategories.links}
                        />
                    </CardContent>
                </Card>

                <CreateCategoryDialog
                    open={showCreateDialog}
                    onOpenChange={setShowCreateDialog}
                    flatCategories={flatCategories}
                />

                <EditCategoryDialog
                    open={showEditDialog}
                    onOpenChange={handleCloseEditDialog}
                    category={editingCategory}
                    flatCategories={flatCategories}
                />
            </div>
        </AppLayout>
    );
}
