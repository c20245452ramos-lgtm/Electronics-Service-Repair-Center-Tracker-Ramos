import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ClipboardList, Laptop, UsersRound } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type DashboardMetrics = {
    repairJobs: number;
    devices: number;
    customers: number;
};

export default function Dashboard({ metrics }: { metrics: DashboardMetrics }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Repair center dashboard" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:gap-8 md:p-8">
                <section className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Repair center</h1>
                        <p className="text-muted-foreground mt-2 text-sm">A live overview of your repair jobs, devices, and customers.</p>
                    </div>
                </section>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" aria-label="Repair center totals">
                    <MetricCard
                        title="Repair jobs"
                        value={metrics.repairJobs}
                        description="All recorded repair requests"
                        href="/repair-job-orders"
                        icon={<ClipboardList />}
                    />
                    <MetricCard title="Devices" value={metrics.devices} description="Registered customer devices" href="/devices" icon={<Laptop />} />
                    <MetricCard
                        title="Customers"
                        value={metrics.customers}
                        description="Registered customers"
                        href="/customers"
                        icon={<UsersRound />}
                    />
                </section>
            </main>
        </AppLayout>
    );
}

function MetricCard({
    title,
    value,
    description,
    href,
    icon,
}: {
    title: string;
    value: number;
    description: string;
    href: string;
    icon: React.ReactNode;
}) {
    return (
        <Link href={href} className="focus-visible:ring-ring block rounded-xl focus-visible:ring-2 focus-visible:outline-hidden">
            <Card className="hover:bg-muted/50 transition-colors">
                <CardContent className="p-5">
                    <div className="flex items-center justify-between">
                        <p className="text-muted-foreground text-sm font-medium">{title}</p>
                        <span className="bg-muted text-muted-foreground rounded-md p-2">{icon}</span>
                    </div>
                    <p className="mt-4 text-2xl font-semibold tracking-tight">{value}</p>
                    <p className="text-muted-foreground mt-1 text-xs">{description}</p>
                </CardContent>
            </Card>
        </Link>
    );
}
