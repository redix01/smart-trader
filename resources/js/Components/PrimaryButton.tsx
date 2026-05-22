import { ButtonHTMLAttributes } from 'react';

export default function PrimaryButton({
    className = '',
    disabled,
    children,
    ...props
}: ButtonHTMLAttributes<HTMLButtonElement>) {
    return (
        <button
            {...props}
            className={
                `w-full py-4 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-500 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-[0_0_30px_rgba(37,99,235,0.2)] text-xs uppercase tracking-widest ${
                    disabled && 'opacity-50 cursor-not-allowed'
                } ` + className
            }
            disabled={disabled}
        >
            {children}
        </button>
    );
}
