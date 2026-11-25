import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout';

export default function AuthLayout({
    children,
    title,
    description,
    showFallingLeaves,
}: {
    children: React.ReactNode;
    title: string;
    description: string;
    showFallingLeaves?: boolean;
}) {
    return (
        <AuthLayoutTemplate title={title} description={description} showFallingLeaves={showFallingLeaves}>
            {children}
        </AuthLayoutTemplate>
    );
}
