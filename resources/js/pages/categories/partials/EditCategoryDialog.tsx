import FormDialog from '@/components/dialogs/FormDialog';
import { CategoryFormData, EditCategoryDialogProps } from '@/types/category';
import { useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import CategoryForm from './CategoryForm';

export default function EditCategoryDialog({
    open,
    onOpenChange,
    category,
    flatCategories,
}: EditCategoryDialogProps) {
    const form = useForm<CategoryFormData>({
        name: '',
        description: '',
        parent_id: null,
    });
    const { setData, put, processing, reset } = form;

    useEffect(() => {
        if (category) {
            setData({
                name: category.name,
                description: category.description,
                parent_id: category.parent_id,
            });
        } else {
            reset();
        }
    }, [category, setData, reset]);

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (!category) return;

        put(`/category/${category.id}`, {
            onSuccess: () => {
                onOpenChange(false);
            },
            onError: (err) => {
                console.error('Validation errors:', err);
            },
        });
    };

    return (
        <FormDialog
            open={open}
            onOpenChange={onOpenChange}
            title="Edit Category"
            onSubmit={handleSubmit}
            isSubmitting={processing}
        >
            <CategoryForm
                form={form}
                flatCategories={flatCategories.filter(
                    (c) => c.id !== category?.id,
                )}
            />
        </FormDialog>
    );
}
