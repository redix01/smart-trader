import { InputHTMLAttributes } from 'react';

export default function Checkbox({
    className = '',
    ...props
}: InputHTMLAttributes<HTMLInputElement>) {
    return (
        <input
            {...props}
            type="checkbox"
            className={
                'rounded border-[#1A1A1A] text-blue-600 shadow-sm focus:ring-blue-600 bg-[#0A0A0A] ' +
                className
            }
        />
    );
}
