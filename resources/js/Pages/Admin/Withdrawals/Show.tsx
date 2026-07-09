import { Head, Link, router, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { ArrowLeft, Building, Check, CheckCircle, Clock, Copy, Globe, Wallet, X, XCircle } from 'lucide-react';
import { useState } from 'react';

export default function WithdrawalShow({ withdrawal }: { withdrawal: any }) {
  const [copied, setCopied] = useState(false);
  const { post, processing } = useForm({});

  const declineWithdrawal = () => {
    const reason = prompt('Decline withdrawal reason:');

    if (!reason?.trim()) {
      return;
    }

    router.post(route('admin.withdrawals.reject', withdrawal.id), { reason: reason.trim() }, { preserveScroll: true });
  };

  const handleCopy = () => {
    const walletAddress = withdrawal.destination_details?.wallet_address;
    if (walletAddress) {
      navigator.clipboard.writeText(walletAddress);
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    }
  };

  const dest = withdrawal.destination_details || {};
  const isCrypto = !!dest.wallet_address;
  const isBank = !!dest.bank_name;
  const isPaypal = !!dest.paypal_email;

  const statusColors: Record<string, string> = {
    pending: 'bg-amber-500/10 text-amber-500',
    approved: 'bg-emerald-500/10 text-emerald-500',
    rejected: 'bg-rose-500/10 text-rose-500',
  };

  const statusIcons: Record<string, React.ReactNode> = {
    pending: <Clock size={14} />,
    approved: <CheckCircle size={14} />,
    rejected: <XCircle size={14} />,
  };

  return (
    <AdminLayout>
      <Head title="Admin - Withdrawal Detail" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Withdrawal Details</h1>
        <p className="text-zinc-500 text-sm font-medium">Review withdrawal request details and approve or reject.</p>
      </header>
      <div className="mb-4">
        <Link href={route('admin.withdrawals.index')} className="text-sm text-zinc-400 hover:text-white flex items-center gap-1 w-fit"><ArrowLeft size={14} /> Back to Withdrawals</Link>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div className="lg:col-span-2 space-y-6">
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 md:p-8 space-y-6">
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Amount</p>
                <p className="text-2xl font-bold text-white font-mono">${withdrawal.amount}</p>
              </div>
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Fee</p>
                <p className="text-2xl font-bold text-white font-mono">${Number(withdrawal.fee || 0).toFixed(2)}</p>
              </div>
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Net Amount</p>
                <p className="text-2xl font-bold text-emerald-400 font-mono">${withdrawal.net_amount}</p>
              </div>
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Currency</p>
                <p className="text-2xl font-bold text-white">{withdrawal.currency}</p>
              </div>
            </div>

            <div className="h-px bg-[#1A1A1A]" />

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Method</p>
                <p className="text-white font-medium flex items-center gap-2">
                  {isCrypto ? <Wallet size={16} className="text-zinc-400" /> : isBank ? <Building size={16} className="text-zinc-400" /> : <Globe size={16} className="text-zinc-400" />}
                  {withdrawal.method}
                </p>
              </div>
              <div>
                <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Status</p>
                <span className={`inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full ${statusColors[withdrawal.status] || 'bg-zinc-500/10 text-zinc-500'}`}>
                  {statusIcons[withdrawal.status]} {withdrawal.status}
                </span>
              </div>
            </div>

            <div className="h-px bg-[#1A1A1A]" />

            <div>
              <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3">Destination Details</p>

              {isCrypto && (
                <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4 md:p-5">
                  <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Wallet Address</p>
                  <div className="flex items-start gap-3">
                    <span className="font-mono text-sm text-zinc-300 break-all leading-relaxed flex-1 min-w-0">{dest.wallet_address}</span>
                    <button
                      onClick={handleCopy}
                      className={`flex-shrink-0 p-2.5 rounded-xl transition-all ${copied ? 'bg-emerald-500/20 text-emerald-500' : 'bg-white/5 text-zinc-400 hover:text-white hover:bg-white/10'}`}
                      title="Copy wallet address"
                    >
                      {copied ? <Check size={16} /> : <Copy size={16} />}
                    </button>
                  </div>
                  {copied && <p className="text-[10px] text-emerald-500 font-medium mt-2">Copied to clipboard</p>}
                </div>
              )}

              {isBank && (
                <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4 md:p-5 space-y-4">
                  {dest.bank_name && (
                    <div>
                      <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Bank Name</p>
                      <p className="text-sm text-zinc-300">{dest.bank_name}</p>
                    </div>
                  )}
                  {dest.account_name && (
                    <div>
                      <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Account Name</p>
                      <p className="text-sm text-zinc-300">{dest.account_name}</p>
                    </div>
                  )}
                  {dest.account_number && (
                    <div>
                      <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Account Number</p>
                      <p className="text-sm text-zinc-300 font-mono">{dest.account_number}</p>
                    </div>
                  )}
                  {dest.routing_number && (
                    <div>
                      <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Routing Number</p>
                      <p className="text-sm text-zinc-300 font-mono">{dest.routing_number}</p>
                    </div>
                  )}
                </div>
              )}

              {isPaypal && (
                <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4 md:p-5">
                  <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">PayPal Email</p>
                  <p className="text-sm text-zinc-300 font-mono">{dest.paypal_email}</p>
                </div>
              )}
            </div>
          </div>
        </div>

        <div className="space-y-6">
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
            <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-3">User</p>
            <p className="text-white font-medium">{withdrawal.user.name}</p>
            <p className="text-zinc-400 text-sm mt-1">{withdrawal.user.email}</p>
            <p className="text-xs text-zinc-500 mt-3">
              Submitted {new Date(withdrawal.created_at).toLocaleDateString('en-US', {
                year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit',
              })}
            </p>
          </div>

          {withdrawal.admin_notes && (
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
              <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-2">Admin Notes</p>
              <p className="text-sm text-zinc-300">{withdrawal.admin_notes}</p>
            </div>
          )}

          {withdrawal.status === 'pending' && (
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 space-y-3">
              <p className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest mb-1">Actions</p>
              <button
                onClick={() => post(route('admin.withdrawals.approve', withdrawal.id))}
                disabled={processing}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-emerald-500/10 text-emerald-500 rounded-xl text-sm font-bold hover:bg-emerald-500 hover:text-black transition-all disabled:opacity-50"
              >
                <Check size={16} /> Approve
              </button>
              <button
                onClick={declineWithdrawal}
                disabled={processing}
                className="w-full flex items-center justify-center gap-2 px-4 py-3 bg-rose-500/10 text-rose-500 rounded-xl text-sm font-bold hover:bg-rose-500 hover:text-white transition-all disabled:opacity-50"
              >
                <X size={16} /> Decline
              </button>
            </div>
          )}
        </div>
      </div>
    </AdminLayout>
  );
}
