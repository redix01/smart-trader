import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Modal from '@/Components/Modal';
import { ArrowUpRight, ShieldCheck, Info, ChevronDown, Building2, User, CreditCard, Landmark, CheckCircle2, XCircle } from 'lucide-react';

interface WithdrawProps {
  balance: number;
  history?: Array<{
    id: number;
    method: string;
    amount: string;
    fee: string;
    net: string;
    currency: string;
    status: string;
    date: string;
  }>;
}

export default function Withdraw({ balance, history = [] }: WithdrawProps) {
  const { data, setData, post, processing, errors, reset } = useForm({
    method: 'Bank Transfer',
    amount: '',
    currency: 'USD',
    destination: {
      bank_name: '',
      account_name: '',
      account_number: '',
      routing_number: '',
      wallet_address: '',
      paypal_email: '',
    },
  });

  const [confirmation, setConfirmation] = useState<{ status: 'success' | 'error'; message: string } | null>(null);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('withdraw.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset('amount', 'destination');
        setConfirmation({
          status: 'success',
          message: 'Your withdrawal request has been submitted and is now pending admin approval.',
        });
      },
      onError: (formErrors) => {
        const firstError = Object.values(formErrors)[0];
        setConfirmation({
          status: 'error',
          message: typeof firstError === 'string' ? firstError : 'Your withdrawal request could not be submitted. Please check the form and try again.',
        });
      },
    });
  };

  const netAmount = data.amount ? (parseFloat(data.amount) * 0.99).toFixed(2) : '0.00';
  const fee = data.amount ? (parseFloat(data.amount) * 0.01).toFixed(2) : '0.00';
  const isBankTransfer = data.method === 'Bank Transfer';
  const isCryptoWallet = data.method === 'Bitcoin Wallet' || data.method === 'Ethereum Wallet';
  const isPayPal = data.method === 'PayPal';
  const statusClass = (status: string) => status === 'approved'
    ? 'bg-emerald-500/10 text-emerald-500'
    : status === 'rejected'
      ? 'bg-rose-500/10 text-rose-500'
      : 'bg-amber-500/10 text-amber-500';

  return (
    <AppLayout>
      <Head title="Withdraw" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Withdraw</h1>
        <p className="text-zinc-500 text-sm font-medium">Submit a payout request to your preferred destination.</p>
      </header>
      <div className="max-w-4xl mx-auto space-y-8 py-4">
        <div className="bg-[#111] border border-[#1A1A1A] rounded-[40px] overflow-hidden shadow-2xl relative">
          <div className="absolute top-0 right-0 w-64 h-64 bg-rose-600/5 rounded-full blur-[100px] pointer-events-none" />
          <div className="p-8 lg:p-12 border-b border-[#1A1A1A]">
            <h2 className="text-2xl font-bold text-white flex items-center gap-3">
              <Landmark className="text-rose-500" size={28} />
              Withdrawal Request
            </h2>
            <p className="text-zinc-500 mt-2 font-medium">To make a withdrawal, select your balance, amount and verify the address.</p>
          </div>
          <div className="p-8 lg:p-12">
            <form onSubmit={handleSubmit} className="grid grid-cols-1 lg:grid-cols-2 gap-12">
              <div className="space-y-8">
                <div className="space-y-3">
                  <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Withdrawal Type</label>
                  <div className="relative group">
                    <select value={data.method} onChange={(e) => setData('method', e.target.value)}
                      className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl px-6 py-4 text-sm text-white appearance-none focus:border-rose-600/50 outline-none transition-all shadow-inner font-medium">
                      <option>Bank Transfer</option>
                      <option>Bitcoin Wallet</option>
                      <option>Ethereum Wallet</option>
                      <option>PayPal</option>
                    </select>
                    <ChevronDown className="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-600 pointer-events-none group-focus-within:text-rose-500 transition-colors" size={20} />
                  </div>
                </div>
                <div className="space-y-3">
                  <div className="flex justify-between items-end">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Amount to Withdraw</label>
                    <div className="flex items-center gap-2 text-[10px] font-bold">
                      <span className="text-zinc-500">Balance:</span>
                      <span className="text-emerald-500">${balance.toLocaleString()}</span>
                    </div>
                  </div>
                  <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl flex items-center pr-6 focus-within:border-rose-600/50 transition-all shadow-inner">
                    <div className="pl-6 pr-4 py-4 border-r border-[#1A1A1A] text-zinc-400 font-bold">$</div>
                    <input type="number" value={data.amount} onChange={(e) => setData('amount', e.target.value)} placeholder="0.00"
                      className="flex-1 bg-transparent border-none outline-none px-4 py-4 text-xl font-bold text-white placeholder-zinc-700 font-mono" />
                    <button type="button" onClick={() => setData('amount', String(balance))} className="text-[10px] font-black text-rose-500 uppercase tracking-widest bg-rose-500/10 px-3 py-1.5 rounded-lg hover:bg-rose-500 hover:text-white transition-all">MAX</button>
                  </div>
                  {errors.amount && <p className="text-xs text-rose-500">{errors.amount}</p>}
                </div>
                <div className="bg-amber-500/5 border border-amber-500/10 p-6 rounded-3xl space-y-3">
                  <div className="flex items-center gap-2 text-amber-500">
                    <Info size={18} />
                    <span className="text-sm font-bold">Processing Time</span>
                  </div>
                  <p className="text-xs text-amber-200/60 leading-relaxed font-medium">
                    Standard withdrawals are processed within 24-48 business hours.
                  </p>
                </div>
              </div>
              <div className="space-y-8">
                <div className="space-y-6">
                  {isBankTransfer && (
                    <>
                      <div className="space-y-3">
                        <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Bank Name</label>
                        <div className="relative group">
                          <input type="text" value={data.destination.bank_name} onChange={(e) => setData('destination', { ...data.destination, bank_name: e.target.value })}
                            placeholder="e.g. Chase Bank"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl pl-12 pr-6 py-4 text-sm text-white focus:border-rose-600/50 outline-none transition-all shadow-inner placeholder:text-zinc-700" />
                          <Building2 className="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 group-focus-within:text-rose-500 transition-colors" size={20} />
                        </div>
                      </div>
                      <div className="space-y-3">
                        <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Account Name</label>
                        <div className="relative group">
                          <input type="text" value={data.destination.account_name} onChange={(e) => setData('destination', { ...data.destination, account_name: e.target.value })}
                            placeholder="Card holder name"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl pl-12 pr-6 py-4 text-sm text-white focus:border-rose-600/50 outline-none transition-all shadow-inner placeholder:text-zinc-700" />
                          <User className="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 group-focus-within:text-rose-500 transition-colors" size={20} />
                        </div>
                      </div>
                      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="space-y-3">
                          <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Account Number</label>
                          <div className="relative group">
                            <input type="text" value={data.destination.account_number} onChange={(e) => setData('destination', { ...data.destination, account_number: e.target.value })}
                              placeholder="99201928"
                              className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl pl-12 pr-6 py-4 text-sm text-white focus:border-rose-600/50 outline-none transition-all shadow-inner font-mono placeholder:text-zinc-700" />
                            <CreditCard className="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-600 group-focus-within:text-rose-500 transition-colors" size={20} />
                          </div>
                        </div>
                        <div className="space-y-3">
                          <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Routing Number</label>
                          <input type="text" value={data.destination.routing_number} onChange={(e) => setData('destination', { ...data.destination, routing_number: e.target.value })}
                            placeholder="29930192"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl px-6 py-4 text-sm text-white focus:border-rose-600/50 outline-none transition-all shadow-inner font-mono placeholder:text-zinc-700" />
                        </div>
                      </div>
                    </>
                  )}

                  {isCryptoWallet && (
                    <div className="space-y-3">
                      <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Wallet Address</label>
                      <input type="text" value={data.destination.wallet_address} onChange={(e) => setData('destination', { ...data.destination, wallet_address: e.target.value })}
                        placeholder={data.method === 'Bitcoin Wallet' ? 'bc1...' : '0x...'}
                        className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl px-6 py-4 text-sm text-white focus:border-rose-600/50 outline-none transition-all shadow-inner font-mono placeholder:text-zinc-700" />
                    </div>
                  )}

                  {isPayPal && (
                    <div className="space-y-3">
                      <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">PayPal Email</label>
                      <input type="email" value={data.destination.paypal_email} onChange={(e) => setData('destination', { ...data.destination, paypal_email: e.target.value })}
                        placeholder="name@example.com"
                        className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl px-6 py-4 text-sm text-white focus:border-rose-600/50 outline-none transition-all shadow-inner placeholder:text-zinc-700" />
                    </div>
                  )}
                </div>
                <div className="pt-4">
                  <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-[32px] p-6 mb-6">
                    <div className="flex justify-between items-center text-xs mb-4">
                      <span className="text-zinc-500 font-medium">Estimated payout</span>
                      <span className="text-white font-bold">${netAmount}</span>
                    </div>
                    <div className="flex justify-between items-center text-xs">
                      <span className="text-zinc-500 font-medium">Transaction fee (1%)</span>
                      <span className="text-rose-500 font-bold">-${fee}</span>
                    </div>
                  </div>
                  <button type="submit" disabled={processing}
                    className="w-full py-5 bg-rose-600 text-white font-bold rounded-2xl hover:bg-rose-500 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-[0_4px_30px_rgba(225,29,72,0.25)] disabled:opacity-50">
                    {processing ? 'Processing...' : 'Request Payout'} <ArrowUpRight size={20} />
                  </button>
                </div>
              </div>
            </form>
          </div>
          <div className="bg-[#0D0D0D] p-8 flex flex-col md:flex-row items-center gap-6 justify-between border-t border-[#1A1A1A]">
            <div className="flex items-center gap-4">
              <div className="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-500">
                <ShieldCheck size={28} />
              </div>
              <div>
                <p className="text-sm font-bold text-white">Secure Institutional Grade Processing</p>
                <p className="text-xs text-zinc-500 mt-0.5">Your funds are protected by multi-signature vaults.</p>
              </div>
            </div>
            <div className="flex items-center gap-6">
              <div className="text-center md:text-right">
                <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">24h Volume</p>
                <p className="text-sm font-bold text-zinc-300">$1.2M+</p>
              </div>
              <div className="h-8 w-[1px] bg-[#1A1A1A]"></div>
              <div className="text-center md:text-right">
                <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">Active Requests</p>
                <p className="text-sm font-bold text-zinc-300">142</p>
              </div>
            </div>
          </div>
        </div>

        <div className="bg-[#111] border border-[#1A1A1A] rounded-[32px] overflow-hidden">
          <div className="px-6 py-5 border-b border-[#1A1A1A] flex items-center justify-between">
            <div>
              <h2 className="text-base font-bold text-white">Withdrawal History</h2>
              <p className="text-xs text-zinc-500 font-medium">Track requests submitted for admin review.</p>
            </div>
            <span className="text-[10px] font-bold text-zinc-600 uppercase tracking-widest">{history.length} requests</span>
          </div>
          {history.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="w-full text-left">
                <thead>
                  <tr className="border-b border-[#1A1A1A]">
                    <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Method</th>
                    <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest text-right">Amount</th>
                    <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest text-right">Fee</th>
                    <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest text-right">Net</th>
                    <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest">Status</th>
                    <th className="px-6 py-3.5 text-[10px] font-bold text-zinc-600 uppercase tracking-widest text-right">Date</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-[#0A0A0A]">
                  {history.map((withdrawal) => (
                    <tr key={withdrawal.id} className="hover:bg-[#151515] transition-colors">
                      <td className="px-6 py-4 text-sm font-bold text-white">{withdrawal.method}</td>
                      <td className="px-6 py-4 text-right text-sm font-bold text-white font-mono">${withdrawal.amount} {withdrawal.currency}</td>
                      <td className="px-6 py-4 text-right text-sm text-rose-500 font-mono">${withdrawal.fee}</td>
                      <td className="px-6 py-4 text-right text-sm text-zinc-300 font-mono">${withdrawal.net}</td>
                      <td className="px-6 py-4">
                        <span className={`text-[10px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider ${statusClass(withdrawal.status)}`}>
                          {withdrawal.status}
                        </span>
                      </td>
                      <td className="px-6 py-4 text-right text-xs text-zinc-500 font-mono">{withdrawal.date}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div className="px-6 py-8 text-sm text-zinc-500 font-medium">No withdrawal requests yet.</div>
          )}
        </div>
      </div>

      <Modal show={confirmation !== null} maxWidth="sm" onClose={() => setConfirmation(null)}>
        <div className="p-8 text-center space-y-5">
          <div className={`w-16 h-16 mx-auto rounded-full flex items-center justify-center ${confirmation?.status === 'success' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'}`}>
            {confirmation?.status === 'success' ? <CheckCircle2 size={32} /> : <XCircle size={32} />}
          </div>
          <div className="space-y-2">
            <h3 className="text-lg font-bold text-white">
              {confirmation?.status === 'success' ? 'Withdrawal Request Sent' : 'Withdrawal Request Rejected'}
            </h3>
            <p className="text-sm text-zinc-400 font-medium">{confirmation?.message}</p>
          </div>
          <button
            onClick={() => setConfirmation(null)}
            className={`w-full py-3.5 rounded-2xl font-bold text-sm transition-all ${confirmation?.status === 'success' ? 'bg-emerald-600 text-white hover:bg-emerald-500' : 'bg-rose-600 text-white hover:bg-rose-500'}`}
          >
            {confirmation?.status === 'success' ? 'Done' : 'Try Again'}
          </button>
        </div>
      </Modal>
    </AppLayout>
  );
}
