import { PropsWithChildren } from 'react';
import BrandLogo from '@/Components/BrandLogo';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-[#0A0A0A] px-4 py-12">
            <div className="w-full max-w-md space-y-8">
                <div className="flex flex-col items-center gap-4">
                    <BrandLogo className="h-20 w-auto object-contain" />
                </div>
                <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-8 shadow-2xl">
                    {children}
                </div>
            </div>
        </div>
    );
}
