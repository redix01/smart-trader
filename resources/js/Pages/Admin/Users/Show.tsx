import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ArrowLeft } from 'lucide-react';

interface Wallet { id: number; currency: string; balance: string; }
interface KycSubmission { id: number; document_type: string; status: string; submitted_at: string; }
interface Deposit { id: number; amount: string; currency: string; status: string; created_at: string; }
interface Withdrawal { id: number; amount: string; currency: string; status: string; created_at: string; }

interface UserDetail {
  id: number; name: string; email: string; account_tier: string; kyc_level: string;
  wallets: Wallet[]; kyc_submissions: KycSubmission[];
  deposits: Deposit[]; withdrawals: Withdrawal[];
}

export default function UsersShow({ user }: { user: UserDetail }) {
  const { patch, setData, delete: destroy, processing } = useForm({ account_tier: '' });

  return (
    <AdminLayout>
      <Head title={`Admin - User ${user.name}`} />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">{user.name}</h1>
        <p className="text-zinc-500 text-sm font-medium">User profile, wallets, KYC submissions, and account actions.</p>
      </header>

      <div className="mb-4">
        <Link href={route('admin.users.index')} className="text-sm text-zinc-400 hover:text-white flex items-center gap-1">
          <ArrowLeft size={14} /> Back to Users
        </Link>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 col-span-1">
          <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Profile</h3>
          <div className="space-y-3">
            <div><p className="text-xs text-zinc-500">Name</p><p className="text-white font-medium">{user.name}</p></div>
            <div><p className="text-xs text-zinc-500">Email</p><p className="text-white font-medium">{user.email}</p></div>
            <div><p className="text-xs text-zinc-500">Role</p><p className="text-white font-medium capitalize">{user.account_tier}</p></div>
            <div><p className="text-xs text-zinc-500">KYC Level</p><p className="text-white font-medium capitalize">{user.kyc_level}</p></div>
          </div>
          <div className="mt-6 flex gap-2">
            <button onClick={() => { setData('account_tier', user.account_tier === 'admin' ? 'user' : 'admin'); setTimeout(() => patch(route('admin.users.update', user.id)), 0); }} disabled={processing}
              className="px-4 py-2 bg-blue-500/10 text-blue-500 rounded-xl text-sm hover:bg-blue-500 hover:text-white transition-all disabled:opacity-50">
              Toggle Admin
            </button>
            <button onClick={() => { if (confirm('Delete this user?')) destroy(route('admin.users.destroy', user.id)); }} disabled={processing}
              className="px-4 py-2 bg-rose-500/10 text-rose-500 rounded-xl text-sm hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50">
              Delete
            </button>
          </div>
        </div>

        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
          <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Wallets</h3>
          {user.wallets.length > 0 ? (
            <div className="space-y-2">
              {user.wallets.map(w => (
                <div key={w.id} className="flex justify-between py-2 border-b border-[#1A1A1A] last:border-0">
                  <span className="text-white font-medium">{w.currency}</span>
                  <span className="text-white font-mono">{w.balance}</span>
                </div>
              ))}
            </div>
          ) : <p className="text-zinc-500 text-sm">No wallets</p>}
        </div>

        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
          <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">Latest Activity</h3>
          <div className="space-y-3">
            {user.deposits.map(d => (
              <div key={d.id} className="flex justify-between text-sm">
                <span className="text-emerald-500">+${d.amount} {d.currency}</span>
                <span className={`text-xs ${d.status === 'completed' ? 'text-emerald-500' : 'text-amber-500'}`}>{d.status}</span>
              </div>
            ))}
            {user.withdrawals.map(w => (
              <div key={w.id} className="flex justify-between text-sm">
                <span className="text-rose-500">-${w.amount} {w.currency}</span>
                <span className={`text-xs ${w.status === 'completed' ? 'text-emerald-500' : 'text-amber-500'}`}>{w.status}</span>
              </div>
            ))}
            {user.deposits.length === 0 && user.withdrawals.length === 0 && <p className="text-zinc-500 text-sm">No activity</p>}
          </div>
        </div>
      </div>

      {user.kyc_submissions.length > 0 && (
        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
          <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">KYC Submissions</h3>
          <table className="w-full text-left">
            <thead><tr className="border-b border-[#1A1A1A]">
              <th className="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase">Document</th>
              <th className="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase">Status</th>
              <th className="px-4 py-3 text-[10px] font-bold text-zinc-500 uppercase">Date</th>
            </tr></thead>
            <tbody>{user.kyc_submissions.map(k => (
              <tr key={k.id} className="border-b border-[#0A0A0A]">
                <td className="px-4 py-3 text-sm text-white">{k.document_type}</td>
                <td className="px-4 py-3"><span className="text-xs font-bold px-2 py-1 rounded-full bg-amber-500/10 text-amber-500">{k.status}</span></td>
                <td className="px-4 py-3 text-xs text-zinc-500">{k.submitted_at}</td>
              </tr>
            ))}</tbody>
          </table>
        </div>
      )}
    </AdminLayout>
  );
}
