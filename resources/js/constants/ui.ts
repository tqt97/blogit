export const EMPTY_TEXT = 'No results found.';
export const LOADING_TEXT = 'Loading...';
export const CONFIRM_TEXT = 'Confirm';

export const ACTION_TEXT = {
    action_delete: 'Delete',
    action_restore: 'Restore',
    action_permanent_delete: 'Permanent delete',
    action_cancel: 'Cancel',
    action_ok: 'Ok',
    action_close: 'Close',
    action_save: 'Save',
    action_create: 'Create',
    action_edit: 'Edit',
    action_update: 'Update',
} as const;

export const DESCRIPTION = {
    delete: 'Delete this item. You can restore it later.',
    restore: 'This action cannot be undone. This will restore this item.',
    permanentDelete:
        'This action cannot be undone. This will permanently delete this item.',
};
