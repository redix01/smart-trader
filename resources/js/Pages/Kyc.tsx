import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';

interface Submission {
  id: number;
  status: string;
  id_document_type: string | null;
  rejection_reason: string | null;
  submitted_at: string | null;
}

export default function Kyc({ submission }: { submission: Submission | null }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    id_document_type: 'passport',
    id_document: null as File | null,
    selfie: null as File | null,
    address_proof: null as File | null,
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('kyc.store'), {
      forceFormData: true,
      onSuccess: () => reset(),
    });
  };

  return (
    <AppLayout>
      <Head title="KYC Verification" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">KYC Verification</h1>
        <p className="text-zinc-500 text-sm font-medium">Submit your identity documents for review.</p>
      </header>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 lg:p-8">
          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Document Type</label>
              <select value={data.id_document_type} onChange={e => setData('id_document_type', e.target.value)} className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl px-4 py-3 text-sm text-white outline-none focus:border-blue-600">
                <option value="passport">Passport</option>
                <option value="drivers_license">Driver&apos;s License</option>
                <option value="national_id">National ID</option>
              </select>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div className="space-y-2">
                <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">ID Document</label>
                <input type="file" onChange={e => setData('id_document', e.target.files?.[0] ?? null)} className="w-full text-sm text-zinc-300" />
                {errors.id_document && <p className="text-xs text-rose-500">{errors.id_document}</p>}
              </div>
              <div className="space-y-2">
                <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Selfie</label>
                <input type="file" onChange={e => setData('selfie', e.target.files?.[0] ?? null)} className="w-full text-sm text-zinc-300" />
                {errors.selfie && <p className="text-xs text-rose-500">{errors.selfie}</p>}
              </div>
            </div>

            <div className="space-y-2">
              <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Proof of Address</label>
              <input type="file" onChange={e => setData('address_proof', e.target.files?.[0] ?? null)} className="w-full text-sm text-zinc-300" />
              {errors.address_proof && <p className="text-xs text-rose-500">{errors.address_proof}</p>}
            </div>

            <button type="submit" disabled={processing} className="px-5 py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-500 transition-all disabled:opacity-50">
              {processing ? 'Submitting...' : 'Submit KYC'}
            </button>
          </form>
        </div>

        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 space-y-4">
          <h2 className="text-lg font-bold text-white">Status</h2>
          {submission ? (
            <div className="space-y-3 text-sm">
              <p className="text-zinc-400">Current status: <span className="text-white font-semibold capitalize">{submission.status}</span></p>
              <p className="text-zinc-400">Document type: <span className="text-white font-semibold capitalize">{submission.id_document_type ?? 'N/A'}</span></p>
              <p className="text-zinc-400">Submitted: <span className="text-white font-semibold">{submission.submitted_at ?? 'N/A'}</span></p>
              {submission.rejection_reason && <p className="text-zinc-400">Reason: <span className="text-rose-400">{submission.rejection_reason}</span></p>}
            </div>
          ) : (
            <p className="text-zinc-500 text-sm">No submission yet.</p>
          )}
        </div>
      </div>
    </AppLayout>
  );
}
