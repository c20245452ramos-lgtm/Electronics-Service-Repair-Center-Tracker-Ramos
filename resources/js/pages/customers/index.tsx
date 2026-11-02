import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Plus, UserRound, Users } from 'lucide-react';

type Customer = {
    id: number;
    name: string;
    phone: string | null;
    email: string | null;
    devices_count: number;
    repair_job_orders_count: number;
};
type Page<T> = { data: T[]; total: number };

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Customers', href: '/customers' },
];

export default function CustomersIndex({ customers }: { customers: Page<Customer> }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Customers" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p className="text-sm text-muted-foreground">People</p>
                        <h1 className="mt-1 text-2xl font-semibold tracking-tight">Customers</h1>
                        <p className="mt-1 text-sm text-muted-foreground">Customer contacts and their registered devices.</p>
                    </div>
                    <Button asChild><Link href="/customers/create"><Plus /> Add customer</Link></Button>
                </header>
                <Card>
                    <CardHeader><CardTitle className="text-base">Customer directory <span className="ml-1 text-sm font-normal text-muted-foreground">({customers.total})</span></CardTitle></CardHeader>
                    <CardContent>
                        {customers.data.length ? (
                            <div className="divide-y">
                                {customers.data.map((customer) => (
                                    <article key={customer.id} className="flex flex-col justify-between gap-3 py-4 sm:flex-row sm:items-center">
                                        <div className="flex items-center gap-3">
                                            <span className="rounded-full bg-muted p-2.5 text-muted-foreground"><UserRound className="size-5" /></span>
                                            <div>
                                                <h2 className="font-medium">{customer.name}</h2>
                                                <p className="text-sm text-muted-foreground">{customer.email || customer.phone || 'No contact details'}</p>
                                            </div>
                                        </div>
                                        <div className="flex gap-5 pl-12 text-sm text-muted-foreground sm:pl-0">
                                            <span>{customer.devices_count} devices</span>
                                            <span>{customer.repair_job_orders_count} repair jobs</span>
                                        </div>
                                    </article>
                                ))}
                            </div>
                        ) : (
                            <Empty icon={<Users className="size-6" />} title="No customers yet" description="Add customers before registering devices or repair jobs." href="/customers/create" action="Add a customer" />
                        )}
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}

function Empty({ icon, title, description, href, action }: { icon: React.ReactNode; title: string; description: string; href: string; action: string }) {
    return (
        <div className="flex flex-col items-center py-16 text-center">
            <span className="rounded-full bg-muted p-4 text-muted-foreground">{icon}</span>
            <h2 className="mt-4 font-medium">{title}</h2>
            <p className="mt-1 max-w-sm text-sm text-muted-foreground">{description}</p>
            <Button asChild className="mt-5"><Link href={href}><Plus /> {action}</Link></Button>
        </div>
    );
}
