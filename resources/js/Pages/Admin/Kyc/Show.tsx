import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ArrowLeft, Check, X } from 'lucide-react';

export default function KycShow({ submission }: { submission: any }) {
  const { post, setData, processing } = useForm({ reason: '' });

  return (
    <AdminLayout>
      <Head title="Admin - KYC Detail" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">KYC Details</h1>
        <p className="text-zinc-500 text-sm font-medium">Review the submitted identity document and take action.</p>
      </header>
      <div className="mb-4">
        <Link href={route('admin.kyc.index')} className="text-sm text-zinc-400 hover:text-white flex items-center gap-1"><ArrowLeft size={14} /> Back</Link>
      </div>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 max-w-2xl">
        <div className="space-y-4">
          <div><p className="text-xs text-zinc-500">User</p><p className="text-white font-medium">{submission.user.name} ({submission.user.email})</p></div>
          <div><p className="text-xs text-zinc-500">Document Type</p><p className="text-white font-medium">{submission.document_type}</p></div>
          <div><p className="text-xs text-zinc-500">Status</p><p className="text-white font-medium capitalize">{submission.status}</p></div>
          <div><p className="text-xs text-zinc-500">Submitted</p><p className="text-white font-medium">{submission.submitted_at}</p></div>
        </div>
        {submission.status === 'pending' && (
          <div className="mt-6 flex gap-3">
            <button onClick={() => post(route('admin.kyc.approve', submission.id))} disabled={processing}
              className="flex items-center gap-2 px-4 py-2 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm hover:bg-emerald-500 hover:text-black transition-all disabled:opacity-50">
              <Check size={16} /> Approve
            </button>
            <button onClick={() => { const r = prompt('Rejection reason:'); if (r) { setData('reason', r); setTimeout(() => post(route('admin.kyc.reject', submission.id)), 0); } }} disabled={processing}
              className="flex items-center gap-2 px-4 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-sm hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50">
              <X size={16} /> Reject
            </button>
          </div>
        )}
      </div>
    </AdminLayout>
  );
}
