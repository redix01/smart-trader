import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ArrowLeft } from 'lucide-react';

export default function SwapShow({ swap }: { swap: any }) {
  return (
    <AdminLayout>
      <Head title="Admin - Swap Detail" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Swap #{swap.id}</h1>
        <p className="text-zinc-500 text-sm font-medium">Detailed view of this swap transaction.</p>
      </header>
      <div className="mb-4">
        <Link href={route('admin.swaps.index')} className="text-sm text-zinc-400 hover:text-white flex items-center gap-1"><ArrowLeft size={14} /> Back</Link>
      </div>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 max-w-2xl">
        <div className="grid grid-cols-2 gap-4">
          <div><p className="text-xs text-zinc-500">User</p><p className="text-white font-medium">{swap.user.name} ({swap.user.email})</p></div>
          <div><p className="text-xs text-zinc-500">Status</p><p className="text-white font-medium capitalize">{swap.status}</p></div>
          <div><p className="text-xs text-zinc-500">From</p><p className="text-white font-medium">{swap.from_currency}</p></div>
          <div><p className="text-xs text-zinc-500">To</p><p className="text-white font-medium">{swap.to_currency}</p></div>
          <div><p className="text-xs text-zinc-500">From Amount</p><p className="text-white font-medium font-mono">${Number(swap.from_amount).toFixed(2)}</p></div>
          <div><p className="text-xs text-zinc-500">To Amount</p><p className="text-white font-medium font-mono">{Number(swap.to_amount).toFixed(6)}</p></div>
          <div><p className="text-xs text-zinc-500">Rate</p><p className="text-white font-medium font-mono">{Number(swap.rate).toFixed(6)}</p></div>
          <div><p className="text-xs text-zinc-500">Fee</p><p className="text-white font-medium font-mono">${Number(swap.fee).toFixed(2)}</p></div>
          <div><p className="text-xs text-zinc-500">Created</p><p className="text-white font-medium">{swap.created_at}</p></div>
        </div>
      </div>
    </AdminLayout>
  );
}
