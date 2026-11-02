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
type Device = { id: number; brand: string; model: string; serial_number: string };
type Technician = { id: number; name: string; specialty: string | null };

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Repair jobs', href: '/repair-job-orders' },
    { title: 'New repair job', href: '/repair-job-orders/create' },
];

export default function CreateRepairJobOrder({ customers, devices, technicians }: { customers: Customer[]; devices: Device[]; technicians: Technician[] }) {
    const { data, setData, post, processing, errors } = useForm({
        customer_id: '',
        device_id: '',
        technician_id: '',
        job_order_number: '',
        reported_issue: '',
        resolution_notes: '',
        job_status: 'Pending',
        parts_cost: '0',
        labor_cost: '0',
        completion_date: '',
    });

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        post('/repair-job-orders');
    }

    const ready = customers.length > 0 && devices.length > 0 && technicians.length > 0;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="New repair job" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <div>
                    <Button variant="ghost" size="sm" asChild className="-ml-3 mb-3"><Link href="/repair-job-orders"><ArrowLeft /> Back to repair jobs</Link></Button>
                    <h1 className="text-2xl font-semibold tracking-tight">New repair job</h1>
                    <p className="mt-1 text-sm text-muted-foreground">Record the repair request, assign a technician, and estimate costs.</p>
                </div>
                <Card className="max-w-4xl">
                    <CardHeader>
                        <CardTitle className="text-base">Repair order details</CardTitle>
                        <CardDescription>Total repair cost is calculated from parts and labor.</CardDescription>
                    </CardHeader>
                    <CardContent>
                        {!ready ? (
                            <div className="rounded-md border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-200">
                                A customer, device, and technician are required before creating a repair job. Add those records to the database first.
                            </div>
                        ) : (
                            <form onSubmit={submit} className="grid gap-5 sm:grid-cols-2">
                                <Field label="Job order number" id="job_order_number" value={data.job_order_number} error={errors.job_order_number} onChange={(value) => setData('job_order_number', value)} required placeholder="RO-2026-0001" />
                                <SelectField id="job_status" label="Status" value={data.job_status} error={errors.job_status} onChange={(value) => setData('job_status', value)} options={['Pending', 'In Progress', 'Awaiting Parts', 'On Hold', 'Completed']} />
                                <SelectField id="customer_id" label="Customer" value={data.customer_id} error={errors.customer_id} onChange={(value) => setData('customer_id', value)} options={customers.map((customer) => ({ value: String(customer.id), label: customer.name }))} />
                                <SelectField id="device_id" label="Device" value={data.device_id} error={errors.device_id} onChange={(value) => setData('device_id', value)} options={devices.map((device) => ({ value: String(device.id), label: `${device.brand} ${device.model} · ${device.serial_number}` }))} />
                                <SelectField id="technician_id" label="Technician" value={data.technician_id} error={errors.technician_id} onChange={(value) => setData('technician_id', value)} options={technicians.map((technician) => ({ value: String(technician.id), label: technician.specialty ? `${technician.name} · ${technician.specialty}` : technician.name }))} />
                                <Field label="Completion date" id="completion_date" type="date" value={data.completion_date} error={errors.completion_date} onChange={(value) => setData('completion_date', value)} />
                                <div className="grid gap-2 sm:col-span-2">
                                    <Label htmlFor="reported_issue">Reported issue</Label>
                                    <textarea id="reported_issue" required maxLength={2000} value={data.reported_issue} onChange={(event) => setData('reported_issue', event.target.value)} placeholder="Describe the issue reported by the customer..." className="min-h-28 rounded-md border border-input bg-background px-3 py-2 text-sm outline-none placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring" />
                                    <InputError message={errors.reported_issue} />
                                </div>
                                <div className="grid gap-2 sm:col-span-2">
                                    <Label htmlFor="resolution_notes">Resolution notes</Label>
                                    <textarea id="resolution_notes" maxLength={2000} value={data.resolution_notes} onChange={(event) => setData('resolution_notes', event.target.value)} placeholder="Add findings or repair notes. Required before marking as completed." className="min-h-24 rounded-md border border-input bg-background px-3 py-2 text-sm outline-none placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring" />
                                    <InputError message={errors.resolution_notes} />
                                </div>
                                <Field label="Parts cost" id="parts_cost" type="number" value={data.parts_cost} error={errors.parts_cost} onChange={(value) => setData('parts_cost', value)} required min="0" step="0.01" />
                                <Field label="Labor cost" id="labor_cost" type="number" value={data.labor_cost} error={errors.labor_cost} onChange={(value) => setData('labor_cost', value)} required min="0" step="0.01" />
                                <div className="flex gap-2 sm:col-span-2">
                                    <Button type="submit" disabled={processing}>{processing && <LoaderCircle className="animate-spin" />} Create repair job</Button>
                                    <Button type="button" variant="outline" asChild><Link href="/repair-job-orders">Cancel</Link></Button>
                                </div>
                            </form>
                        )}
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}

type Option = string | { value: string; label: string };

function SelectField({ id, label, value, error, onChange, options }: { id: string; label: string; value: string; error?: string; onChange: (value: string) => void; options: Option[] }) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>{label}</Label>
            <select id={id} required className="h-10 rounded-md border border-input bg-background px-3 text-sm" value={value} onChange={(event) => onChange(event.target.value)}>
                <option value="">Select {label.toLowerCase()}</option>
                {options.map((option) => {
                    const item = typeof option === 'string' ? { value: option, label: option } : option;
                    return <option key={item.value} value={item.value}>{item.label}</option>;
                })}
            </select>
            <InputError message={error} />
        </div>
    );
}

function Field({ label, id, type = 'text', value, error, onChange, required, placeholder, min, step }: { label: string; id: string; type?: string; value: string; error?: string; onChange: (value: string) => void; required?: boolean; placeholder?: string; min?: string; step?: string }) {
    return (
        <div className="grid gap-2">
            <Label htmlFor={id}>{label}</Label>
            <Input id={id} type={type} required={required} value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} min={min} step={step} />
            <InputError message={error} />
        </div>
    );
}
