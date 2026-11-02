import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/react';
import { Laptop, Plus } from 'lucide-react';

type Device = {
    id: number;
    brand: string;
    model: string;
    device_type: string | null;
    serial_number: string;
    purchase_date: string | null;
    customer: { id: number; name: string };
};

type PaginationLink = { url: string | null; label: string; active: boolean };
type Paginated<T> = { data: T[]; links: PaginationLink[]; total: number; from: number | null; to: number | null };

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Devices', href: '/devices' },
];

export default function DevicesIndex({ devices }: { devices: Paginated<Device> }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Devices" />
            <main className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                    <div>
                        <p className="text-sm text-muted-foreground">Inventory</p>
                        <h1 className="mt-1 text-2xl font-semibold tracking-tight">Devices</h1>
                        <p className="mt-1 text-sm text-muted-foreground">Registered customer devices and serial numbers.</p>
                    </div>
                    <Button asChild>
                        <Link href="/devices/create"><Plus /> Register device</Link>
                    </Button>
                </header>
                <Card>
                    <CardHeader>
                        <CardTitle className="text-base">Device inventory <span className="ml-1 text-sm font-normal text-muted-foreground">({devices.total})</span></CardTitle>
                    </CardHeader>
                    <CardContent>
                        {devices.data.length ? (
                            <>
                                <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                    {devices.data.map((device) => (
                                        <article key={device.id} className="rounded-lg border p-4 transition-colors hover:bg-muted/30">
                                            <div className="flex items-start justify-between gap-3">
                                                <div className="flex items-center gap-3">
                                                    <span className="rounded-md bg-muted p-2.5 text-muted-foreground"><Laptop className="size-5" /></span>
                                                    <div>
                                                        <h2 className="font-medium">{device.brand} {device.model}</h2>
                                                        <p className="text-sm text-muted-foreground">{device.customer.name}</p>
                                                    </div>
                                                </div>
                                                {device.device_type && <Badge variant="secondary">{device.device_type}</Badge>}
                                            </div>
                                            <dl className="mt-5 grid grid-cols-2 gap-3 text-sm">
                                                <div>
                                                    <dt className="text-xs text-muted-foreground">Serial number</dt>
                                                    <dd className="mt-1 font-mono text-xs">{device.serial_number}</dd>
                                                </div>
                                                <div>
                                                    <dt className="text-xs text-muted-foreground">Purchased</dt>
                                                    <dd className="mt-1">{device.purchase_date ?? 'Not provided'}</dd>
                                                </div>
                                            </dl>
                                        </article>
                                    ))}
                                </div>
                                <Pagination links={devices.links} range={`${devices.from ?? 0}-${devices.to ?? 0} of ${devices.total} devices`} />
                            </>
                        ) : (
                            <div className="flex flex-col items-center py-16 text-center">
                                <span className="rounded-full bg-muted p-4 text-muted-foreground"><Laptop className="size-6" /></span>
                                <h2 className="mt-4 font-medium">No devices registered</h2>
                                <p className="mt-1 max-w-sm text-sm text-muted-foreground">Register a device to start tracking its repair history.</p>
                                <Button asChild className="mt-5"><Link href="/devices/create"><Plus /> Register a device</Link></Button>
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
