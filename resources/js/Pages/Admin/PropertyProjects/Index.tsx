import { Head, useForm, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Plus, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';

interface Project {
  id: number; title: string; region: string | null; description: string | null; strategy: string | null;
  min_investment: number; target_roi: number | null; status: string; image: string | null;
  media: string | null; disclosure: string | null; is_active: boolean; investments_count: number;
}

export default function PropertyProjectsIndex({ projects }: { projects: { data: Project[] } }) {
  const [editing, setEditing] = useState<Project | null>(null);
  const [showCreate, setShowCreate] = useState(false);

  return (
    <AdminLayout>
      <Head title="Admin - Property Projects" />
      <header className="mb-8 flex items-start justify-between gap-4">
        <div className="flex flex-col gap-1">
          <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Property Projects</h1>
          <p className="text-zinc-500 text-sm font-medium">Manage real estate investment projects and availability.</p>
        </div>
        <button onClick={() => setShowCreate(true)} className="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm hover:bg-emerald-500 hover:text-black transition-all flex-shrink-0"><Plus size={16} /> Add Project</button>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="overflow-x-auto">
        <table className="w-full text-left">
          <thead><tr className="border-b border-[#1A1A1A]">
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Title</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Region</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Strategy</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Min Investment</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Target ROI</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-center">Status</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Actions</th>
          </tr></thead>
          <tbody className="divide-y divide-[#0A0A0A]">
            {projects.data.map(project => (
              <tr key={project.id} className="hover:bg-[#151515] transition-colors">
                <td className="px-6 py-4"><span className="text-sm font-bold text-white">{project.title}</span></td>
                <td className="px-6 py-4 text-sm text-zinc-400">{project.region ?? '-'}</td>
                <td className="px-6 py-4 text-sm text-zinc-400">{project.strategy ?? '-'}</td>
                <td className="px-6 py-4 text-right text-sm text-zinc-400">${project.min_investment}</td>
                <td className="px-6 py-4 text-right"><span className="text-emerald-500 font-bold">{project.target_roi ?? '-'}%</span></td>
                <td className="px-6 py-4 text-center"><span className={`text-xs font-bold px-2 py-1 rounded-full ${project.status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : project.status === 'draft' ? 'bg-zinc-500/10 text-zinc-400' : project.status === 'completed' ? 'bg-blue-500/10 text-blue-500' : 'bg-rose-500/10 text-rose-500'}`}>{project.status}</span></td>
                <td className="px-6 py-4 text-right">
                  <button onClick={() => setEditing(project)} className="p-2 text-blue-500 hover:bg-blue-500/10 rounded-lg"><Pencil size={14} /></button>
                  <button onClick={() => { if (confirm('Delete?')) router.delete(route('admin.property-projects.destroy', project.id)); }} className="p-2 text-rose-500 hover:bg-rose-500/10 rounded-lg"><Trash2 size={14} /></button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>
      </div>
      {(showCreate || editing) && <ProjectForm project={editing} onClose={() => { setShowCreate(false); setEditing(null); }} />}
    </AdminLayout>
  );
}

function ProjectForm({ project, onClose }: { project: Project | null; onClose: () => void }) {
  const { data, setData, post, patch, processing, errors } = useForm(project ?? {
    title: '', region: '', description: '', strategy: '', min_investment: 0,
    target_roi: 0, image: '', media: '[]', disclosure: '', status: 'draft', is_active: true,
  });

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    project ? patch(route('admin.property-projects.update', project.id)) : post(route('admin.property-projects.store'), { onSuccess: onClose });
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onClick={onClose}>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 w-full max-w-lg overflow-y-auto max-h-[90vh]" onClick={e => e.stopPropagation()}>
        <h3 className="text-lg font-bold text-white mb-4">{project ? 'Edit Project' : 'Add Project'}</h3>
        <form onSubmit={submit} className="space-y-4">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Title</label><input value={data.title} onChange={e => setData('title', e.target.value)} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Region</label><input value={data.region ?? ''} onChange={e => setData('region', e.target.value)} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          {errors.title && <p className="text-xs text-rose-500">{errors.title}</p>}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Strategy</label><input value={data.strategy ?? ''} onChange={e => setData('strategy', e.target.value)} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Min Investment ($)</label><input value={data.min_investment} onChange={e => setData('min_investment', Number(e.target.value))} type="number" step="0.01" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div><label className="text-xs text-zinc-500">Target ROI (%)</label><input value={data.target_roi ?? ''} onChange={e => setData('target_roi', Number(e.target.value))} type="number" step="0.1" className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none" /></div>
            <div><label className="text-xs text-zinc-500">Status</label><select value={data.status} onChange={e => setData('status', e.target.value)} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white focus:outline-none">
              <option value="draft">Draft</option><option value="open">Open</option><option value="active">Active</option><option value="completed">Completed</option><option value="archived">Archived</option>
            </select></div>
          </div>
          <textarea value={data.description ?? ''} onChange={e => setData('description', e.target.value)} placeholder="Description" rows={3} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white placeholder-zinc-500 focus:outline-none" />
          <div className="flex items-center gap-2">
            <input checked={data.is_active} onChange={e => setData('is_active', e.target.checked)} type="checkbox" id="active" className="rounded bg-[#0A0A0A]" />
            <label htmlFor="active" className="text-sm text-white">Active</label>
          </div>
          <div className="flex gap-3 justify-end">
            <button type="button" onClick={onClose} className="px-4 py-2 text-sm text-zinc-400 hover:text-white">Cancel</button>
            <button type="submit" disabled={processing} className="px-4 py-2 bg-white text-black rounded-xl text-sm font-bold hover:bg-zinc-200 transition-all disabled:opacity-50">{project ? 'Update' : 'Create'}</button>
          </div>
        </form>
      </div>
    </div>
  );
}
