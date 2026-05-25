import { Head, Link, router, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { motion } from 'motion/react';
import {
  ShieldCheck, ArrowUpCircle, ArrowDownCircle,
  Check, X, Eye, Clock,
} from 'lucide-react';

interface PendingItem {
  id: number;
  user_name: string;
  method?: string;
  amount?: string;
  currency?: string;
  has_proof?: boolean;
  document_type?: string;
  submitted_at: string;
}

interface AdminDashboardProps {
  pendingKyc: PendingItem[];
  pendingDeposits: PendingItem[];
  pendingWithdrawals: PendingItem[];
}

function StatCard({ label, count, icon, color, href }: {
  label: string; count: number; icon: React.ReactNode; color: string; href: string;
}) {
  return (
    <Link href={route(href)} className="bg-[#111] border border-[#1A1A1A] p-5 rounded-2xl hover:border-zinc-700 transition-all group">
      <div className="flex items-center justify-between mb-4">
        <div className={`p-2.5 rounded-xl ${color}/10`}>
          <div className={color}>{icon}</div>
        </div>
        <span className="text-[10px] font-bold text-zinc-600 uppercase tracking-widest group-hover:text-zinc-400 transition-colors">View all</span>
      </div>
      <div className="flex items-end justify-between">
        <p className="text-3xl font-bold text-white tracking-tight font-mono">{count}</p>
        <p className="text-xs font-medium text-zinc-500">{label}</p>
      </div>
    </Link>
  );
}

function SectionTable({ title, icon, items, type, color }: {
  title: string; icon: React.ReactNode; items: PendingItem[]; type: 'kyc' | 'deposit' | 'withdrawal'; color: string;
}) {
  const { post, processing } = useForm({});
  const detailRoute = type === 'kyc' ? 'admin.kyc.show'
    : type === 'deposit' ? 'admin.deposits.show'
    : 'admin.withdrawals.show';
  const indexRoute = type === 'kyc' ? 'admin.kyc.index'
    : type === 'deposit' ? 'admin.deposits.index'
    : 'admin.withdrawals.index';
  const rejectRoute = type === 'kyc' ? 'admin.kyc.reject'
    : type === 'deposit' ? 'admin.deposits.reject'
    : 'admin.withdrawals.reject';
  const declineItem = (item: PendingItem) => {
    const reason = prompt(`Decline ${type} reason:`);

    if (!reason?.trim()) {
      return;
    }

    router.post(route(rejectRoute, item.id), { reason: reason.trim() }, { preserveScroll: true });
  };

  return (
    <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden flex flex-col">
      <div className="px-6 py-5 border-b border-[#1A1A1A] flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className={`p-2 rounded-xl ${color}/10`}>
            <div className={color}>{icon}</div>
          </div>
          <div>
            <h2 className="text-base font-bold text-white">{title}</h2>
            {items.length > 0 && (
              <p className="text-xs text-zinc-500 font-medium">{items.length} pending {type === 'kyc' ? 'verifications' : 'requests'}</p>
            )}
          </div>
        </div>
        {items.length > 0 && (
          <span className="px-3 py-1 bg-amber-500/10 text-amber-500 text-xs font-bold rounded-full">{items.length}</span>
        )}
      </div>

      {items.length > 0 ? (
        <>
          <div className="overflow-x-auto flex-1">
            <table className="w-full text-left">
              <thead>
                <tr className="border-b border-[#1A1A1A]">
                  <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest">User</th>
                  {type !== 'kyc' && <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Method</th>}
                  {type === 'kyc' && <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Document</th>}
                  {type !== 'kyc' && <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest text-right">Amount</th>}
                  <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest text-right">Date</th>
                  <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest text-center">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-[#0A0A0A]">
                {items.map((item) => (
                  <tr key={item.id} className="hover:bg-[#151515] transition-colors group">
                    <td className="px-6 py-4">
                      <div className="flex items-center gap-3">
                        <div className="w-8 h-8 rounded-full bg-gradient-to-br from-zinc-600 to-zinc-700 flex items-center justify-center text-xs font-bold text-white">
                          {item.user_name?.charAt(0)?.toUpperCase() || '?'}
                        </div>
                        <span className="text-sm font-bold text-white">{item.user_name}</span>
                      </div>
                    </td>
                    {type !== 'kyc' && (
                      <td className="px-6 py-4">
                        <span className="text-xs px-2.5 py-1 bg-[#0A0A0A] rounded-lg text-zinc-400 font-medium">{item.method}</span>
                      </td>
                    )}
                    {type === 'kyc' && (
                      <td className="px-6 py-4">
                        <span className="text-xs px-2.5 py-1 bg-[#0A0A0A] rounded-lg text-zinc-400 font-medium capitalize">{item.document_type ?? 'N/A'}</span>
                      </td>
                    )}
                    {type !== 'kyc' && (
                      <td className="px-6 py-4 text-right">
                        <span className="text-sm font-bold text-white font-mono">${item.amount} <span className="text-xs text-zinc-500">{item.currency}</span></span>
                      </td>
                    )}
                    <td className="px-6 py-4 text-right text-xs text-zinc-600 font-mono">{item.submitted_at}</td>
                    <td className="px-6 py-4">
                      <div className="flex items-center justify-center gap-1.5">
                        <button
                          onClick={() => post(
                            type === 'kyc' ? route('admin.kyc.approve', item.id)
                            : type === 'deposit' ? route('admin.deposits.approve', item.id)
                            : route('admin.withdrawals.approve', item.id)
                          )}
                          disabled={processing}
                          className="p-2 bg-emerald-500/10 text-emerald-500 rounded-xl hover:bg-emerald-500 hover:text-black transition-all disabled:opacity-50"
                          title="Approve"
                        >
                          <Check size={15} />
                        </button>
                        <button
                          onClick={() => declineItem(item)}
                          disabled={processing}
                          className="p-2 bg-rose-500/10 text-rose-500 rounded-xl hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50"
                          title="Decline"
                        >
                          <X size={15} />
                        </button>
                        <Link
                          href={route(detailRoute, item.id)}
                          className="p-2 bg-blue-500/10 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition-all"
                          title="View details"
                        >
                          <Eye size={15} />
                        </Link>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
          <div className="px-6 py-3.5 border-t border-[#1A1A1A] bg-[#0C0C0C]">
            <Link
              href={route(indexRoute)}
              className="text-xs font-bold text-zinc-500 hover:text-white transition-colors flex items-center gap-1.5 w-fit"
            >
              View all {type === 'kyc' ? 'KYC submissions' : type === 'deposit' ? 'deposits' : 'withdrawals'}
              <span className="text-zinc-700">→</span>
            </Link>
          </div>
        </>
      ) : (
        <div className="flex-1 flex items-center justify-center p-12">
          <div className="text-center">
            <div className={`w-14 h-14 ${type === 'kyc' ? 'bg-emerald-500/10' : type === 'deposit' ? 'bg-emerald-500/10' : 'bg-emerald-500/10'} rounded-2xl flex items-center justify-center mx-auto mb-4`}>
              <Check size={28} className="text-emerald-500" />
            </div>
            <p className="text-sm font-semibold text-white mb-1">All clear</p>
            <p className="text-xs text-zinc-500">No pending {type === 'kyc' ? 'KYC' : type} requests</p>
          </div>
        </div>
      )}
    </div>
  );
}

export default function AdminDashboard({ pendingKyc, pendingDeposits, pendingWithdrawals }: AdminDashboardProps) {
  const totalPending = pendingKyc.length + pendingDeposits.length + pendingWithdrawals.length;

  return (
    <AdminLayout>
      <Head title="Admin Dashboard" />
      <header className="mb-8 flex flex-col gap-1">
        <motion.div initial={{ opacity: 0, y: -10 }} animate={{ opacity: 1, y: 0 }} transition={{ duration: 0.3 }}>
          <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Admin Dashboard</h1>
          <p className="text-zinc-500 text-sm font-medium">Operations overview — review, approve, and manage platform activity.</p>
        </motion.div>
      </header>

      <motion.div
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.3, delay: 0.1 }}
        className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8"
      >
        <StatCard label="Pending KYC" count={pendingKyc.length} icon={<ShieldCheck size={20} />} color="text-blue-500" href="admin.kyc.index" />
        <StatCard label="Pending Deposits" count={pendingDeposits.length} icon={<ArrowUpCircle size={20} />} color="text-emerald-500" href="admin.deposits.index" />
        <StatCard label="Pending Withdrawals" count={pendingWithdrawals.length} icon={<ArrowDownCircle size={20} />} color="text-rose-500" href="admin.withdrawals.index" />
        <StatCard label="Total Pending" count={totalPending} icon={<Clock size={20} />} color="text-amber-500" href="admin.dashboard" />
      </motion.div>

      <motion.div
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.3, delay: 0.2 }}
        className="space-y-6"
      >
        <SectionTable
          title="KYC Reviews"
          icon={<ShieldCheck size={18} />}
          items={pendingKyc}
          type="kyc"
          color="text-blue-500"
        />
        <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
          <SectionTable
            title="Deposit Approvals"
            icon={<ArrowUpCircle size={18} />}
            items={pendingDeposits}
            type="deposit"
            color="text-emerald-500"
          />
          <SectionTable
            title="Withdrawal Approvals"
            icon={<ArrowDownCircle size={18} />}
            items={pendingWithdrawals}
            type="withdrawal"
            color="text-rose-500"
          />
        </div>
      </motion.div>
    </AdminLayout>
  );
}
