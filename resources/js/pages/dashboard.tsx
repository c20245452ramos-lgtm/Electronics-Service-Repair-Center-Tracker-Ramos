import { Card, CardContent } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { ClipboardList, Clock3, Laptop, Wrench } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

type DashboardMetrics = {
    openRepairJobs: number;
    completedRepairs: number;
    awaitingParts: number;
    monthlyRevenue: number | string;
};

export default function Dashboard({ metrics }: { metrics: DashboardMetrics }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Repair center dashboard" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:gap-8 md:p-8">
                <section className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <h1 className="text-2xl font-semibold tracking-tight">Repair center</h1>
                        <p className="text-sm text-muted-foreground">A quick overview of your service operations.</p>
                    </div>
                </section>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Repair center metrics">
                    <MetricCard title="Open repair jobs" value={metrics.openRepairJobs} description="Currently open" icon={<ClipboardList />} />
                    <MetricCard title="Completed repairs" value={metrics.completedRepairs} description="Completed jobs" icon={<Laptop />} />
                    <MetricCard title="Awaiting parts" value={metrics.awaitingParts} description="Currently waiting for parts" icon={<Clock3 />} />
                    <MetricCard title="Revenue this month" value={`₱${Number(metrics.monthlyRevenue).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`} description="Payments received this month" icon={<Wrench />} />
                </section>

            </main>
        </AppLayout>
    );
}

function MetricCard({ title, value, description, icon }: { title: string; value: string | number; description: string; icon: React.ReactNode }) {
    return (
        <Card>
            <CardContent className="p-5">
                <div className="flex items-center justify-between">
                    <p className="text-sm font-medium text-muted-foreground">{title}</p>
                    <span className="rounded-md bg-muted p-2 text-muted-foreground">{icon}</span>
                </div>
                <div className="mt-4 flex items-baseline gap-2">
                    <span className="text-2xl font-semibold tracking-tight">{value}</span>
                </div>
                <p className="mt-1 text-xs text-muted-foreground">{description}</p>
            </CardContent>
        </Card>
    );
}
