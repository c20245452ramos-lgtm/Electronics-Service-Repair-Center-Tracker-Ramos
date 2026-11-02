import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/react';
import { ArrowDownRight, ArrowUpRight, Clock3, ClipboardList, Laptop, Search, Wrench } from 'lucide-react';
import { useMemo, useState } from 'react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const jobs = [
    { order: 'RO-2025-1048', customer: 'Olivia Rhye', device: 'MacBook Pro 14"', issue: 'Screen replacement', technician: 'Marcus Chen', status: 'In Progress', due: 'Today' },
    { order: 'RO-2025-1047', customer: 'Phoenix Baker', device: 'iPhone 15 Pro', issue: 'Battery replacement', technician: 'Alex Morgan', status: 'Awaiting Parts', due: 'Tomorrow' },
    { order: 'RO-2025-1046', customer: 'Lana Steiner', device: 'Dell XPS 13', issue: 'Won’t power on', technician: 'Jordan Lee', status: 'Completed', due: 'Oct 28' },
    { order: 'RO-2025-1045', customer: 'Demi Wilkinson', device: 'iPad Air', issue: 'Charging port repair', technician: 'Marcus Chen', status: 'In Progress', due: 'Oct 30' },
    { order: 'RO-2025-1044', customer: 'Candice Wu', device: 'Samsung S24', issue: 'Cracked display', technician: 'Alex Morgan', status: 'Diagnosing', due: 'Oct 30' },
];

const filters = ['All jobs', 'In Progress', 'Awaiting Parts', 'Completed'];

function statusStyle(status: string) {
    switch (status) {
        case 'Completed':
            return 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-300';
        case 'Awaiting Parts':
            return 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-300';
        case 'Diagnosing':
            return 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-900 dark:bg-sky-950 dark:text-sky-300';
        default:
            return 'border-violet-200 bg-violet-50 text-violet-700 dark:border-violet-900 dark:bg-violet-950 dark:text-violet-300';
    }
}

export default function Dashboard() {
    const [activeFilter, setActiveFilter] = useState('All jobs');
    const [search, setSearch] = useState('');

    const filteredJobs = useMemo(() => {
        const query = search.trim().toLowerCase();

        return jobs.filter((job) => {
            const matchesFilter = activeFilter === 'All jobs' || job.status === activeFilter;
            const matchesSearch = !query || [job.order, job.customer, job.device, job.issue, job.technician].some((value) => value.toLowerCase().includes(query));

            return matchesFilter && matchesSearch;
        });
    }, [activeFilter, search]);

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Repair center dashboard" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:gap-8 md:p-8">
                <section className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <div className="mb-2 flex items-center gap-2">
                            <h1 className="text-2xl font-semibold tracking-tight">Repair center</h1>
                            <Badge variant="outline" className="font-normal text-muted-foreground">Preview data</Badge>
                        </div>
                        <p className="text-sm text-muted-foreground">A quick overview of your service operations.</p>
                    </div>
                    <Button variant="outline" className="w-fit">
                        <Clock3 />
                        Last 30 days
                    </Button>
                </section>

                <section className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Repair center metrics">
                    <MetricCard title="Open repair jobs" value="24" change="+12.5%" trend="up" description="vs. previous 30 days" icon={<ClipboardList />} />
                    <MetricCard title="Ready for pickup" value="8" change="3 this week" trend="up" description="completed repairs" icon={<Laptop />} />
                    <MetricCard title="Awaiting parts" value="5" change="-2" trend="down" description="vs. previous 30 days" icon={<Clock3 />} />
                    <MetricCard title="Revenue this month" value="$8,420" change="+8.2%" trend="up" description="vs. previous month" icon={<Wrench />} />
                </section>

                <section className="grid gap-4 xl:grid-cols-[1.7fr_1fr]">
                    <Card>
                        <CardHeader className="flex flex-row items-start justify-between space-y-0">
                            <div>
                                <CardTitle className="text-base">Job activity</CardTitle>
                                <p className="mt-1 text-sm text-muted-foreground">Repair jobs created over the last 7 days</p>
                            </div>
                            <Badge variant="secondary" className="font-normal">This week</Badge>
                        </CardHeader>
                        <CardContent>
                            <div className="flex h-48 items-end justify-between gap-3 border-b pb-2 sm:gap-5">
                                {[42, 68, 51, 82, 61, 94, 72].map((height, index) => (
                                    <div key={index} className="flex h-full flex-1 flex-col items-center justify-end gap-2">
                                        <div className="w-full max-w-12 rounded-t-md bg-primary/80 transition-all hover:bg-primary" style={{ height: `${height}%` }} />
                                        <span className="text-xs text-muted-foreground">{['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][index]}</span>
                                    </div>
                                ))}
                            </div>
                            <div className="mt-4 flex items-center gap-2 text-sm text-muted-foreground">
                                <span className="size-2 rounded-full bg-primary" />
                                <span>New repair jobs</span>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="text-base">Job status</CardTitle>
                            <p className="text-sm text-muted-foreground">Current workload by status</p>
                        </CardHeader>
                        <CardContent className="space-y-5">
                            <StatusBar label="In progress" count={11} total={24} className="bg-violet-500" />
                            <StatusBar label="Diagnosing" count={6} total={24} className="bg-sky-500" />
                            <StatusBar label="Awaiting parts" count={5} total={24} className="bg-amber-500" />
                            <StatusBar label="Ready for pickup" count={2} total={24} className="bg-emerald-500" />
                        </CardContent>
                    </Card>
                </section>

                <Card>
                    <CardHeader className="gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <CardTitle className="text-base">Recent repair jobs</CardTitle>
                            <p className="mt-1 text-sm text-muted-foreground">Track the latest service requests and their progress.</p>
                        </div>
                        <div className="relative w-full sm:max-w-xs">
                            <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />
                            <input
                                aria-label="Search repair jobs"
                                className="h-9 w-full rounded-md border border-input bg-background pr-3 pl-9 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring"
                                placeholder="Search jobs..."
                                value={search}
                                onChange={(event) => setSearch(event.target.value)}
                            />
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div className="mb-4 flex flex-wrap gap-2">
                            {filters.map((filter) => (
                                <Button
                                    key={filter}
                                    size="sm"
                                    variant={activeFilter === filter ? 'secondary' : 'ghost'}
                                    onClick={() => setActiveFilter(filter)}
                                >
                                    {filter}
                                </Button>
                            ))}
                        </div>
                        <div className="overflow-x-auto">
                            <table className="w-full min-w-[760px] text-left text-sm">
                                <thead>
                                    <tr className="border-y text-xs text-muted-foreground">
                                        <th className="py-3 pr-4 font-medium">Job order</th>
                                        <th className="px-4 py-3 font-medium">Customer / device</th>
                                        <th className="px-4 py-3 font-medium">Issue</th>
                                        <th className="px-4 py-3 font-medium">Technician</th>
                                        <th className="px-4 py-3 font-medium">Status</th>
                                        <th className="py-3 pl-4 text-right font-medium">Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredJobs.map((job) => (
                                        <tr key={job.order} className="border-b last:border-0">
                                            <td className="py-4 pr-4 font-medium">{job.order}</td>
                                            <td className="px-4 py-4">
                                                <div className="font-medium">{job.customer}</div>
                                                <div className="mt-0.5 text-xs text-muted-foreground">{job.device}</div>
                                            </td>
                                            <td className="px-4 py-4 text-muted-foreground">{job.issue}</td>
                                            <td className="px-4 py-4">{job.technician}</td>
                                            <td className="px-4 py-4">
                                                <Badge variant="outline" className={statusStyle(job.status)}>{job.status}</Badge>
                                            </td>
                                            <td className="py-4 pl-4 text-right text-muted-foreground">{job.due}</td>
                                        </tr>
                                    ))}
                                    {filteredJobs.length === 0 && (
                                        <tr>
                                            <td colSpan={6} className="py-10 text-center text-muted-foreground">No repair jobs match your search.</td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}

function MetricCard({ title, value, change, trend, description, icon }: { title: string; value: string; change: string; trend: 'up' | 'down'; description: string; icon: React.ReactNode }) {
    const TrendIcon = trend === 'up' ? ArrowUpRight : ArrowDownRight;

    return (
        <Card>
            <CardContent className="p-5">
                <div className="flex items-center justify-between">
                    <p className="text-sm font-medium text-muted-foreground">{title}</p>
                    <span className="rounded-md bg-muted p-2 text-muted-foreground">{icon}</span>
                </div>
                <div className="mt-4 flex items-baseline gap-2">
                    <span className="text-2xl font-semibold tracking-tight">{value}</span>
                    <span className={`inline-flex items-center text-xs font-medium ${trend === 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'}`}>
                        <TrendIcon className="mr-0.5 size-3.5" />
                        {change}
                    </span>
                </div>
                <p className="mt-1 text-xs text-muted-foreground">{description}</p>
            </CardContent>
        </Card>
    );
}

function StatusBar({ label, count, total, className }: { label: string; count: number; total: number; className: string }) {
    return (
        <div>
            <div className="mb-2 flex items-center justify-between text-sm">
                <span>{label}</span>
                <span className="font-medium tabular-nums">{count}</span>
            </div>
            <div className="h-2 overflow-hidden rounded-full bg-muted">
                <div className={`h-full rounded-full ${className}`} style={{ width: `${(count / total) * 100}%` }} />
            </div>
        </div>
    );
}
