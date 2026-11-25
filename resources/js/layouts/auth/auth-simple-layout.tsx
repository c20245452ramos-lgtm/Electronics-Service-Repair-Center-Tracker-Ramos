import AppLogoIcon from '@/components/app-logo-icon';
import { Link } from '@inertiajs/react';
import { Leaf } from 'lucide-react';

interface AuthLayoutProps {
    children: React.ReactNode;
    name?: string;
    title?: string;
    description?: string;
    showFallingLeaves?: boolean;
}

export default function AuthSimpleLayout({ children, title, description, showFallingLeaves = false }: AuthLayoutProps) {
    return (
        <div className="bg-background relative flex min-h-svh flex-col items-center justify-center gap-6 overflow-hidden p-6 md:p-10">
            {showFallingLeaves && (
                <div className="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
                    {[1, 2, 3, 4, 5, 6].map((leaf) => (
                        <span key={leaf} className={`auth-falling-leaf auth-falling-leaf-${leaf}`}>
                            <Leaf />
                        </span>
                    ))}
                </div>
            )}
            <div className="relative z-10 w-full max-w-sm">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link href={route('home')} className="flex max-w-full items-center gap-3 font-medium">
                            <div className="bg-primary text-primary-foreground flex size-10 shrink-0 items-center justify-center rounded-xl shadow-sm">
                                <AppLogoIcon className="size-6" />
                            </div>
                            <span className="text-sm font-semibold tracking-tight">Electronics Repair Center</span>
                        </Link>

                        <div className="space-y-2 text-center">
                            <h1 className="text-xl font-medium">{title}</h1>
                            <p className="text-muted-foreground text-center text-sm">{description}</p>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
