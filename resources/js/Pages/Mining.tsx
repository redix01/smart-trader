import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { motion } from 'motion/react';
import { Pickaxe, Zap, ChevronRight, ShieldCheck, Clock, DollarSign, TrendingUp, AlertTriangle } from 'lucide-react';

interface MiningPlan {
  id: number;
  name: string;
  icon: string | null;
  min: number;
  max: number;
  roi: string;
  roi_percent: number;
  duration: string;
  duration_days: number;
  color: string;
}

interface Subscription {
  id: number;
  plan_name: string;
  amount: number;
  earned: number;
  status: string;
  start_date: string;
  end_date: string;
}

interface MiningProps {
  plans: MiningPlan[];
  subscriptions: Subscription[];
  kycRequired: boolean;
}

export default function Mining({ plans, subscriptions, kycRequired }: MiningProps) {
  const { data, setData, post, processing, errors } = useForm({
    plan_id: 0,
    amount: '',
  });
  const [selectedPlan, setSelectedPlan] = useState<number | null>(null);
  const handleSubscribe = (planId: number) => {
    if (kycRequired) return;
    setSelectedPlan(planId === selectedPlan ? null : planId);
  };

  const selectedPlanData = plans.find(p => p.id === selectedPlan);

  return (
    <AppLayout>
      <Head title="Mining" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Mining</h1>
        <p className="text-zinc-500 text-sm font-medium">Cloud mining infrastructure investment plans.</p>
      </header>
      <div className="space-y-10">
        <div className="bg-gradient-to-r from-[#111] to-[#1A1A1A] border border-[#1F1F1F] p-8 lg:p-12 rounded-[40px] flex flex-col lg:flex-row items-center gap-12 relative overflow-hidden">
          <div className="relative z-10 space-y-6 lg:max-w-2xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 bg-blue-600/10 border border-blue-600/20 rounded-full">
              <div className="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse" />
              <span className="text-[10px] font-bold text-blue-500 uppercase tracking-widest">Hyper-Hash Cloud Active</span>
            </div>
            <h2 className="text-4xl font-bold text-white tracking-tight leading-tight">
              Next-Gen Cloud <span className="text-blue-500">Mining</span> Infrastructure
            </h2>
            <p className="text-zinc-500 text-lg leading-relaxed">Harness the power of our global data centers. No hardware required.</p>
            <div className="flex items-center gap-8 pt-4">
              <div className="space-y-1">
                <p className="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">Global Hashrate</p>
                <p className="text-2xl font-bold text-white font-mono">14.2 EH/s</p>
              </div>
              <div className="h-10 w-[1px] bg-[#222]"></div>
              <div className="space-y-1">
                <p className="text-zinc-600 text-[10px] font-bold uppercase tracking-widest">Network Diff.</p>
                <p className="text-2xl font-bold text-zinc-300 font-mono">82.1 T</p>
              </div>
            </div>
          </div>
          <div className="relative z-10 flex-1 flex justify-center">
            <div className="w-64 h-64 bg-blue-600/10 rounded-full flex items-center justify-center border border-blue-600/20 relative group">
              <Pickaxe size={120} className="text-blue-500 group-hover:rotate-12 transition-transform duration-500" />
              <div className="absolute inset-0 bg-blue-600/5 rounded-full animate-ping" />
            </div>
          </div>
          <div className="absolute top-[-100px] right-[-100px] w-[500px] h-[500px] bg-blue-600/5 rounded-full blur-[120px] pointer-events-none" />
        </div>

        {kycRequired && (
          <div className="bg-amber-500/10 border border-amber-500/20 rounded-3xl p-6 flex items-start gap-4">
            <AlertTriangle size={24} className="text-amber-500 shrink-0 mt-0.5" />
            <div>
              <h3 className="text-lg font-bold text-white mb-1">KYC Verification Required</h3>
              <p className="text-zinc-400 text-sm">Verify your identity to access mining investment plans.</p>
            </div>
          </div>
        )}

        <div>
          <h2 className="text-2xl font-bold text-white">Mining Plans</h2>
          <p className="text-zinc-500 mt-1">Select a contract that best fits your investment goals.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
          {plans.map((plan) => {
            const isSelected = selectedPlan === plan.id;
            return (
              <motion.div key={plan.id} whileHover={kycRequired ? {} : { y: -8 }}
                className={`bg-[#111] border rounded-[32px] overflow-hidden group transition-all flex flex-col shadow-xl ${
                  isSelected ? 'border-blue-600/50' : 'border-[#1A1A1A] hover:border-blue-600/30'
                } ${kycRequired ? 'opacity-60' : ''}`}>
                <div className={`p-8 bg-gradient-to-br ${plan.color} border-b border-[#1A1A1A] space-y-4`}>
                  <div className="flex items-center justify-between">
                    <h3 className="text-xl font-bold text-white">{plan.name}</h3>
                    <div className="p-2 bg-white/5 rounded-xl text-white/40"><Zap size={20} /></div>
                  </div>
                  <div className="space-y-1">
                    <p className="text-[10px] text-white/50 font-bold uppercase tracking-widest">Minimum Contract</p>
                    <p className="text-3xl font-black text-white tracking-tight">${plan.min.toLocaleString()}</p>
                  </div>
                </div>
                <div className="p-8 space-y-8 flex-1">
                  <div className="grid grid-cols-3 gap-4">
                    <div className="bg-[#0A0A0A] border border-[#1A1A1A] p-4 rounded-2xl flex flex-col items-center">
                      <p className="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Duration</p>
                      <p className="text-xs font-bold text-white font-mono">{plan.duration}</p>
                    </div>
                    <div className="bg-[#0A0A0A] border border-[#1A1A1A] p-4 rounded-2xl flex flex-col items-center">
                      <p className="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mb-1">ROI</p>
                      <p className="text-xs font-bold text-emerald-500 font-mono">{plan.roi}</p>
                    </div>
                    <div className="bg-[#0A0A0A] border border-[#1A1A1A] p-4 rounded-2xl flex flex-col items-center overflow-hidden">
                      <p className="text-[9px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Max</p>
                      <p className="text-[10px] font-bold text-white font-mono truncate w-full text-center">
                        {plan.max > 0 ? `$${plan.max.toLocaleString()}` : '∞'}
                      </p>
                    </div>
                  </div>
                  {isSelected && !kycRequired ? (
                    <form onSubmit={(e) => { e.preventDefault(); setData('plan_id', plan.id); post(route('mining.store')); }} className="space-y-4">
                      <div className="space-y-2">
                        <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Subscription Amount</label>
                        <div className="relative">
                          <input type="number" value={data.amount} onChange={(e) => setData('amount', e.target.value)}
                            min={plan.min} max={plan.max > 0 ? plan.max : undefined}
                            placeholder={String(plan.min)}
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl px-5 py-4 text-sm text-white focus:border-blue-600/50 outline-none transition-all shadow-inner font-mono" />
                          <div className="absolute right-5 top-1/2 -translate-y-1/2 text-[10px] font-bold text-zinc-600">USD</div>
                        </div>
                        <div className="flex justify-between text-[10px] text-zinc-500 px-1">
                          <span>Min: ${plan.min.toLocaleString()}</span>
                          {plan.max > 0 && <span>Max: ${plan.max.toLocaleString()}</span>}
                        </div>
                        <p className="text-[10px] text-zinc-600">
                          Est. return: ${((parseFloat(data.amount || '0') * plan.roi_percent / 100)).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ({plan.roi})
                        </p>
                      </div>
                      {errors?.amount && <p className="text-xs text-rose-500">{errors.amount}</p>}
                      {errors?.plan_id && <p className="text-xs text-rose-500">{errors.plan_id}</p>}
                      <button type="submit" disabled={processing}
                        className="w-full py-5 bg-blue-600 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-500 transition-all flex items-center justify-center gap-2 shadow-lg">
                        {processing ? 'Processing...' : <>Confirm Subscription <ChevronRight size={16} /></>}
                      </button>
                    </form>
                  ) : (
                    <button onClick={() => handleSubscribe(plan.id)} disabled={kycRequired}
                      className="w-full py-5 bg-[#0A0A0A] border border-white/5 text-white/50 rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all flex items-center justify-center gap-2 group-hover:bg-[#151515] disabled:cursor-not-allowed">
                      {kycRequired ? 'Verify KYC First' : 'Subscribe Now'} <ChevronRight size={16} />
                    </button>
                  )}
                  <div className="flex items-center gap-2 px-1 text-zinc-600">
                    <ShieldCheck size={14} className="text-emerald-500/50" />
                    <span className="text-[10px] font-medium tracking-wide">Automated Daily Earnings Payout</span>
                  </div>
                </div>
              </motion.div>
            );
          })}
        </div>

        {subscriptions.length > 0 && (
          <div>
            <div className="flex items-center gap-3 mb-6">
              <Clock size={22} className="text-blue-500" />
              <h2 className="text-2xl font-bold text-white">Your Contracts</h2>
            </div>
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left">
                  <thead>
                    <tr className="border-b border-[#1A1A1A]">
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Plan</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Amount</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Earned</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-center">Status</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Start</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">End</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#0A0A0A]">
                    {subscriptions.map(sub => (
                      <tr key={sub.id} className="hover:bg-[#151515] transition-colors">
                        <td className="px-6 py-4"><span className="text-sm font-bold text-white">{sub.plan_name}</span></td>
                        <td className="px-6 py-4 text-right"><span className="text-sm font-bold text-white font-mono">${sub.amount.toLocaleString()}</span></td>
                        <td className="px-6 py-4 text-right"><span className="text-sm font-bold text-emerald-500 font-mono">${sub.earned.toLocaleString()}</span></td>
                        <td className="px-6 py-4 text-center">
                          <span className={`text-xs font-bold px-2 py-1 rounded-full ${
                            sub.status === 'active' ? 'bg-emerald-500/10 text-emerald-500' :
                            sub.status === 'completed' ? 'bg-blue-500/10 text-blue-500' :
                            'bg-zinc-500/10 text-zinc-400'
                          }`}>{sub.status}</span>
                        </td>
                        <td className="px-6 py-4 text-xs text-zinc-500 font-mono">{sub.start_date}</td>
                        <td className="px-6 py-4 text-xs text-zinc-500 font-mono">{sub.end_date ?? 'Ongoing'}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}
      </div>
    </AppLayout>
  );
}
