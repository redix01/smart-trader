import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface Method {
  id: number; currency: string; network: string; label: string; wallet_address: string;
  icon: string | null; min_amount: number; max_amount: number | null;
  fee_fixed: number; fee_percent: number; is_active: boolean; sort_order: number;
}

export default function DepositMethodsIndex({ methods }: { methods: { data: Method[] } }) {
  const [editing, setEditing] = useState<Method | null>(null);
  const [showCreate, setShowCreate] = useState(false);

  return (
    <AdminLayout>
      <Head title="Admin - Deposit Methods" />
      <header className="mb-8 flex items-start justify-between gap-4">
        <div className="flex flex-col gap-1">
          <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Deposit Methods</h1>
          <p className="text-zinc-500 text-sm font-medium">Configure supported currencies, networks, and wallet addresses.</p>
        </div>
        <button onClick={() => setShowCreate(true)} className="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm hover:bg-emerald-500 hover:text-black transition-all flex-shrink-0"><Plus size={16} /> Add Method</button>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead><tr className="border-b border-[#1A1A1A]">
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Currency</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Network</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Label</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Wallet Address</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Min</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Fee</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-center">Active</th>
              <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Actions</th>
            </tr></thead>
            <tbody className="divide-y divide-[#0A0A0A]">
              {methods.data.map(m => (
                <tr key={m.id} className="hover:bg-[#151515] transition-colors">
                  <td className="px-6 py-4"><span className="text-sm font-bold text-white">{m.currency}</span></td>
                  <td className="px-6 py-4 text-sm text-zinc-400">{m.network}</td>
                  <td className="px-6 py-4 text-sm text-zinc-400">{m.label}</td>
                  <td className="px-6 py-4 text-xs text-zinc-500 font-mono max-w-[200px] truncate">{m.wallet_address}</td>
                  <td className="px-6 py-4 text-right text-sm text-zinc-400">{m.min_amount > 0 ? `$${m.min_amount}` : '-'}</td>
                  <td className="px-6 py-4 text-right text-sm text-zinc-400">{m.fee_percent > 0 ? `${m.fee_percent}%` : m.fee_fixed > 0 ? `$${m.fee_fixed}` : '-'}</td>
                  <td className="px-6 py-4 text-center"><span className={`text-xs font-bold px-2 py-1 rounded-full ${m.is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-zinc-500/10 text-zinc-400'}`}>{m.is_active ? 'Yes' : 'No'}</span></td>
                  <td className="px-6 py-4 text-right">
                    <button onClick={() => setEditing(m)} className="p-2 text-blue-500 hover:bg-blue-500/10 rounded-lg"><Pencil size={14} /></button>
                    <button onClick={() => { if (confirm('Delete?')) router.delete(route('admin.deposit-methods.destroy', m.id)); }} className="p-2 text-rose-500 hover:bg-rose-500/10 rounded-lg"><Trash2 size={14} /></button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
      {(showCreate || editing) && <MethodForm method={editing} onClose={() => { setShowCreate(false); setEditing(null); }} />}
    </AdminLayout>
  );
}

function MethodForm({ method, onClose }: { method: Method | null; onClose: () => void }) {
  const { data, setData, post, patch, processing, errors } = useForm(method ?? {
    currency: '', network: '', label: '', wallet_address: '', icon: '',
    min_amount: 0, max_amount: '', fee_fixed: 0, fee_percent: 0, is_active: true, sort_order: 0,
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    const payload = { ...data, max_amount: data.max_amount || null };
    setData(payload as typeof data);
    setTimeout(() => {
      method ? patch(route('admin.deposit-methods.update', method.id)) : post(route('admin.deposit-methods.store'), { onSuccess: onClose });
    }, 0);
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50" onClick={onClose}>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 w-full max-w-lg" onClick={e => e.stopPropagation()}>
        <h3 className="text-lg font-bold text-white mb-4">{method ? 'Edit Method' : 'Add Method'}</h3>
        <form onSubmit={submit} className="space-y-4">
          <div className="grid grid-cols-3 gap-4">
            <div><label className="text-xs text-zinc-500">Currency</label><input value={data.currency} onChange={e => setData('currency', e.target.value)} placeholder="BTC" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Network</label><input value={data.network} onChange={e => setData('network', e.target.value)} placeholder="ERC20" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Sort Order</label><input value={data.sort_order} onChange={e => setData('sort_order', Number(e.target.value))} type="number" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div><label className="text-xs text-zinc-500">Label</label><input value={data.label} onChange={e => setData('label', e.target.value)} placeholder="Bitcoin (BTC)" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          {errors.label && <p className="text-xs text-rose-500">{errors.label}</p>}
          <div><label className="text-xs text-zinc-500">Wallet Address</label><input value={data.wallet_address} onChange={e => setData('wallet_address', e.target.value)} placeholder="1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white font-mono focus:outline-none" /></div>
          {errors.wallet_address && <p className="text-xs text-rose-500">{errors.wallet_address}</p>}
          <div className="grid grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Min Amount ($)</label><input value={data.min_amount} onChange={e => setData('min_amount', Number(e.target.value))} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Max Amount ($)</label><input value={data.max_amount ?? ''} onChange={e => setData('max_amount', e.target.value)} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Fixed Fee ($)</label><input value={data.fee_fixed} onChange={e => setData('fee_fixed', Number(e.target.value))} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Fee (%)</label><input value={data.fee_percent} onChange={e => setData('fee_percent', Number(e.target.value))} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div className="flex items-center gap-3">
            <input checked={data.is_active} onChange={e => setData('is_active', e.target.checked)} type="checkbox" id="active" className="rounded bg-[#0A0A0A] border-[#1A1A1A]" />
            <label htmlFor="active" className="text-sm text-white">Active</label>
          </div>
          <div className="flex gap-3 justify-end">
            <button type="button" onClick={onClose} className="px-4 py-2 text-sm text-zinc-400 hover:text-white">Cancel</button>
            <button type="submit" disabled={processing} className="px-4 py-2 bg-white text-black rounded-xl text-sm font-bold hover:bg-zinc-200 transition-all disabled:opacity-50">{method ? 'Update' : 'Create'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
