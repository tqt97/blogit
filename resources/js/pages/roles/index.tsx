import TablePagination from '@/components/table-pagination';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
	Card,
	CardAction,
	CardContent,
	CardHeader,
	CardTitle,
} from '@/components/ui/card';
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
import { Role } from '@/types/role_permissions';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useEffect } from 'react';
import { toast } from 'sonner';

const breadcrumbs: BreadcrumbItem[] = [
	{
		title: 'Roles',
		href: '/roles',
	},
];

function deleteRole(id: number) {
	if (confirm('Are you sure you want to delete this role?')) {
		router.delete(`/roles/${id}`);
	}
}

export default function Roles({ roles }: { roles: Role }) {
	const { flash } = usePage<{ flash: { message?: string; error: string } }>()
		.props;

	const { can } = usePermissions();

	useEffect(() => {
		if (flash.message) {
			toast.success(flash.message);
		}
	}, [flash.message]);

	return (
		<AppLayout breadcrumbs={breadcrumbs}>
			<Head title="Roles" />
			<div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
				<Card>
					<CardHeader className="flex items-center justify-between">
						<CardTitle>Roles Managements</CardTitle>
						<CardAction>
							{can('create_roles') && (
								<Link href={'/roles/create'}>
									<Button variant={'default'}>Add New</Button>
								</Link>
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
										Permissions
									</TableHead>
									<TableHead className="font-bold text-white">
										Actions
									</TableHead>
								</TableRow>
							</TableHeader>
							<TableBody>
								{roles.data.map((role, index) => (
									<TableRow
										key={index + 1}
										className="odd:bg-slate-100 dark:odd:bg-slate-800"
									>
										<TableCell>{role.id}</TableCell>
										<TableCell>{role.name}</TableCell>
										<TableCell>
											{role.description}
										</TableCell>
										<TableCell className="flex flex-wrap items-center gap-2">
											{role.permissions.map(
												(permission, idx) => (
													<Badge
														key={idx}
														variant={'outline'}
														className="me-1"
													>
														{permission.name}
													</Badge>
												),
											)}
										</TableCell>
										<TableCell>
											{can('edit_roles') && (
												<Link
													href={`/roles/${role.id}/edit`}
												>
													<Button
														variant={'outline'}
														size={'sm'}
													>
														Edit
													</Button>
												</Link>
											)}
											{can('delete_roles') && (
												<Button
													className="ms-2"
													variant={'destructive'}
													size={'sm'}
													onClick={() =>
														deleteRole(role.id)
													}
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
					{roles.data.length > 0 ? (
						<TablePagination
							total={roles.total}
							from={roles.from}
							to={roles.to}
							links={roles.links}
						/>
					) : (
						<div className="flex h-full items-center justify-center">
							No Results Found!
						</div>
					)}
				</Card>
			</div>
		</AppLayout>
	);
}
