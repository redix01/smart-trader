import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ArrowLeft, Check, X } from 'lucide-react';

export default function WithdrawalShow({ withdrawal }: { withdrawal: any }) {
  const { post, setData, processing } = useForm({ reason: '' });

  return (
    <AdminLayout>
      <Head title="Admin - Withdrawal Detail" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Withdrawal Details</h1>
        <p className="text-zinc-500 text-sm font-medium">Review withdrawal request details and approve or reject.</p>
      </header>
      <div className="mb-4">
        <Link href={route('admin.withdrawals.index')} className="text-sm text-zinc-400 hover:text-white flex items-center gap-1"><ArrowLeft size={14} /> Back</Link>
      </div>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 max-w-2xl">
        <div className="space-y-4">
          <div><p className="text-xs text-zinc-500">User</p><p className="text-white font-medium">{withdrawal.user.name} ({withdrawal.user.email})</p></div>
          <div><p className="text-xs text-zinc-500">Amount</p><p className="text-white font-medium font-mono">${withdrawal.amount} {withdrawal.currency}</p></div>
          <div><p className="text-xs text-zinc-500">Status</p><p className="text-white font-medium capitalize">{withdrawal.status}</p></div>
          <div><p className="text-xs text-zinc-500">Submitted</p><p className="text-white font-medium">{withdrawal.created_at}</p></div>
        </div>
        {withdrawal.status === 'pending' && (
          <div className="mt-6 flex gap-3">
            <button onClick={() => post(route('admin.withdrawals.approve', withdrawal.id))} disabled={processing}
              className="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm hover:bg-emerald-500 hover:text-black transition-all disabled:opacity-50"><Check size={16} /> Approve</button>
            <button onClick={() => { const r = prompt('Rejection reason:'); if (r) { setData('reason', r); setTimeout(() => post(route('admin.withdrawals.reject', withdrawal.id)), 0); } }} disabled={processing}
              className="flex items-center gap-2 px-4 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-sm hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50"><X size={16} /> Reject</button>
          </div>
        )}
      </div>
    </AdminLayout>
  );
}
