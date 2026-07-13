import { useState } from 'react';
import { Head, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { motion } from 'motion/react';
import { Users, TrendingUp, Check, Star, ChevronRight, BarChart3, XCircle } from 'lucide-react';

interface Expert {
  id: number;
  name: string;
  avatar: string | null;
  win_rate: string;
  profit_share: string;
  status: string;
  total_volume: string;
}

interface Subscription {
  id: number;
  expert_name: string;
  allocated: string;
  current_value: string;
  status: string;
  date: string;
}

interface CopyExpertsProps {
  experts: Expert[];
  subscriptions: Subscription[];
}

export default function CopyExperts({ experts, subscriptions }: CopyExpertsProps) {
  const { data, setData, post, processing } = useForm({
    expert_id: 0,
    amount: '',
  });
  const [selectedExpert, setSelectedExpert] = useState<number | null>(null);

  const handleCopy = (expertId: number) => {
    setSelectedExpert(expertId === selectedExpert ? null : expertId);
  };

  const cancel = (sub: Subscription) => {
    if (confirm(`Cancel your subscription to ${sub.expert_name}? No profits will be transferred.`)) {
      router.delete(route('experts.destroy', sub.id));
    }
  };

  const activeSubscriptions = subscriptions.filter(s => s.status === 'active');

  return (
    <AppLayout>
      <Head title="Copy Experts" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Copy Experts</h1>
        <p className="text-zinc-500 text-sm font-medium">Automate your portfolio by copying top traders.</p>
      </header>
      <div className="space-y-10">
        <div className="bg-[#111] border border-[#1A1A1A] rounded-[40px] p-8 lg:p-12 relative overflow-hidden group">
          <div className="relative z-10 flex flex-col md:flex-row items-center gap-12">
            <div className="space-y-6 flex-1">
              <div className="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                <span className="w-1.5 h-1.5 bg-emerald-500 rounded-full" />
                <span className="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Mirror Protocol Active</span>
              </div>
              <h2 className="text-4xl font-bold text-white tracking-tight leading-tight">
                Follow the <span className="text-emerald-500">Market Makers</span>
              </h2>
              <p className="text-zinc-500 text-lg leading-relaxed max-w-xl">Automate your portfolio by copying top-performing traders in real-time.</p>
              <div className="flex flex-wrap gap-4 pt-4">
                {[
                  { label: 'Avg Monthly ROI', value: '+24.5%', icon: TrendingUp },
                  { label: 'Total Copiers', value: '12K+', icon: Users },
                ].map((idx, i) => (
                  <div key={i} className="bg-[#0A0A0A] border border-[#1A1A1A] px-6 py-4 rounded-2xl flex items-center gap-4">
                    <div className="p-2 bg-emerald-500/10 rounded-lg text-emerald-500"><idx.icon size={20} /></div>
                    <div>
                      <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">{idx.label}</p>
                      <p className="text-lg font-bold text-white">{idx.value}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
            <div className="hidden lg:flex relative">
              <div className="w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl absolute -inset-4" />
              <BarChart3 size={180} className="text-emerald-500 opacity-20 relative z-10" />
            </div>
          </div>
        </div>

        {activeSubscriptions.length > 0 && (
          <div>
            <h2 className="text-2xl font-bold text-white">Your Active Subscriptions</h2>
            <p className="text-zinc-500 mt-1">You are currently copying the following experts.</p>
            <div className="mt-6 space-y-4">
              {activeSubscriptions.map(sub => (
                <div key={sub.id} className="bg-[#111] border border-[#1A1A1A] rounded-[24px] p-6 flex items-center justify-between">
                  <div>
                    <div className="flex items-center gap-2">
                      <div className="w-2 h-2 bg-emerald-500 rounded-full" />
                      <h3 className="text-lg font-bold text-white">{sub.expert_name}</h3>
                    </div>
                    <div className="flex gap-6 mt-2">
                      <div><span className="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Allocated</span><p className="text-sm font-mono text-zinc-300">${sub.allocated}</p></div>
                      <div><span className="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Current Value</span><p className="text-sm font-mono text-zinc-300">${sub.current_value}</p></div>
                      <div><span className="text-[10px] text-zinc-500 uppercase tracking-widest font-bold">Since</span><p className="text-sm text-zinc-300">{sub.date}</p></div>
                    </div>
                  </div>
                  <button onClick={() => cancel(sub)}
                    className="flex items-center gap-2 px-4 py-2 text-xs font-bold text-rose-500 bg-rose-500/10 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                    <XCircle size={14} /> Cancel
                  </button>
                </div>
              ))}
            </div>
          </div>
        )}

        <div>
          <h2 className="text-2xl font-bold text-white">Top Performing Strategies</h2>
          <p className="text-zinc-500 mt-1">Verified experts with consistent market-beating performance history.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
          {experts.map((expert) => (
            <motion.div key={expert.id} whileHover={{ y: -8 }}
              className="bg-[#111] border border-[#1A1A1A] rounded-[32px] overflow-hidden group hover:border-emerald-500/30 transition-all flex flex-col shadow-xl">
              <div className="p-8 border-b border-[#1A1A1A] flex items-center gap-6">
                <div className="relative">
                  <div className="w-20 h-20 rounded-[28px] overflow-hidden bg-white/5 border border-white/10 group-hover:border-emerald-500/50 transition-all relative z-10">
                    <img src={expert.avatar ?? '/images/avatar-placeholder.svg'} alt={expert.name} className="w-full h-full object-cover" />
                  </div>
                  <div className="absolute -top-2 -right-2 w-8 h-8 bg-[#0A0A0A] border-4 border-[#111] rounded-full flex items-center justify-center text-blue-500 z-20">
                    <Check size={14} strokeWidth={4} />
                  </div>
                </div>
                <div className="flex-1">
                  <div className="flex items-center gap-2">
                    <h3 className="text-lg font-bold text-white group-hover:text-emerald-500 transition-colors">{expert.name}</h3>
                    <div className="p-1 px-1.5 bg-blue-600 rounded-md">
                      <Star size={10} className="text-white fill-white" />
                    </div>
                  </div>
                  <p className="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mt-1">{expert.status} status</p>
                </div>
              </div>
              <div className="p-8 space-y-8 flex-1">
                <div className="grid grid-cols-2 gap-4">
                  <div className="bg-[#0A0A0A] p-4 rounded-2xl border border-[#1A1A1A]">
                    <p className="text-[10px] text-zinc-500 font-bold uppercase tracking-widest text-center mb-1">Win Rate</p>
                    <p className="text-xl font-bold text-white font-mono text-center">{expert.win_rate}</p>
                  </div>
                  <div className="bg-[#0A0A0A] p-4 rounded-2xl border border-[#1A1A1A]">
                    <p className="text-[10px] text-zinc-500 font-bold uppercase tracking-widest text-center mb-1">Profit Share</p>
                    <p className="text-xl font-bold text-blue-500 font-mono text-center">{expert.profit_share}</p>
                  </div>
                </div>
                {selectedExpert === expert.id ? (
                  <form onSubmit={() => { setData('expert_id', expert.id); post(route('experts.store')); }} className="space-y-4">
                    <div className="space-y-2">
                      <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Allocation ($)</label>
                      <input type="number" value={data.amount} onChange={(e) => setData('amount', e.target.value)}
                        placeholder="1000"
                        className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl px-5 py-4 text-sm text-white focus:border-emerald-600/50 outline-none transition-all font-mono" />
                    </div>
                    <button type="submit" disabled={processing}
                      className="w-full py-5 bg-emerald-600 text-black font-bold rounded-2xl hover:bg-emerald-500 transition-all flex items-center justify-center gap-2 shadow-xl">
                      {processing ? 'Processing...' : <>Confirm Copy <ChevronRight size={18} /></>}
                    </button>
                  </form>
                ) : (
                  <button onClick={() => handleCopy(expert.id)}
                    className="w-full py-5 bg-white/5 border border-white/10 text-white font-bold rounded-2xl hover:bg-emerald-600 hover:text-black hover:border-emerald-600 transition-all flex items-center justify-center gap-2 shadow-xl group-hover:shadow-[0_4px_30px_rgba(16,185,129,0.2)]">
                    Copy Strategy <ChevronRight size={18} />
                  </button>
                )}
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </AppLayout>
  );
}