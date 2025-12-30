import { FlatCategory, SingleCategory } from '@/types/category';

export const flattenCategories = (
    categories: SingleCategory[],
    level: number = 1,
): FlatCategory[] => {
    let result: FlatCategory[] = [];
    categories.forEach((cat) => {
        result.push({ ...cat, level });
        if (cat.children_recursive && cat.children_recursive.length > 0) {
            result = result.concat(
                flattenCategories(cat.children_recursive, level + 1),
            );
        }
    });
    return result;
};
