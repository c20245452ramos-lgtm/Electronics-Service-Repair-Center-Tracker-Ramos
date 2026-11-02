import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/react';
import { ArrowLeft, LoaderCircle } from 'lucide-react';
import { type FormEvent } from 'react';

type Customer = { id: number; name: string };

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Devices', href: '/devices' },
    { title: 'Register device', href: '/devices/create' },
];

export default function CreateDevice({ customers }: { customers: Customer[] }) {
    const { data, setData, post, processing, errors } = useForm({
        customer_id: '',
        brand: '',
        model: '',
        device_type: '',
        serial_number: '',
        purchase_date: '',
    });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        post('/devices');
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Register device" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <div>
                    <Button variant="ghost" size="sm" asChild className="-ml-3 mb-3"><Link href="/devices"><ArrowLeft /> Back to devices</Link></Button>
                    <h1 className="text-2xl font-semibold tracking-tight">Register a device</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Add a customer device to the repair center inventory.</p>
                </div>
                <Card className="max-w-3xl">
                    <CardHeader>
                        <CardTitle className="text-base">Device details</CardTitle>
                        <CardDescription>Serial numbers are stored in uppercase.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {customers.length === 0 ? (
                            <div className="rounded-md border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200">
                                Add a customer record before registering a device. No customers are available yet.
                            </div>
                        ) : (
                            <form onSubmit={submit} className="grid gap-5 sm:grid-cols-2">
                                <div className="grid gap-2 sm:col-span-2">
                                    <Label htmlFor="customer_id">Customer</Label>
                                    <select id="customer_id" className="h-10 rounded-md border border-input bg-background px-3 text-sm" required value={data.customer_id} onChange={(event) => setData('customer_id', event.target.value)}>
                                        <option value="">Select a customer</option>
                                        {customers.map((customer) => <option key={customer.id} value={customer.id}>{customer.name}</option>)}
                                    </select>
                                    <InputError message={errors.customer_id} />
                                </div>
                                <Field label="Brand" id="brand" value={data.brand} error={errors.brand} onChange={(value) => setData('brand', value)} required placeholder="Apple" />
                                <Field label="Model" id="model" value={data.model} error={errors.model} onChange={(value) => setData('model', value)} required placeholder="MacBook Pro 14&quot;" />
                                <Field label="Device type" id="device_type" value={data.device_type} error={errors.device_type} onChange={(value) => setData('device_type', value)} placeholder="Laptop, phone, tablet..." />
                                <Field label="Serial number" id="serial_number" value={data.serial_number} error={errors.serial_number} onChange={(value) => setData('serial_number', value)} required placeholder="C02XXXXXXXXX" />
                                <Field label="Purchase date" id="purchase_date" type="date" value={data.purchase_date} error={errors.purchase_date} onChange={(value) => setData('purchase_date', value)} />
                                <div className="flex gap-2 sm:col-span-2">
                                    <Button type="submit" disabled={processing || customers.length === 0}>{processing && <LoaderCircle className="animate-spin" />} Save device</Button>
                                    <Button type="button" variant="outline" asChild><Link href="/devices">Cancel</Link></Button>
                                </div>
                            </form>
                        )}
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}

function Field({ label, id, type = 'text', value, error, onChange, required, placeholder }: { label: string; id: string; type?: string; value: string; error?: string; onChange: (value: string) => void; required?: boolean; placeholder?: string }) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>{label}</Label>
            <Input id={id} type={type} required={required} value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} />
            <InputError message={error} />
        </div>
    );
}
