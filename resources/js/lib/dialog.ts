import { useDialogStore } from "./dialog-store";

export function confirm(options: {
	title: string;
	message?: string;
	confirmText?: string;
	cancelText?: string;
}) {
	return useDialogStore.getState().open(options);
}
