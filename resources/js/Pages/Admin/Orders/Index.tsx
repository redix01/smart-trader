import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

interface Order {
  id: number; user: { id: number; name: string };
  type: string; side: string; pair: string; price: string | null;
  amount: string; filled: string; total: string; fee: string; status: string; created_at: string;
}

export default function OrdersIndex({ orders, filters }: { orders: { data: Order[]; current_page: number; last_page: number; total: number }; filters: { status?: string; side?: string; pair?: string } }) {
  return (
    <AdminLayout>
      <Head title="Admin - Orders" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Trade Orders</h1>
        <p className="text-zinc-500 text-sm font-medium">Inspect and manage all platform trade orders.</p>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="p-4 border-b border-[#1A1A1A] flex gap-2 flex-wrap">
          {['', 'open', 'filled', 'cancelled'].map(s => (
            <Link key={s} href={route('admin.orders.index', s ? { status: s } : {})}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${(filters.status ?? '') === s ? 'bg-white text-black' : 'bg-[#0A0A0A] text-zinc-400 hover:text-white'}`}>{s || 'All'}</Link>
          ))}
          <div className="w-px bg-[#1A1A1A] mx-1" />
          {['', 'buy', 'sell'].map(s => (
            <Link key={'s'+s} href={route('admin.orders.index', s ? { side: s } : {})}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${(filters.side ?? '') === s ? 'bg-white text-black' : 'bg-[#0A0A0A] text-zinc-400 hover:text-white'}`}>{s || 'All Sides'}</Link>
          ))}
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead><tr className="border-b border-[#1A1A1A]">
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">User</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Side</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Pair</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Amount</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Price</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Total</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-center">Status</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Date</th>
            </tr></thead>
            <tbody className="divide-y divide-[#0A0A0A]">
              {orders.data.map(o => (
                <tr key={o.id} className="hover:bg-[#151515] transition-colors">
                  <td className="px-6 py-4"><span className="text-sm font-bold text-white">{o.user?.name ?? 'Deleted User'}</span></td>
                  <td className="px-6 py-4"><span className={`text-xs font-bold px-2 py-1 rounded-full ${o.side === 'buy' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'}`}>{o.side}</span></td>
                  <td className="px-6 py-4 text-sm text-zinc-400">{o.pair}</td>
                  <td className="px-6 py-4 text-right"><span className="text-sm font-bold text-white font-mono">{Number(o.amount).toFixed(4)}</span></td>
                  <td className="px-6 py-4 text-right text-sm text-zinc-400 font-mono">{o.price ? `$${Number(o.price).toFixed(2)}` : 'Market'}</td>
                  <td className="px-6 py-4 text-right text-sm text-zinc-400 font-mono">${Number(o.total).toFixed(2)}</td>
                  <td className="px-6 py-4 text-center">
                    <span className={`text-xs font-bold px-2 py-1 rounded-full ${o.status === 'filled' ? 'bg-emerald-500/10 text-emerald-500' : o.status === 'open' ? 'bg-blue-500/10 text-blue-500' : 'bg-zinc-500/10 text-zinc-400'}`}>{o.status}</span>
                  </td>
                  <td className="px-6 py-4 text-xs text-zinc-500 font-mono">{o.created_at}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </AdminLayout>
  );
}
