import { Pagination } from './pagination';

export interface SinglePermission {
	id: number;
	name: string;
	description?: string;
	created_at: string;
	updated_at: string;
}

export interface SingleRole {
	id: number;
	name: string;
	permissions: SinglePermission[];
	description?: string;
	created_at: string;
	updated_at: string;
}

export interface RolePermission {
	id: number;
	name: string;
	permissions: SinglePermission[];
	description?: string;
	created_at: string;
	updated_at: string;
}


export interface Permission extends Pagination {
	data: SinglePermission[];
}

export interface Role extends Pagination {
	data: SingleRole[];
}
