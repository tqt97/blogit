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
import { SinglePermission } from '@/types/role_permissions';
import { Head, Link, useForm } from '@inertiajs/react';

const breadcrumbs: BreadcrumbItem[] = [
	{
		title: 'Create Roles',
		href: '/roles/create',
	},
];

export default function CreateRoles({
	permissions,
}: {
	permissions: SinglePermission[];
}) {
	const { data, setData, post, processing, errors } = useForm({
		name: '',
		description: '',
		permissions: [] as string[],
	});

	function submit(e: React.FormEvent<HTMLFormElement>) {
		e.preventDefault();
		post('/roles');
	}

	return (
		<AppLayout breadcrumbs={breadcrumbs}>
			<Head title="Roles" />
			<div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
				<Card>
					<CardHeader className="flex items-center justify-between">
						<CardTitle>Create Role</CardTitle>
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
									{permissions.map((permission) => (
										<div
											key={permission.id}
											className="flex items-center gap-3"
										>
											<Checkbox
												id={permission.name}
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
									Create
								</Button>
							</div>
						</form>
					</CardContent>
				</Card>
			</div>
		</AppLayout>
	);
}
