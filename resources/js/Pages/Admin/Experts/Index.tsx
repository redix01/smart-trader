import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface Expert {
  id: number; name: string; avatar: string | null; bio: string | null;
  win_rate: number | null; total_volume: number | null; profit_share: number | null;
  status: string; is_active: boolean; subscriptions_count: number;
}

export default function ExpertsIndex({ experts }: { experts: { data: Expert[] } }) {
  const [editing, setEditing] = useState<Expert | null>(null);
  const [showCreate, setShowCreate] = useState(false);

  return (
    <AdminLayout>
      <Head title="Admin - Experts" />
      <header className="mb-8 flex items-start justify-between gap-4">
        <div className="flex flex-col gap-1">
          <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Experts</h1>
          <p className="text-zinc-500 text-sm font-medium">Manage copy-trading expert profiles and strategy listings.</p>
        </div>
        <button onClick={() => setShowCreate(true)} className="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm hover:bg-emerald-500 hover:text-black transition-all flex-shrink-0"><Plus size={16} /> Add Expert</button>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="overflow-x-auto">
        <table className="w-full text-left">
          <thead><tr className="border-b border-[#1A1A1A]">
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Name</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Win Rate</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Volume</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Profit Share</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Subscribers</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-center">Status</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Actions</th>
          </tr></thead>
          <tbody className="divide-y divide-[#0A0A0A]">
            {experts.data.map(expert => (
              <tr key={expert.id} className="hover:bg-[#151515] transition-colors">
                <td className="px-6 py-4"><span className="text-sm font-bold text-white">{expert.name}</span></td>
                <td className="px-6 py-4"><span className="text-emerald-500 font-bold">{expert.win_rate ?? '-'}%</span></td>
                <td className="px-6 py-4"><span className="font-bold text-blue-500">${expert.total_volume ?? 0}</span></td>
                <td className="px-6 py-4 text-sm text-zinc-400">{expert.profit_share ?? '-'}%</td>
                <td className="px-6 py-4 text-right text-sm text-zinc-400">{expert.subscriptions_count}</td>
                <td className="px-6 py-4 text-center">
                  <span className={`text-xs font-bold px-2 py-1 rounded-full ${expert.status === 'verified' ? 'bg-blue-500/10 text-blue-500' : expert.status === 'pro' ? 'bg-purple-500/10 text-purple-500' : expert.is_active ? 'bg-emerald-500/10 text-emerald-500' : 'bg-zinc-500/10 text-zinc-400'}`}>
                    {expert.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-right">
                  <button onClick={() => setEditing(expert)} className="p-2 text-blue-500 hover:bg-blue-500/10 rounded-lg"><Pencil size={14} /></button>
                  <button onClick={() => { if (confirm('Delete?')) router.delete(route('admin.experts.destroy', expert.id)); }} className="p-2 text-rose-500 hover:bg-rose-500/10 rounded-lg"><Trash2 size={14} /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>
      </div>
      {(showCreate || editing) && <ExpertForm expert={editing} onClose={() => { setShowCreate(false); setEditing(null); }} />}
    </AdminLayout>
  );
}

function ExpertForm({ expert, onClose }: { expert: Expert | null; onClose: () => void }) {
  const { data, setData, post, patch, processing, errors } = useForm(expert ?? {
    name: '', avatar: '', bio: '', win_rate: 0, total_volume: 0, profit_share: 0, status: 'verified', is_active: true,
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    expert ? patch(route('admin.experts.update', expert.id)) : post(route('admin.experts.store'), { onSuccess: onClose });
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onClick={onClose}>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 w-full max-w-md overflow-y-auto max-h-[90vh]" onClick={e => e.stopPropagation()}>
        <h3 className="text-lg font-bold text-white mb-4">{expert ? 'Edit Expert' : 'Add Expert'}</h3>
        <form onSubmit={submit} className="space-y-4">
          <input value={data.name} onChange={e => setData('name', e.target.value)} placeholder="Expert name" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white placeholder-zinc-500 focus:outline-none" />
          {errors.name && <p className="text-xs text-rose-500">{errors.name}</p>}
          <textarea value={data.bio ?? ''} onChange={e => setData('bio', e.target.value)} placeholder="Bio" rows={3} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white placeholder-zinc-500 focus:outline-none" />
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Win Rate (%)</label><input value={data.win_rate ?? ''} onChange={e => setData('win_rate', Number(e.target.value))} type="number" step="0.1" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Profit Share (%)</label><input value={data.profit_share ?? ''} onChange={e => setData('profit_share', Number(e.target.value))} type="number" step="0.1" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Total Volume ($)</label><input value={data.total_volume ?? ''} onChange={e => setData('total_volume', Number(e.target.value))} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Avatar URL</label><input value={data.avatar ?? ''} onChange={e => setData('avatar', e.target.value)} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div className="flex items-center gap-4">
            <label className="flex items-center gap-2"><input checked={data.is_active} onChange={e => setData('is_active', e.target.checked)} type="checkbox" className="rounded bg-[#0A0A0A]" /><span className="text-sm text-white">Active</span></label>
            <div><label className="text-xs text-zinc-500">Status</label><select value={data.status} onChange={e => setData('status', e.target.value)} className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-3 py-1 text-sm text-white">
              <option value="verified">Verified</option><option value="pro">Pro</option><option value="institutional">Institutional</option><option value="top-tier">Top Tier</option>
            </select></div>
          </div>
          <div className="flex gap-3 justify-end">
            <button type="button" onClick={onClose} className="px-4 py-2 text-sm text-zinc-400 hover:text-white">Cancel</button>
            <button type="submit" disabled={processing} className="px-4 py-2 bg-white text-black rounded-xl text-sm font-bold hover:bg-zinc-200 transition-all disabled:opacity-50">{expert ? 'Update' : 'Create'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
