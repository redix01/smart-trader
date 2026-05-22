import AppLayout from '@/Layouts/AppLayout';
import { PageProps } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import DeleteUserForm from './Partials/DeleteUserForm';
import UpdatePasswordForm from './Partials/UpdatePasswordForm';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm';
import { ShieldCheck, User, Calendar, Layers } from 'lucide-react';

export default function Edit({
    mustVerifyEmail,
    status,
}: PageProps<{ mustVerifyEmail: boolean; status?: string }>) {
    const user = usePage().props.auth.user;

    const initials = user.name
        .split(' ')
        .map((n: string) => n[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);

    const kycColor =
        user.kyc_level === 'verified'
            ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500'
            : user.kyc_level === 'pending'
            ? 'bg-amber-500/10 border-amber-500/20 text-amber-500'
            : user.kyc_level === 'rejected'
            ? 'bg-rose-500/10 border-rose-500/20 text-rose-500'
            : 'bg-zinc-500/10 border-zinc-500/20 text-zinc-400';

    return (
        <AppLayout>
            <Head title="Profile" />

            <header className="mb-8 flex flex-col gap-1">
                <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Profile</h1>
                <p className="text-zinc-500 text-sm font-medium">Manage your account settings and security.</p>
            </header>

            {/* Hero card */}
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 lg:p-8 mb-6 relative overflow-hidden">
                <div className="absolute top-0 right-0 w-64 h-64 bg-blue-600/5 rounded-full blur-[80px] pointer-events-none" />
                <div className="relative flex flex-col sm:flex-row items-start sm:items-center gap-6">
                    <div className="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-2xl font-bold text-white shadow-[0_0_30px_rgba(37,99,235,0.2)] flex-shrink-0 select-none">
                        {initials}
                    </div>
                    <div className="flex-1 min-w-0">
                        <div className="flex flex-wrap items-center gap-3 mb-1">
                            <h2 className="text-xl font-bold text-white">{user.name}</h2>
                            <span className={`px-3 py-1 border rounded-full text-[10px] font-bold uppercase tracking-widest ${kycColor}`}>
                                {user.kyc_level ?? 'Unverified'}
                            </span>
                        </div>
                        <p className="text-zinc-500 text-sm mb-4">{user.email}</p>
                        <div className="flex flex-wrap gap-6">
                            <div className="flex items-center gap-2">
                                <Layers size={14} className="text-zinc-600" />
                                <div>
                                    <p className="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Account Tier</p>
                                    <p className="text-xs font-semibold text-zinc-300 capitalize">{user.account_tier ?? 'Standard'}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <ShieldCheck size={14} className="text-zinc-600" />
                                <div>
                                    <p className="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">KYC Status</p>
                                    <p className={`text-xs font-semibold capitalize ${user.kyc_level === 'verified' ? 'text-emerald-500' : user.kyc_level === 'pending' ? 'text-amber-500' : 'text-zinc-400'}`}>
                                        {user.kyc_level ?? 'Unverified'}
                                    </p>
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <User size={14} className="text-zinc-600" />
                                <div>
                                    <p className="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">User ID</p>
                                    <p className="text-xs font-semibold text-zinc-300 font-mono">#{user.id}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Two-column form layout */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div className="lg:col-span-2 space-y-6">
                    <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 lg:p-8">
                        <UpdateProfileInformationForm
                            mustVerifyEmail={mustVerifyEmail}
                            status={status}
                        />
                    </div>
                    <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 lg:p-8">
                        <UpdatePasswordForm />
                    </div>
                </div>
                <div>
                    <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 lg:p-8">
                        <DeleteUserForm />
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
