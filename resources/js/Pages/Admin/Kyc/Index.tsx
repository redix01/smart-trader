import { Head, Link } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

interface Submission {
  id: number; user: { id: number; name: string; email: string };
  document_type: string; status: string; submitted_at: string;
}

export default function KycIndex({ submissions, filters }: { submissions: { data: Submission[]; current_page: number; last_page: number; total: number }; filters: { status?: string } }) {
  return (
    <AdminLayout>
      <Head title="Admin - KYC" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">KYC Submissions</h1>
        <p className="text-zinc-500 text-sm font-medium">Review and approve identity verification requests.</p>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="p-4 border-b border-[#1A1A1A] flex flex-wrap gap-2">
          {['', 'pending', 'approved', 'rejected'].map(s => (
            <Link key={s} href={route('admin.kyc.index', s ? { status: s } : {})}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all ${(filters.status ?? '') === s ? 'bg-white text-black' : 'bg-[#0A0A0A] text-zinc-400 hover:text-white'}`}>{s || 'All'}</Link>
          ))}
        </div>
        <div className="overflow-x-auto">
        <table className="w-full text-left">
          <thead><tr className="border-b border-[#1A1A1A]">
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">User</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Document</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Status</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Date</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Actions</th>
          </tr></thead>
          <tbody className="divide-y divide-[#0A0A0A]">
            {submissions.data.map(s => (
              <tr key={s.id} className="hover:bg-[#151515] transition-colors">
                <td className="px-6 py-4"><span className="text-sm font-bold text-white">{s.user.name}</span><br /><span className="text-xs text-zinc-500">{s.user.email}</span></td>
                <td className="px-6 py-4 text-xs text-zinc-400">{s.document_type}</td>
                <td className="px-6 py-4"><span className={`text-xs font-bold px-2 py-1 rounded-full ${s.status === 'approved' ? 'bg-emerald-500/10 text-emerald-500' : s.status === 'rejected' ? 'bg-rose-500/10 text-rose-500' : 'bg-amber-500/10 text-amber-500'}`}>{s.status}</span></td>
                <td className="px-6 py-4 text-xs text-zinc-500 font-mono">{s.submitted_at}</td>
                <td className="px-6 py-4 text-right"><Link href={route('admin.kyc.show', s.id)} className="text-xs font-bold text-blue-500 hover:text-blue-400">View</Link></td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>
      </div>
    </AdminLayout>
  );
}
