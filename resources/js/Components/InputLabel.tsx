import { LabelHTMLAttributes } from 'react';

export default function InputLabel({
    value,
    className = '',
    children,
    ...props
}: LabelHTMLAttributes<HTMLLabelElement> & { value?: string }) {
    return (
        <label
            {...props}
            className={
                `block text-[10px] font-bold text-zinc-500 uppercase tracking-widest ` +
                className
            }
        >
            {value ? value : children}
        </label>
    );
}
