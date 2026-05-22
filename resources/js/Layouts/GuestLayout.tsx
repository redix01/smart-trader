import { PropsWithChildren } from 'react';
import { BarChart3 } from 'lucide-react';

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className="flex min-h-screen flex-col items-center justify-center bg-[#0A0A0A] px-4 py-12">
            <div className="w-full max-w-md space-y-8">
                <div className="flex flex-col items-center gap-4">
                    <div className="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center shadow-[0_0_30px_rgba(37,99,235,0.3)]">
                        <BarChart3 className="text-white w-8 h-8" />
                    </div>
                    <h1 className="text-2xl font-bold text-white tracking-tight">Cognizant-Pro Market</h1>
                </div>
                <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-8 shadow-2xl">
                    {children}
                </div>
            </div>
        </div>
    );
}
