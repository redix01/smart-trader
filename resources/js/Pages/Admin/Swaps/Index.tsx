import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

interface Swap {
  id: number; user: { id: number; name: string };
  from_currency: string; to_currency: string;
  from_amount: string; to_amount: string; rate: string; fee: string; status: string; created_at: string;
}

export default function SwapsIndex({ swaps, filters }: { swaps: { data: Swap[]; current_page: number; last_page: number; total: number }; filters: { status?: string } }) {
  return (
    <AdminLayout>
      <Head title="Admin - Swaps" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Swap History</h1>
        <p className="text-zinc-500 text-sm font-medium">Monitor all currency swap transactions on the platform.</p>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="p-4 border-b border-[#1A1A1A] flex gap-2">
          {['', 'pending', 'completed', 'failed'].map(s => (
            <Link key={s} href={route('admin.swaps.index', s ? { status: s } : {})}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${(filters.status ?? '') === s ? 'bg-white text-black' : 'bg-[#0A0A0A] text-zinc-400 hover:text-white'}`}>{s || 'All'}</Link>
          ))}
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead><tr className="border-b border-[#1A1A1A]">
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">User</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">From</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">To</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Amount</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Rate</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Fee</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-center">Status</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Date</th>
            </tr></thead>
            <tbody className="divide-y divide-[#0A0A0A]">
              {swaps.data.map(s => (
                <tr key={s.id} className="hover:bg-[#151515] transition-colors">
                  <td className="px-6 py-4"><span className="text-sm font-bold text-white">{s.user.name}</span></td>
                  <td className="px-6 py-4 text-sm text-zinc-400">{s.from_currency}</td>
                  <td className="px-6 py-4 text-sm text-zinc-400">{s.to_currency}</td>
                  <td className="px-6 py-4 text-right"><span className="text-sm font-bold text-white font-mono">{Number(s.from_amount).toFixed(2)} → {Number(s.to_amount).toFixed(6)}</span></td>
                  <td className="px-6 py-4 text-right text-sm text-zinc-400 font-mono">{Number(s.rate).toFixed(6)}</td>
                  <td className="px-6 py-4 text-right text-sm text-zinc-400 font-mono">${Number(s.fee).toFixed(2)}</td>
                  <td className="px-6 py-4 text-center">
                    <span className={`text-xs font-bold px-2 py-1 rounded-full ${s.status === 'completed' ? 'bg-emerald-500/10 text-emerald-500' : s.status === 'pending' ? 'bg-amber-500/10 text-amber-500' : 'bg-rose-500/10 text-rose-500'}`}>{s.status}</span>
                  </td>
                  <td className="px-6 py-4 text-xs text-zinc-500 font-mono">{s.created_at}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </AdminLayout>
  );
}
