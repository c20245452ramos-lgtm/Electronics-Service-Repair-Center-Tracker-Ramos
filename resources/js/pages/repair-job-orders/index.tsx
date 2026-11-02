import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { ClipboardList, Plus } from 'lucide-react';

type JobOrder = {
    id: number;
    job_order_number: string;
    reported_issue: string;
    job_status: string;
    total_repair_cost: string;
    completion_date: string | null;
    customer: { id: number; name: string };
    device: { id: number; brand: string; model: string; serial_number: string };
    technician: { id: number; name: string };
};

type PaginationLink = { url: string | null; label: string; active: boolean };
type Paginated<T> = { data: T[]; links: PaginationLink[]; total: number; from: number | null; to: number | null };

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Repair jobs', href: '/repair-job-orders' },
];

function statusStyle(status: string) {
    switch (status) {
        case 'Completed':
            return 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300';
        case 'Awaiting Parts':
            return 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300';
        case 'Pending':
        case 'Diagnosing':
            return 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900 dark:bg-sky-950 dark:text-sky-300';
        default:
            return 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950 dark:text-violet-300';
    }
}

export default function RepairJobOrdersIndex({ jobOrders }: { jobOrders: Paginated<JobOrder> }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Repair jobs" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p className="text-sm text-muted-foreground">Service desk</p>
                        <h1 className="mt-1 text-2xl font-semibold tracking-tight">Repair jobs</h1>
                        <p className="mt-1 text-sm text-muted-foreground">Manage repair requests from intake through pickup.</p>
                    </div>
                    <Button asChild>
                        <Link href="/repair-job-orders/create"><Plus /> New repair job</Link>
                    </Button>
                </header>
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">All repair jobs <span className="ml-1 text-sm font-normal text-muted-foreground">({jobOrders.total})</span></CardTitle>
                    </CardHeader>
                    <CardContent>
                        {jobOrders.data.length ? (
                            <>
                                <div className="overflow-x-auto">
                                    <table className="w-full min-w-[850px] text-left text-sm">
                                        <thead>
                                            <tr className="border-y text-xs text-muted-foreground">
                                                <th className="py-3 pr-4 font-medium">Job order</th>
                                                <th className="px-4 py-3 font-medium">Customer / device</th>
                                                <th className="px-4 py-3 font-medium">Reported issue</th>
                                                <th className="px-4 py-3 font-medium">Technician</th>
                                                <th className="px-4 py-3 font-medium">Status</th>
                                                <th className="py-3 pl-4 text-right font-medium">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {jobOrders.data.map((job) => (
                                                <tr key={job.id} className="border-b last:border-0">
                                                    <td className="py-4 pr-4 font-medium">{job.job_order_number}</td>
                                                    <td className="px-4 py-4">
                                                        <div className="font-medium">{job.customer.name}</div>
                                                        <div className="mt-0.5 text-xs text-muted-foreground">{job.device.brand} {job.device.model} · {job.device.serial_number}</div>
                                                    </td>
                                                    <td className="max-w-56 px-4 py-4 text-muted-foreground">{job.reported_issue}</td>
                                                    <td className="px-4 py-4">{job.technician.name}</td>
                                                    <td className="px-4 py-4"><Badge variant="outline" className={statusStyle(job.job_status)}>{job.job_status}</Badge></td>
                                                    <td className="py-4 pl-4 text-right font-medium tabular-nums">${Number(job.total_repair_cost).toFixed(2)}</td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                                <Pagination links={jobOrders.links} range={`${jobOrders.from ?? 0}-${jobOrders.to ?? 0} of ${jobOrders.total} jobs`} />
                            </>
                        ) : (
                            <div className="flex flex-col items-center py-16 text-center">
                                <span className="rounded-full bg-muted p-4 text-muted-foreground"><ClipboardList className="size-6" /></span>
                                <h2 className="mt-4 font-medium">No repair jobs yet</h2>
                                <p className="mt-1 max-w-sm text-sm text-muted-foreground">Start by recording a repair request for a registered device.</p>
                                <Button asChild className="mt-5"><Link href="/repair-job-orders/create"><Plus /> Create a repair job</Link></Button>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}

function Pagination({ links, range }: { links: PaginationLink[]; range: string }) {
    return (
        <nav className="mt-5 flex flex-wrap items-center justify-between gap-3 border-t pt-4" aria-label="Pagination">
            <p className="text-sm text-muted-foreground">{range}</p>
            <div className="flex gap-1">
                {links.map((link, index) => (
                    <Button key={`${link.label}-${index}`} variant={link.active ? 'secondary' : 'ghost'} size="sm" disabled={!link.url} asChild={Boolean(link.url)}>
                        {link.url ? <Link href={link.url}>{paginationLabel(link.label)}</Link> : <span>{paginationLabel(link.label)}</span>}
                    </Button>
                ))}
            </div>
        </nav>
    );
}

function paginationLabel(label: string) {
    return label.replace(/&laquo;|&raquo;/g, (entity) => entity === '&laquo;' ? '‹' : '›');
}
