import { Head, Link, router, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Check, Eye, X } from 'lucide-react';

interface Withdrawal {
  id: number; user: { id: number; name: string };
  amount: string; currency: string; status: string; created_at: string;
}

export default function WithdrawalsIndex({ withdrawals, filters }: { withdrawals: { data: Withdrawal[]; current_page: number; last_page: number; total: number }; filters: { status?: string } }) {
  const { post, processing } = useForm({});
  const declineWithdrawal = (withdrawal: Withdrawal) => {
    const reason = prompt('Decline withdrawal reason:');

    if (!reason?.trim()) {
      return;
    }

    router.post(route('admin.withdrawals.reject', withdrawal.id), { reason: reason.trim() }, { preserveScroll: true });
  };

  return (
    <AdminLayout>
      <Head title="Admin - Withdrawals" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Withdrawals</h1>
        <p className="text-zinc-500 text-sm font-medium">Review and process pending withdrawal requests.</p>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="p-4 border-b border-[#1A1A1A] flex flex-wrap gap-2">
          {['', 'pending', 'approved', 'rejected'].map(s => (
            <Link key={s} href={route('admin.withdrawals.index', s ? { status: s } : {})}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${(filters.status ?? '') === s ? 'bg-white text-black' : 'bg-[#0A0A0A] text-zinc-400 hover:text-white'}`}>{s || 'All'}</Link>
          ))}
        </div>
        <div className="overflow-x-auto">
        <table className="w-full text-left">
          <thead><tr className="border-b border-[#1A1A1A]">
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">User</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Amount</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Status</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Date</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Actions</th>
          </tr></thead>
          <tbody className="divide-y divide-[#0A0A0A]">
            {withdrawals.data.map(w => (
              <tr key={w.id} className="hover:bg-[#151515] transition-colors">
                <td className="px-6 py-4"><span className="text-sm font-bold text-white">{w.user?.name ?? 'Deleted User'}</span></td>
                <td className="px-6 py-4 text-right"><span className="text-sm font-bold text-white font-mono">${w.amount} {w.currency}</span></td>
                <td className="px-6 py-4"><span className={`text-xs font-bold px-2 py-1 rounded-full ${w.status === 'approved' ? 'bg-emerald-500/10 text-emerald-500' : w.status === 'rejected' ? 'bg-rose-500/10 text-rose-500' : 'bg-amber-500/10 text-amber-500'}`}>{w.status}</span></td>
                <td className="px-6 py-4 text-xs text-zinc-500 font-mono">{w.created_at}</td>
                <td className="px-6 py-4">
                  <div className="flex items-center justify-end gap-1.5">
                    {w.status === 'pending' && (
                      <>
                        <button
                          onClick={() => post(route('admin.withdrawals.approve', w.id))}
                          disabled={processing}
                          className="p-2 bg-emerald-500/10 text-emerald-500 rounded-xl hover:bg-emerald-500 hover:text-black transition-all disabled:opacity-50"
                          title="Approve"
                        >
                          <Check size={15} />
                        </button>
                        <button
                          onClick={() => declineWithdrawal(w)}
                          disabled={processing}
                          className="p-2 bg-rose-500/10 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50"
                          title="Decline"
                        >
                          <X size={15} />
                        </button>
                      </>
                    )}
                    <Link
                      href={route('admin.withdrawals.show', w.id)}
                      className="p-2 bg-blue-500/10 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition-all inline-flex"
                      title="View details"
                    >
                      <Eye size={15} />
                    </Link>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>
      </div>
    </AdminLayout>
  );
}
