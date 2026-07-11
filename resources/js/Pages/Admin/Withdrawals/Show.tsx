import { Head, Link, router, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import {
  ArrowLeft, Building, Check, CheckCircle, Clock, Copy, Globe,
  Hash, User as UserIcon, Wallet, X, XCircle,
} from 'lucide-react';
import { useState } from 'react';

export default function WithdrawalShow({ withdrawal }: { withdrawal: any }) {
  const [copied, setCopied] = useState<string | null>(null);
  const { post, processing } = useForm({});

  const declineWithdrawal = () => {
    const reason = prompt('Decline withdrawal reason:');
    if (!reason?.trim()) return;
    router.post(route('admin.withdrawals.reject', withdrawal.id), { reason: reason.trim() }, { preserveScroll: true });
  };

  const handleCopy = (value: string, label: string) => {
    navigator.clipboard.writeText(value);
    setCopied(label);
    setTimeout(() => setCopied(null), 2000);
  };

  const dest = withdrawal.destination_details || {};
  const destFields: { label: string; key: string; mono?: boolean; copyable?: boolean }[] = [
    ...(dest.wallet_address ? [{ label: 'Wallet Address', key: 'wallet_address', mono: true, copyable: true }] : []),
    ...(dest.bank_name ? [{ label: 'Bank Name', key: 'bank_name' }] : []),
    ...(dest.account_name ? [{ label: 'Account Name', key: 'account_name' }] : []),
    ...(dest.account_number ? [{ label: 'Account Number', key: 'account_number', mono: true }] : []),
    ...(dest.routing_number ? [{ label: 'Routing Number', key: 'routing_number', mono: true }] : []),
    ...(dest.paypal_email ? [{ label: 'PayPal Email', key: 'paypal_email', mono: true }] : []),
    ...(dest.swift_code ? [{ label: 'SWIFT Code', key: 'swift_code', mono: true }] : []),
    ...(dest.iban ? [{ label: 'IBAN', key: 'iban', mono: true }] : []),
    ...(dest.sort_code ? [{ label: 'Sort Code', key: 'sort_code', mono: true }] : []),
  ];

  const statusConfig: Record<string, { color: string; icon: React.ReactNode }> = {
    pending: { color: 'bg-amber-500/10 text-amber-500 border-amber-500/20', icon: <Clock size={14} /> },
    approved: { color: 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20', icon: <CheckCircle size={14} /> },
    rejected: { color: 'bg-rose-500/10 text-rose-500 border-rose-500/20', icon: <XCircle size={14} /> },
  };

  const status = statusConfig[withdrawal.status] || statusConfig.pending;

  const methodIcon = dest.wallet_address
    ? <Wallet size={18} className="text-zinc-400" />
    : dest.bank_name
      ? <Building size={18} className="text-zinc-400" />
      : <Globe size={18} className="text-zinc-400" />;

  const fmtDate = (date: string | null | undefined) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString('en-US', {
      year: 'numeric', month: 'long', day: 'numeric',
      hour: '2-digit', minute: '2-digit',
    });
  };

  return (
    <AdminLayout>
      <Head title="Admin - Withdrawal Detail" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">
          Withdrawal #{withdrawal.id}
        </h1>
        <p className="text-zinc-500 text-sm font-medium">Review withdrawal request details and approve or reject.</p>
      </header>
      <div className="mb-4">
        <Link href={route('admin.withdrawals.index')} className="text-sm text-zinc-400 hover:text-white flex items-center gap-1 w-fit">
          <ArrowLeft size={14} /> Back to Withdrawals
        </Link>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 md:p-8 space-y-6">
            <div className="flex flex-wrap items-start justify-between gap-4">
              <div className="flex items-center gap-3">
                <div className="w-12 h-12 bg-white/5 rounded-2xl flex items-center justify-center">
                  <Hash size={20} className="text-zinc-400" />
                </div>
                <div>
                  <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Withdrawal ID</p>
                  <p className="text-lg font-bold text-white font-mono">#{withdrawal.id}</p>
                </div>
              </div>
              <span className={`inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full border ${status.color}`}>
                {status.icon} {withdrawal.status}
              </span>
            </div>

            <div className="h-px bg-[#1A1A1A]" />

            <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
              <div className="bg-[#0A0A0A] rounded-2xl p-4">
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Amount</p>
                <p className="text-xl font-bold text-white font-mono">${withdrawal.amount}</p>
              </div>
              <div className="bg-[#0A0A0A] rounded-2xl p-4">
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Fee</p>
                <p className="text-xl font-bold text-rose-400 font-mono">-${Number(withdrawal.fee || 0).toFixed(2)}</p>
              </div>
              <div className="bg-[#0A0A0A] rounded-2xl p-4">
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Net Amount</p>
                <p className="text-xl font-bold text-emerald-400 font-mono">${withdrawal.net_amount}</p>
              </div>
              <div className="bg-[#0A0A0A] rounded-2xl p-4">
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Currency</p>
                <p className="text-xl font-bold text-white">{withdrawal.currency}</p>
              </div>
            </div>

            <div className="h-px bg-[#1A1A1A]" />

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Withdrawal Method</p>
                <p className="text-white font-medium flex items-center gap-2">
                  {methodIcon}
                  {withdrawal.method}
                </p>
              </div>
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Submitted</p>
                <p className="text-white font-medium">{fmtDate(withdrawal.created_at)}</p>
              </div>
              {withdrawal.approved_at && (
                <div>
                  <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">
                    {withdrawal.status === 'rejected' ? 'Declined' : 'Approved'}
                  </p>
                  <p className="text-white font-medium">{fmtDate(withdrawal.approved_at)}</p>
                </div>
              )}
              {withdrawal.approver && (
                <div>
                  <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">
                    {withdrawal.status === 'rejected' ? 'Declined By' : 'Approved By'}
                  </p>
                  <p className="text-white font-medium flex items-center gap-1.5">
                    <UserIcon size={14} className="text-zinc-500" />
                    {withdrawal.approver.name}
                  </p>
                </div>
              )}
            </div>
          </div>

          {destFields.length > 0 && (
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 md:p-8 space-y-5">
              <div className="flex items-center gap-2">
                <div className="w-8 h-8 bg-white/5 rounded-xl flex items-center justify-center">
                  {methodIcon}
                </div>
                <div>
                  <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Payment Destination</p>
                  <p className="text-sm text-white font-medium">{withdrawal.method}</p>
                </div>
              </div>
              <div className="h-px bg-[#1A1A1A]" />
              <div className="space-y-4">
                {destFields.map((field) => {
                  const value = dest[field.key];
                  const isCopied = copied === field.label;
                  return (
                    <div key={field.key} className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4">
                      <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0 flex-1">
                          <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1.5">{field.label}</p>
                          <p className={`text-sm text-zinc-300 break-all leading-relaxed ${field.mono ? 'font-mono' : ''}`}>{value}</p>
                        </div>
                        {field.copyable && (
                          <button
                            onClick={() => handleCopy(value, field.label)}
                            className={`flex-shrink-0 p-2.5 rounded-xl transition-all ${
                              isCopied
                                ? 'bg-emerald-500/20 text-emerald-500'
                                : 'bg-white/5 text-zinc-400 hover:text-white hover:bg-white/10'
                            }`}
                            title={`Copy ${field.label}`}
                          >
                            {isCopied ? <Check size={16} /> : <Copy size={16} />}
                          </button>
                        )}
                      </div>
                      {isCopied && <p className="text-[10px] text-emerald-500 font-medium mt-2">Copied to clipboard</p>}
                    </div>
                  );
                })}
              </div>
            </div>
          )}

          {!destFields.length && (
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 md:p-8">
              <p className="text-sm text-zinc-500">No payment destination details available.</p>
            </div>
          )}
        </div>

        <div className="space-y-6">
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
            <div className="flex items-center gap-2 mb-4">
              <div className="w-8 h-8 bg-white/5 rounded-xl flex items-center justify-center">
                <UserIcon size={16} className="text-zinc-400" />
              </div>
              <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">User</p>
            </div>
            <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4 space-y-3">
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-0.5">Name</p>
                <p className="text-sm font-bold text-white">{withdrawal.user?.name ?? 'Deleted User'}</p>
              </div>
              <div className="h-px bg-[#1A1A1A]" />
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-0.5">Email</p>
                <p className="text-sm text-zinc-300 break-all">{withdrawal.user?.email ?? 'N/A'}</p>
              </div>
            </div>
          </div>

          {withdrawal.admin_notes && (
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
              <div className="flex items-center gap-2 mb-4">
                <div className="w-8 h-8 bg-rose-500/10 rounded-xl flex items-center justify-center">
                  <XCircle size={16} className="text-rose-400" />
                </div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Admin Notes</p>
              </div>
              <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4">
                <p className="text-sm text-zinc-300 leading-relaxed">{withdrawal.admin_notes}</p>
              </div>
            </div>
          )}

          {withdrawal.status === 'pending' && (
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
              <div className="flex items-center gap-2 mb-4">
                <div className="w-8 h-8 bg-blue-500/10 rounded-xl flex items-center justify-center">
                  <Check size={16} className="text-blue-400" />
                </div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Actions</p>
              </div>
              <div className="space-y-3">
                <button
                  onClick={() => post(route('admin.withdrawals.approve', withdrawal.id))}
                  disabled={processing}
                  className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm font-bold hover:bg-emerald-500 hover:text-black transition-all disabled:opacity-50"
                >
                  <CheckCircle size={16} /> Approve Withdrawal
                </button>
                <button
                  onClick={declineWithdrawal}
                  disabled={processing}
                  className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-rose-500/10 text-rose-500 rounded-xl text-sm font-bold hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50"
                >
                  <XCircle size={16} /> Decline Withdrawal
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
}
