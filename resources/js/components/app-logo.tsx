import AppLogoIcon from './app-logo-icon';

export default function AppLogo() {
    return (
        <>
            <div className="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-md">
                <AppLogoIcon className="size-5 text-white dark:text-black" />
            </div>
            <div className="ml-1 grid min-w-0 flex-1 text-left text-xs">
                <span className="truncate leading-none font-semibold">Electronics Repair Center</span>
            </div>
        </>
    );
}
