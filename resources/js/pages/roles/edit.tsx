import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
	Card,
	CardAction,
	CardContent,
	CardHeader,
	CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { RolePermission, SinglePermission} from '@/types/role_permissions';
import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
	{
		title: 'Edit Roles',
		href: '/roles',
	},
];

export default function EditRoles({
	permissions, role
}: {
	permissions: SinglePermission[];
	role: RolePermission;
}) {
	const { flash } = usePage<{ flash: { message?: string; error?: string } }>().props;

	const permissionList = role.permissions.map((perm) => perm.name);

	const { data, setData, put, processing, errors } = useForm({
		name: role.name,
		description: role.description || '',
		permissions: permissionList,
	});

	useEffect(() => {
		if (flash.message) {
			toast.success(flash.message);
		}
		if (flash.error) {
			toast.error(flash.error);
		}
	}, [flash.message, flash.error]);

	function submit(e: React.FormEvent<HTMLFormElement>) {
		e.preventDefault();
		put(`/roles/${role.id}`, {
			onSuccess: () => {
				toast.success('Role updated successfully');
			},
			onError: (errors) => {
				console.error('Update errors:', errors);
				toast.error('Failed to update role');
			}
		});
	}

	return (
		<AppLayout breadcrumbs={breadcrumbs}>
			<Head title="Roles" />
			<div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
				<Card>
					<CardHeader className="flex items-center justify-between">
						<CardTitle>Edit Role</CardTitle>
						<CardAction>
							<Link href={'/roles'}>
								<Button variant="default">Go back</Button>
							</Link>
						</CardAction>
					</CardHeader>
					<hr />
					<CardContent>
						<form onSubmit={submit}>
							<div className="mb-4">
								<Label htmlFor="name">Role Name</Label>
								<Input
									id="name"
									type="text"
									value={data.name}
									onChange={(e) =>
										setData('name', e.target.value)
									}
									aria-invalid={!!errors.name}
								/>
								<InputError
									message={errors.name}
									className="mt-2"
								/>
							</div>
							<div className="mb-4">
								<Label htmlFor="description">Description</Label>
								<Input
									id="description"
									type="text"
									value={data.description}
									onChange={(e) =>
										setData('description', e.target.value)
									}
									aria-invalid={!!errors.description}
								/>
								<InputError
									message={errors.description}
									className="mt-2"
								/>
							</div>

							<Label htmlFor="permissions">
								Select Permissions
							</Label>
							<div className="my-4">
								<div className="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5">
									{permissions.map((permission) =>  (
										<div
											key={permission.id}
											className="flex items-center gap-3"
										>
											<Checkbox
												id={permission.name}
												checked={data.permissions.includes(permission.name)}
												onCheckedChange={(checked) => {
													if (checked) {
														setData('permissions', [
															...data.permissions,
															permission.name,
														]);
													} else {
														setData(
															'permissions',
															data.permissions.filter(
																(p) =>
																	p !==
																	permission.name,
															),
														);
													}
												}}
											/>
											<Label htmlFor={permission.name}>
												{permission.name}
											</Label>
										</div>
									))}
								</div>
							</div>
							<div className="flex justify-end">
								<Button
									size={'lg'}
									type="submit"
									disabled={processing}
								>
									Update
								</Button>
							</div>
						</form>
					</CardContent>
				</Card>
			</div>
		</AppLayout>
	);
}
