export const isoToDate = (iso?: string | null) =>
    iso ? iso.slice(0, 10) : '-';

export const isoToDateTime = (iso?: string | null) =>
    iso ? iso.replace('T', ' ').slice(0, 19) : '-';

export function formatIso(iso?: string | null, withTime = false): string {
    if (!iso) return '-';

    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '-';

    const pad = (n: number) => String(n).padStart(2, '0');

    const y = d.getFullYear();
    const m = pad(d.getMonth() + 1);
    const day = pad(d.getDate());

    if (!withTime) return `${y}-${m}-${day}`;

    const h = pad(d.getHours());
    const min = pad(d.getMinutes());
    const s = pad(d.getSeconds());

    return `${y}-${m}-${day} ${h}:${min}:${s}`;
}

export function diffToHuman(iso?: string | null): string {
    if (!iso) return '-';

    const diff = Date.now() - new Date(iso).getTime();
    if (Number.isNaN(diff)) return '-';

    const sec = Math.floor(diff / 1000);
    if (sec < 60) return 'just now';

    const min = Math.floor(sec / 60);
    if (min < 60) return `${min} minute(s) ago`;

    const hour = Math.floor(min / 60);
    if (hour < 24) return `${hour} hour(s) ago`;

    const day = Math.floor(hour / 24);
    return `${day} day(s) ago`;
}
