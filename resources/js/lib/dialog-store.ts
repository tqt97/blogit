import { create } from 'zustand';

export interface ConfirmOptions {
	title: string;
	message?: string;
	confirmText?: string;
	cancelText?: string;
}

interface DialogState {
	isOpen: boolean;
	options: ConfirmOptions | null;
	resolve: ((value: boolean) => void) | null;

	open: (options: ConfirmOptions) => Promise<boolean>;
	close: (result: boolean) => void;
}

export const useDialogStore = create<DialogState>((set, get) => ({
	isOpen: false,
	options: null,
	resolve: null,

	open: (options) => {
		return new Promise<boolean>((resolve) => {
			set({ isOpen: true, options, resolve });
		});
	},

	close: (result) => {
		const resolveFn = get().resolve;
		if (resolveFn) resolveFn(result);
		set({ isOpen: false, options: null, resolve: null });
	},
}));
