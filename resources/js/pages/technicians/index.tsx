import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, UserCheck, UserX, Wrench } from 'lucide-react';

type Technician = { id: number; name: string; specialty: string | null; phone: string | null; email: string | null; is_active: boolean; repair_job_orders_count: number };
type Page<T> = { data: T[]; total: number };

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Technicians', href: '/technicians' },
];

export default function TechniciansIndex({ technicians }: { technicians: Page<Technician> }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Technicians" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p className="text-sm text-muted-foreground">Team</p>
                        <h1 className="mt-1 text-2xl font-semibold tracking-tight">Technicians</h1>
                        <p className="mt-1 text-sm text-muted-foreground">Manage repair staff and assigned workload.</p>
                    </div>
                    <Button asChild><Link href="/technicians/create"><Plus /> Add technician</Link></Button>
                </header>
                <Card>
                    <CardHeader><CardTitle className="text-base">Technician directory <span className="ml-1 text-sm font-normal text-muted-foreground">({technicians.total})</span></CardTitle></CardHeader>
                    <CardContent>
                        {technicians.data.length ? (
                            <div className="divide-y">
                                {technicians.data.map((technician) => (
                                    <article key={technician.id} className="grid gap-3 py-4 sm:grid-cols-[minmax(0,1fr)_9rem_auto] sm:items-center">
                                        <div className="flex min-w-0 items-center gap-3">
                                            <span className="rounded-full bg-muted p-2.5 text-muted-foreground"><Wrench className="size-5" /></span>
                                            <div className="min-w-0">
                                                <h2 className="font-medium">{technician.name}</h2>
                                                <p className="break-words text-sm text-muted-foreground">
                                                    {technician.specialty || 'General repairs'} · {technician.email || technician.phone || 'No contact details'}
                                                    {!technician.is_active && <span className="ml-2 text-amber-700 dark:text-amber-400">Inactive</span>}
                                                </p>
                                            </div>
                                        </div>
                                        <p className="ml-12 text-sm text-muted-foreground sm:ml-0 sm:text-right">
                                            {technician.repair_job_orders_count} repair jobs
                                        </p>
                                        <div className="ml-12 flex flex-wrap gap-2 sm:ml-0">
                                            <Button variant="outline" size="sm" asChild>
                                                <Link href={`/technicians/${technician.id}/edit`} aria-label={`Edit ${technician.name}`}>
                                                    <Pencil /> Edit
                                                </Link>
                                            </Button>
                                            <Button
                                                variant={technician.is_active ? 'outline' : 'secondary'}
                                                size="sm"
                                                onClick={() => {
                                                    const action = technician.is_active ? 'deactivate' : 'activate';
                                                    if (window.confirm(`Are you sure you want to ${action} ${technician.name}?`)) {
                                                        router.patch(`/technicians/${technician.id}/active`, {}, { preserveScroll: true });
                                                    }
                                                }}
                                            >
                                                {technician.is_active ? <><UserX /> Deactivate</> : <><UserCheck /> Activate</>}
                                            </Button>
                                        </div>
                                    </article>
                                ))}
                            </div>
                        ) : (
                            <div className="flex flex-col items-center py-16 text-center">
                                <span className="rounded-full bg-muted p-4 text-muted-foreground"><Wrench className="size-6" /></span>
                                <h2 className="mt-4 font-medium">No technicians yet</h2>
                                <p className="mt-1 max-w-sm text-sm text-muted-foreground">Add technicians before assigning repair jobs.</p>
                                <Button asChild className="mt-5"><Link href="/technicians/create"><Plus /> Add a technician</Link></Button>
                            </div>
                        )}
                    </CardContent>
                </Card>
            </main>
        </AppLayout>
    );
}
