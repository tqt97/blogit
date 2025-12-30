import InputError from '@/components/input-error';
import TablePagination from '@/components/table-pagination';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardAction,
    CardContent,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { usePermissions } from '@/hooks/user-permissions';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Permission, SinglePermission } from '@/types/role_permissions';
import { Head, useForm, usePage } from '@inertiajs/react';
import { Loader2 } from 'lucide-react';
import { useEffect, useState } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permissions',
        href: '/permissions',
    },
];

export default function Permissions({
    permissions,
}: {
    permissions: Permission;
}) {
    const [openAddNewPermissionDialog, setOpenAddNewPermissionDialog] =
        useState(false);
    const [openEditPermissionDialog, setOpenEditPermissionDialog] =
        useState(false);

    const { flash } = usePage<{ flash: { message?: string; error: string } }>()
        .props;

    const { can } = usePermissions();

    useEffect(() => {
        if (!flash.message) return;

        toast.success(flash.message);

        const id = setTimeout(() => {
            setOpenAddNewPermissionDialog(false);
            setOpenEditPermissionDialog(false);
        }, 0);

        return () => clearTimeout(id);
    }, [flash.message]);

    const {
        data,
        setData,
        post,
        put,
        delete: destroy,
        processing,
        errors,
        reset,
    } = useForm({
        id: '',
        name: '',
        description: '',
    });
    function submit(e: React.FormEvent) {
        e.preventDefault();
        post('/permissions', {
            onSuccess: () => {
                reset('name');
                reset('description');
            },
        });
    }

    function edit(permission: SinglePermission) {
        setData({
            name: permission.name,
            description: permission.description,
        });
        setOpenEditPermissionDialog(true);
    }

    function update(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();
        put(`/permissions/${data.id}`, {
            onSuccess: () => {
                reset('name');
                reset('description');
            },
        });
    }

    function deletePermission(id: number) {
        destroy(`/permissions/${id}`);
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Permissions" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <Card>
                    <CardHeader className="flex items-center justify-between">
                        <CardTitle>Permissions Managements</CardTitle>
                        <CardAction>
                            {can('create_permissions') && (
                                <Button
                                    variant="default"
                                    onClick={() =>
                                        setOpenAddNewPermissionDialog(true)
                                    }
                                >
                                    Add new
                                </Button>
                            )}
                        </CardAction>
                    </CardHeader>
                    <hr />
                    <CardContent>
                        <Table>
                            <TableHeader className="bg-slate-500 dark:bg-slate-700">
                                <TableRow>
                                    <TableHead className="font-bold text-white">
                                        ID
                                    </TableHead>
                                    <TableHead className="font-bold text-white">
                                        Name
                                    </TableHead>
                                    <TableHead className="font-bold text-white">
                                        Description
                                    </TableHead>
                                    <TableHead className="font-bold text-white">
                                        Created at
                                    </TableHead>
                                    <TableHead className="font-bold text-white">
                                        Updated at
                                    </TableHead>
                                    <TableHead className="font-bold text-white">
                                        Actions
                                    </TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {permissions.data.map((permission, index) => (
                                    <TableRow
                                        key={index + 1}
                                        className="odd:bg-slate-100 dark:odd:bg-slate-800"
                                    >
                                        <TableCell>{permission.id}</TableCell>
                                        <TableCell>{permission.name}</TableCell>
                                        <TableCell>
                                            {permission.description}
                                        </TableCell>
                                        <TableCell>
                                            {permission.created_at}
                                        </TableCell>
                                        <TableCell>
                                            {permission.updated_at}
                                        </TableCell>
                                        <TableCell>
                                            {can('edit_permissions') && (
                                                <Button
                                                    variant={'outline'}
                                                    size={'sm'}
                                                    onClick={() =>
                                                        edit(permission)
                                                    }
                                                >
                                                    Edit
                                                </Button>
                                            )}
                                            {can('delete_permissions') && (
                                                <Button
                                                    className="ms-2"
                                                    variant={'destructive'}
                                                    size={'sm'}
                                                    onClick={() => {
                                                        deletePermission(
                                                            permission.id,
                                                        );
                                                    }}
                                                >
                                                    Delete
                                                </Button>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                    {permissions.data.length > 0 ? (
                        <TablePagination
                            total={permissions.total}
                            from={permissions.from}
                            to={permissions.to}
                            links={permissions.links}
                        />
                    ) : (
                        <div className="flex h-full items-center justify-center">
                            No Results Found!
                        </div>
                    )}
                </Card>
                {/* add new permission diaglog start */}
                <Dialog
                    open={openAddNewPermissionDialog}
                    onOpenChange={setOpenAddNewPermissionDialog}
                >
                    <form
                        onSubmit={(e) => {
                            e.preventDefault();
                        }}
                    >
                        <DialogContent className="sm:max-w-[425px]">
                            <DialogHeader>
                                <DialogTitle>Add New Permissions</DialogTitle>
                            </DialogHeader>
                            <div className="grid gap-4">
                                <div className="grid gap-3">
                                    <Label htmlFor="name">
                                        Permission Name
                                    </Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        placeholder="Permission Name"
                                        value={data.name}
                                        onChange={(e) =>
                                            setData('name', e.target.value)
                                        }
                                        aria-invalid={!!errors.name}
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="grid gap-3">
                                    <Label htmlFor="description">
                                        Description
                                    </Label>
                                    <Input
                                        id="description"
                                        name="description"
                                        placeholder="Description"
                                        value={data.description}
                                        onChange={(e) =>
                                            setData(
                                                'description',
                                                e.target.value,
                                            )
                                        }
                                        aria-invalid={!!errors.description}
                                    />
                                    <InputError message={errors.description} />
                                </div>
                            </div>
                            <DialogFooter>
                                <DialogClose asChild>
                                    <Button variant="outline">Cancel</Button>
                                </DialogClose>
                                <Button
                                    type="submit"
                                    onClick={submit}
                                    disabled={processing}
                                >
                                    {processing && (
                                        <Loader2 className="animate-spin" />
                                    )}
                                    Submit
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </form>
                </Dialog>
                {/* add new permission diaglog end */}

                <Dialog
                    open={openEditPermissionDialog}
                    onOpenChange={setOpenEditPermissionDialog}
                >
                    <DialogContent className="sm:max-w-[425px]">
                        <DialogHeader>
                            <DialogTitle>Edit Permissions</DialogTitle>
                        </DialogHeader>
                        <hr />
                        <form onSubmit={update}>
                            <div className="grid gap-4">
                                <div className="grid gap-3">
                                    <Label htmlFor="name">
                                        Permission Name
                                    </Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        placeholder="Permission Name"
                                        value={data.name}
                                        onChange={(e) =>
                                            setData('name', e.target.value)
                                        }
                                        aria-invalid={!!errors.name}
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="grid gap-3">
                                    <Label htmlFor="description">
                                        Description
                                    </Label>
                                    <Input
                                        id="description"
                                        name="description"
                                        placeholder="Description"
                                        value={data.description}
                                        onChange={(e) =>
                                            setData(
                                                'description',
                                                e.target.value,
                                            )
                                        }
                                        aria-invalid={!!errors.description}
                                    />
                                    <InputError message={errors.description} />
                                </div>
                            </div>
                        </form>
                        <DialogFooter>
                            <DialogClose asChild>
                                <Button variant="outline">Cancel</Button>
                            </DialogClose>
                            <Button
                                type="submit"
                                onClick={submit}
                                disabled={processing}
                            >
                                {processing && (
                                    <Loader2 className="animate-spin" />
                                )}
                                Submit
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>
            </div>
        </AppLayout>
    );
}
