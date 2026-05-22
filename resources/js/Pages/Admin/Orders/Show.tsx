import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ArrowLeft } from 'lucide-react';

export default function OrderShow({ order }: { order: any }) {
  const { patch, setData, processing } = useForm({ status: '' });

  return (
    <AdminLayout>
      <Head title="Admin - Order Detail" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Order #{order.id}</h1>
        <p className="text-zinc-500 text-sm font-medium">Full order details, status, and management actions.</p>
      </header>
      <div className="mb-4">
        <Link href={route('admin.orders.index')} className="text-sm text-zinc-400 hover:text-white flex items-center gap-1"><ArrowLeft size={14} /> Back</Link>
      </div>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 max-w-2xl">
        <div className="grid grid-cols-2 gap-4">
          <div><p className="text-xs text-zinc-500">User</p><p className="text-white font-medium">{order.user.name} ({order.user.email})</p></div>
          <div><p className="text-xs text-zinc-500">Type</p><p className="text-white font-medium capitalize">{order.type}</p></div>
          <div><p className="text-xs text-zinc-500">Side</p><p className={`font-medium capitalize ${order.side === 'buy' ? 'text-emerald-500' : 'text-rose-500'}`}>{order.side}</p></div>
          <div><p className="text-xs text-zinc-500">Pair</p><p className="text-white font-medium">{order.pair}</p></div>
          <div><p className="text-xs text-zinc-500">Amount</p><p className="text-white font-medium font-mono">{Number(order.amount).toFixed(6)}</p></div>
          <div><p className="text-xs text-zinc-500">Filled</p><p className="text-white font-medium font-mono">{Number(order.filled).toFixed(6)}</p></div>
          <div><p className="text-xs text-zinc-500">Price</p><p className="text-white font-medium font-mono">{order.price ? `$${Number(order.price).toFixed(2)}` : 'Market'}</p></div>
          <div><p className="text-xs text-zinc-500">Total</p><p className="text-white font-medium font-mono">${Number(order.total).toFixed(2)}</p></div>
          <div><p className="text-xs text-zinc-500">Fee</p><p className="text-white font-medium font-mono">${Number(order.fee).toFixed(2)}</p></div>
          <div><p className="text-xs text-zinc-500">Status</p><p className="text-white font-medium capitalize">{order.status}</p></div>
          <div><p className="text-xs text-zinc-500">Created</p><p className="text-white font-medium">{order.created_at}</p></div>
          <div><p className="text-xs text-zinc-500">Closed</p><p className="text-white font-medium">{order.closed_at ?? 'N/A'}</p></div>
        </div>
        {order.status === 'open' && (
          <div className="mt-6 flex gap-3">
            <button onClick={() => { setData('status', 'filled'); setTimeout(() => patch(route('admin.orders.update', order.id)), 0); }} disabled={processing}
              className="px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm hover:bg-emerald-500 hover:text-black transition-all disabled:opacity-50">Mark Filled</button>
            <button onClick={() => { setData('status', 'cancelled'); setTimeout(() => patch(route('admin.orders.update', order.id)), 0); }} disabled={processing}
              className="px-4 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-sm hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50">Cancel Order</button>
          </div>
        )}
      </div>
    </AdminLayout>
  );
}
