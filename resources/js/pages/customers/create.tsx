import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle } from 'lucide-react';
import { type FormEvent } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/customers' },
    { title: 'Add customer', href: '/customers/create' },
];

export default function CreateCustomer() {
    const { data, setData, post, processing, errors } = useForm({ name: '', phone: '', email: '', address: '' });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        post('/customers');
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Add customer" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <div>
                    <Button variant="ghost" size="sm" asChild className="-ml-3 mb-3"><Link href="/customers"><ArrowLeft /> Back to customers</Link></Button>
                    <h1 className="text-2xl font-semibold tracking-tight">Add customer</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Create a customer profile for device and repair tracking.</p>
                </div>
                <Card className="max-w-2xl">
                    <CardHeader><CardTitle className="text-base">Contact details</CardTitle></CardHeader>
                    <CardContent>
                        <form onSubmit={submit} className="grid gap-5">
                            <Field id="name" label="Full name" value={data.name} error={errors.name} onChange={(value) => setData('name', value)} required placeholder="Customer name" />
                            <div className="grid gap-5 sm:grid-cols-2">
                                <Field id="phone" label="Phone" type="tel" value={data.phone} error={errors.phone} onChange={(value) => setData('phone', value)} placeholder="+1 555 0100" />
                                <Field id="email" label="Email" type="email" value={data.email} error={errors.email} onChange={(value) => setData('email', value)} placeholder="name@example.com" />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="address">Address</Label>
                                <textarea id="address" maxLength={2000} value={data.address} onChange={(event) => setData('address', event.target.value)} placeholder="Optional mailing address" className="min-h-24 rounded-md border border-input bg-background px-3 py-2 text-sm outline-none placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring" />
                                <InputError message={errors.address} />
                            </div>
                            <div className="flex gap-2">
                                <Button type="submit" disabled={processing}>{processing && <LoaderCircle className="animate-spin" />} Save customer</Button>
                                <Button type="button" variant="outline" asChild><Link href="/customers">Cancel</Link></Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}

function Field({ id, label, type = 'text', value, error, onChange, required, placeholder }: { id: string; label: string; type?: string; value: string; error?: string; onChange: (value: string) => void; required?: boolean; placeholder?: string }) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>{label}</Label>
            <Input id={id} type={type} required={required} value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} />
            <InputError message={error} />
        </div>
    );
}
