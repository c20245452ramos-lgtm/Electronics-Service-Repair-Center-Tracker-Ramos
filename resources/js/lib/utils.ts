import { type ClassValue, clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function capitalizeFirstLetter(value: string): string {
    return value ? value[0].toUpperCase() + value.slice(1) : value;
}

export function sanitizePhoneNumber(value: string): string {
    return value.replace(/\D/g, '').slice(0, 11);
}

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}
