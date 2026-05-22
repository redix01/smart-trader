import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface Plan {
  id: number; name: string; icon: string | null; roi_percent: number;
  duration_days: number; min_amount: number; max_amount: number | null; is_active: boolean; sort_order: number;
}

export default function MiningPlansIndex({ plans }: { plans: { data: Plan[] } }) {
  const [editing, setEditing] = useState<Plan | null>(null);
  const [showCreate, setShowCreate] = useState(false);

  return (
    <AdminLayout>
      <Head title="Admin - Mining Plans" />
      <header className="mb-8 flex items-start justify-between gap-4">
        <div className="flex flex-col gap-1">
          <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Mining Plans</h1>
          <p className="text-zinc-500 text-sm font-medium">Define cloud mining contract plans and activation states.</p>
        </div>
        <button onClick={() => setShowCreate(true)} className="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm hover:bg-emerald-500 hover:text-black transition-all flex-shrink-0"><Plus size={16} /> Create Plan</button>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="overflow-x-auto">
        <table className="w-full text-left">
          <thead><tr className="border-b border-[#1A1A1A]">
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Name</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">ROI</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Duration</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Min</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Max</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-center">Active</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Order</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Actions</th>
          </tr></thead>
          <tbody className="divide-y divide-[#0A0A0A]">
            {plans.data.map(plan => (
              <tr key={plan.id} className="hover:bg-[#151515] transition-colors">
                <td className="px-6 py-4"><span className="text-sm font-bold text-white">{plan.name}</span></td>
                <td className="px-6 py-4"><span className="text-amber-500 font-bold">{plan.roi_percent}%</span></td>
                <td className="px-6 py-4 text-sm text-zinc-400">{plan.duration_days}d</td>
                <td className="px-6 py-4 text-right text-sm text-zinc-400">${Number(plan.min_amount).toLocaleString()}</td>
                <td className="px-6 py-4 text-right text-sm text-zinc-400">{plan.max_amount ? `$${Number(plan.max_amount).toLocaleString()}` : '∞'}</td>
                <td className="px-6 py-4 text-center"><span className={`text-xs font-bold px-2 py-1 rounded-full ${plan.is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-zinc-500/10 text-zinc-400'}`}>{plan.is_active ? 'Yes' : 'No'}</span></td>
                <td className="px-6 py-4 text-right text-xs text-zinc-500">{plan.sort_order}</td>
                <td className="px-6 py-4 text-right">
                  <button onClick={() => setEditing(plan)} className="p-2 text-blue-500 hover:bg-blue-500/10 rounded-lg"><Pencil size={14} /></button>
                  <button onClick={() => { if (confirm('Delete?')) router.delete(route('admin.mining-plans.destroy', plan.id)); }} className="p-2 text-rose-500 hover:bg-rose-500/10 rounded-lg"><Trash2 size={14} /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>
      </div>
      {(showCreate || editing) && <MiningPlanForm plan={editing} onClose={() => { setShowCreate(false); setEditing(null); }} />}
    </AdminLayout>
  );
}

function MiningPlanForm({ plan, onClose }: { plan: Plan | null; onClose: () => void }) {
  const { data, setData, post, patch, processing, errors } = useForm(plan ?? {
    name: '', icon: '', roi_percent: 0, duration_days: 30, min_amount: 0, max_amount: '', is_active: true, sort_order: 0,
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    const payload = { ...data, max_amount: data.max_amount || null };
    setData(payload as typeof data);
    setTimeout(() => {
      plan ? patch(route('admin.mining-plans.update', plan.id)) : post(route('admin.mining-plans.store'), { onSuccess: onClose });
    }, 0);
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onClick={onClose}>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 w-full max-w-md overflow-y-auto max-h-[90vh]" onClick={e => e.stopPropagation()}>
        <h3 className="text-lg font-bold text-white mb-4">{plan ? 'Edit Mining Plan' : 'Create Mining Plan'}</h3>
        <form onSubmit={submit} className="space-y-4">
          <input value={data.name} onChange={e => setData('name', e.target.value)} placeholder="Plan name" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white placeholder-zinc-500 focus:outline-none" />
          {errors.name && <p className="text-xs text-rose-500">{errors.name}</p>}
          <input value={data.icon ?? ''} onChange={e => setData('icon', e.target.value)} placeholder="Icon class (e.g., starter, pro)" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white placeholder-zinc-500 focus:outline-none" />
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">ROI (%)</label><input value={data.roi_percent} onChange={e => setData('roi_percent', Number(e.target.value))} type="number" step="0.1" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Duration (days)</label><input value={data.duration_days} onChange={e => setData('duration_days', Number(e.target.value))} type="number" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Min Amount ($)</label><input value={data.min_amount} onChange={e => setData('min_amount', Number(e.target.value))} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Max Amount ($)</label><input value={data.max_amount ?? ''} onChange={e => setData('max_amount', e.target.value)} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div><label className="text-xs text-zinc-500">Sort Order</label><input value={data.sort_order} onChange={e => setData('sort_order', Number(e.target.value))} type="number" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          <div className="flex items-center gap-3">
            <input checked={data.is_active} onChange={e => setData('is_active', e.target.checked)} type="checkbox" id="active" className="rounded bg-[#0A0A0A] border-[#1A1A1A]" />
            <label htmlFor="active" className="text-sm text-white">Active</label>
          </div>
          <div className="flex gap-3 justify-end">
            <button type="button" onClick={onClose} className="px-4 py-2 text-sm text-zinc-400 hover:text-white">Cancel</button>
            <button type="submit" disabled={processing} className="px-4 py-2 bg-white text-black rounded-xl text-sm font-bold hover:bg-zinc-200 transition-all disabled:opacity-50">{plan ? 'Update' : 'Create'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
