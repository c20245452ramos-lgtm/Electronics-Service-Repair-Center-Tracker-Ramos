import { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg
            {...props}
            viewBox="0 0 40 40"
            fill="none"
            stroke="currentColor"
            strokeWidth="2.25"
            strokeLinecap="round"
            strokeLinejoin="round"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <rect x="7" y="7" width="26" height="26" rx="4" />
            <rect x="15" y="15" width="10" height="10" rx="2" />
            <path d="M12 16h3m-3 4h3m-3 4h3m10-8h3m-3 4h3m-3 4h3m-10-11v3m4-3v3m4-3v3m-8 10v3m4-3v3m4-3v3" />
        </svg>
    );
}
