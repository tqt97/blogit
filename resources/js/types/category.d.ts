import { Pagination } from './pagination';

export interface SingleCategory {
	id: number;
	name: string;
	slug: string;
	description: string;
	parent_id: number | null;
	created_at: string;
	updated_at: string;
	children_recursive?: SingleCategory[];
}

export interface Category extends Pagination {
	data: SingleCategory[];
}

export interface FlatCategory extends SingleCategory {
	level: number;
}

export interface CategoryFormData {
	name: string;
	description: string;
	parent_id: number | null;
}

export interface CategoryFormProps {
	form: {
		data: CategoryFormData;
		setData: (
			key: keyof CategoryFormData,
			value: string | number | null,
		) => void;
		errors: Partial<Record<keyof CategoryFormData, string>>;
	};
	flatCategories: FlatCategory[];
}

export interface EditCategoryDialogProps {
	open: boolean;
	onOpenChange: (open: boolean) => void;
	category: SingleCategory | null;
	flatCategories: FlatCategory[];
}
