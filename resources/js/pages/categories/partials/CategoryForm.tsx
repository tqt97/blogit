import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { CategoryFormProps } from '@/types/category';
import { CornerDownRight } from 'lucide-react';

export default function CategoryForm({
    form: { data, setData, errors },
    flatCategories,
}: CategoryFormProps) {
    return (
        <div className="space-y-4 py-4">
            {/* SELECT PARENT */}
            <div className="grid gap-3">
                <Label htmlFor="parent">Parent Category</Label>

                <Select
                    value={
                        data.parent_id === null
                            ? 'root'
                            : String(data.parent_id)
                    }
                    onValueChange={(val) =>
                        setData(
                            'parent_id',
                            val === 'root' ? null : Number(val),
                        )
                    }
                >
                    <SelectTrigger id="parent">
                        <SelectValue placeholder="Select parent category">
                            {data.parent_id === null
                                ? '📂 Create Root Category'
                                : flatCategories.find(
                                      (c) => c.id === data.parent_id,
                                  )?.name}
                        </SelectValue>{' '}
                    </SelectTrigger>

                    <SelectContent className="max-h-[300px]">
                        <SelectGroup>
                            <SelectLabel>Root</SelectLabel>
                            <SelectItem value="root">
                                📂 Create Root Category
                            </SelectItem>
                        </SelectGroup>

                        <SelectGroup>
                            <SelectLabel>Existing Categories</SelectLabel>
                            {flatCategories.map((cat) => (
                                <SelectItem key={cat.id} value={String(cat.id)}>
                                    <div
                                        className="flex items-center"
                                        style={{
                                            marginLeft: `${(cat.level - 1) * 20}px`,
                                        }}
                                    >
                                        {cat.level > 1 && (
                                            <CornerDownRight className="mr-2 h-4 w-4" />
                                        )}
                                        <span>{cat.name}</span>
                                    </div>
                                </SelectItem>
                            ))}
                        </SelectGroup>
                    </SelectContent>
                </Select>

                {errors.parent_id && (
                    <p className="text-sm text-red-500">{errors.parent_id}</p>
                )}
            </div>

            {/* NAME */}
            <div className="grid gap-3">
                <Label htmlFor="name">Name</Label>
                <Input
                    id="name"
                    value={data.name}
                    onChange={(e) => setData('name', e.target.value)}
                    placeholder="Enter category name"
                />
                {errors.name && (
                    <p className="text-sm text-red-500">{errors.name}</p>
                )}
            </div>

            {/* DESCRIPTION */}
            <div className="grid gap-3">
                <Label htmlFor="description">Description</Label>
                <Textarea
                    placeholder="Enter description"
                    value={data.description}
                    id="description"
                    onChange={(e) => setData('description', e.target.value)}
                />
                {errors.description && (
                    <p className="text-sm text-red-500">{errors.description}</p>
                )}
            </div>
        </div>
    );
}
