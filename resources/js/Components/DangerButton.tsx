import { ButtonHTMLAttributes } from 'react';

export default function DangerButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <button
            {...props}
            className={
                `py-4 px-6 bg-rose-600 text-white font-bold rounded-2xl hover:bg-rose-500 active:scale-[0.98] transition-all text-xs uppercase tracking-widest shadow-[0_0_30px_rgba(225,29,72,0.2)] ${
                    disabled && 'opacity-50 cursor-not-allowed'
                } ` + className
            }
            disabled={disabled}
        >
            {children}
        </button>
    );
}
