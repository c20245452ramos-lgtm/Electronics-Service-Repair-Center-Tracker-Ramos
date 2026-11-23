import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { capitalizeFirstLetter, sanitizePhoneNumber } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle } from 'lucide-react';
import { type FormEvent } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Technicians', href: '/technicians' },
    { title: 'Add technician', href: '/technicians/create' },
];

export default function CreateTechnician() {
    const { data, setData, post, processing, errors } = useForm({ name: '', specialty: '', phone: '', email: '' });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        post('/technicians');
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Add technician" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <div>
                    <Button variant="ghost" size="sm" asChild className="-ml-3 mb-3"><Link href="/technicians"><ArrowLeft /> Back to technicians</Link></Button>
                    <h1 className="text-2xl font-semibold tracking-tight">Add technician</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Add a technician to the service center team.</p>
                </div>
                <Card className="max-w-2xl">
                    <CardHeader><CardTitle className="text-base">Technician details</CardTitle></CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="grid gap-5">
                            <Field id="name" label="Full name" value={data.name} error={errors.name} maxLength={20} onChange={(value) => setData('name', capitalizeFirstLetter(value))} required placeholder="Technician name" />
                            <Field id="specialty" label="Specialty" value={data.specialty} error={errors.specialty} maxLength={20} onChange={(value) => setData('specialty', capitalizeFirstLetter(value.replace(/[0-9]/g, '').slice(0, 20)))} placeholder="Laptop repair, mobile devices..." />
                            <div className="grid gap-5 sm:grid-cols-2">
                                <Field id="phone" label="Phone" type="tel" value={data.phone} error={errors.phone} maxLength={11} inputMode="numeric" onChange={(value) => setData('phone', sanitizePhoneNumber(value))} placeholder="09123456789" />
                                <Field id="email" label="Email" type="email" value={data.email} error={errors.email} maxLength={30} onChange={(value) => setData('email', value)} placeholder="name@example.com" />
                            </div>
                            <div className="flex gap-2">
                                <Button type="submit" disabled={processing}>{processing && <LoaderCircle className="animate-spin" />} Save technician</Button>
                                <Button type="button" variant="outline" asChild><Link href="/technicians">Cancel</Link></Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}

function Field({ id, label, type = 'text', value, error, onChange, required, placeholder, maxLength, inputMode }: { id: string; label: string; type?: string; value: string; error?: string; onChange: (value: string) => void; required?: boolean; placeholder?: string; maxLength?: number; inputMode?: 'numeric' }) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>{label}</Label>
            <Input id={id} type={type} required={required} maxLength={maxLength} inputMode={inputMode} pattern={inputMode === 'numeric' ? '[0-9]*' : undefined} value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} />
            <InputError message={error} />
        </div>
    );
}
