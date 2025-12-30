import FormDialog from '@/components/dialogs/FormDialog';
import { CategoryFormData, FlatCategory } from '@/types/category';
import { useForm, usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import CategoryForm from './CategoryForm';

interface CreateCategoryDialogProps {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    flatCategories: FlatCategory[];
}

export default function CreateCategoryDialog({
    open,
    onOpenChange,
    flatCategories,
}: CreateCategoryDialogProps) {
    const form = useForm<CategoryFormData>({
        name: '',
        description: '',
        parent_id: null,
    });
    const { post, processing, reset } = form;
    const { flash } = usePage<{ flash: { message?: string; error: string } }>()
        .props;

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        post('/categories', {
            onSuccess: () => {
                toast.success(flash.message);
                onOpenChange(false);
                reset();
            },
            onError: (err) => {
                toast.error(flash.message);
                console.error('Validation errors:', err);
            },
        });
    };

    return (
        <FormDialog
            open={open}
            onOpenChange={onOpenChange}
            title="Create Category"
            onSubmit={handleSubmit}
            isSubmitting={processing}
        >
            <CategoryForm form={form} flatCategories={flatCategories} />
        </FormDialog>
    );
}
