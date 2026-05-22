import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { motion } from 'motion/react';
import { ShieldCheck, ChevronRight, Database, TrendingUp, Lock } from 'lucide-react';

interface StakingPlan {
  id: number;
  name: string;
  symbol: string;
  icon: string;
  icon_bg: string;
  min: string;
  max: string;
  cycle: string;
  apy: string;
  duration_days: number;
}

interface StakingProps {
  plans: StakingPlan[];
}

export default function Staking({ plans }: StakingProps) {
  return (
    <AppLayout>
      <Head title="Staking" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Stakes</h1>
        <p className="text-zinc-500 text-sm font-medium">Earn passive income by locking your assets.</p>
      </header>
      <div className="space-y-8">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          {[
            { label: 'Total Value Locked', value: '$850,420.00', icon: Lock, color: 'text-blue-500' },
            { label: 'Avg. APY', value: plans.length > 0 ? plans[0].apy : '14.2%', icon: TrendingUp, color: 'text-emerald-500' },
            { label: 'Active Stakes', value: String(plans.length), icon: Database, color: 'text-indigo-500' },
          ].map((stat, i) => (
            <div key={i} className="bg-[#111] border border-[#1A1A1A] p-6 rounded-3xl flex items-center justify-between group hover:border-blue-600/30 transition-all cursor-default relative overflow-hidden">
              <div className="relative z-10">
                <p className="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{stat.label}</p>
                <h3 className="text-2xl font-bold text-white mt-1">{stat.value}</h3>
              </div>
              <div className={`p-3 rounded-2xl bg-white/5 ${stat.color} relative z-10 group-hover:scale-110 transition-transform`}>
                <stat.icon size={24} />
              </div>
              <div className="absolute top-0 right-0 w-24 h-24 bg-blue-600/5 rounded-full blur-2xl pointer-events-none" />
            </div>
          ))}
        </div>

        <div>
          <h2 className="text-xl font-bold text-white flex items-center gap-2">
            <Database size={20} className="text-blue-500" />
            Active Staking Plans
          </h2>
          <p className="text-zinc-500 text-sm mt-1">Choose a plan to start earning passive income.</p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
          {plans.map((plan) => (
            <motion.div key={plan.id} whileHover={{ y: -5 }}
              className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden group hover:border-blue-600/30 transition-all flex flex-col">
              <div className={`p-6 bg-gradient-to-br from-white/5 to-white/0 border-b border-[#1A1A1A] flex items-center justify-between`}>
                <div className="flex items-center gap-4">
                  <div className={`w-12 h-12 ${plan.icon_bg} rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform`}>
                    <img src={plan.icon} className="w-8 h-8 rounded-full p-1 bg-white" alt="" />
                  </div>
                  <div>
                    <h3 className="text-lg font-bold text-white">{plan.name}</h3>
                    <div className="flex items-center gap-2">
                      <span className="text-[10px] font-bold text-white/50 uppercase tracking-widest">Available</span>
                      <div className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse" />
                    </div>
                  </div>
                </div>
                <div className="text-right">
                  <p className="text-[10px] text-white/60 font-bold uppercase tracking-widest">APY</p>
                  <p className="text-lg font-black text-white">{plan.apy}</p>
                </div>
              </div>
              <div className="p-6 space-y-6 flex-1">
                <div className="grid grid-cols-3 gap-2">
                  <div className="bg-[#0A0A0A] border border-[#1A1A1A] p-3 rounded-2xl flex flex-col items-center">
                    <p className="text-[8px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Minimum</p>
                    <p className="text-xs font-bold text-white font-mono">{plan.min}</p>
                  </div>
                  <div className="bg-[#0A0A0A] border border-[#1A1A1A] p-3 rounded-2xl flex flex-col items-center">
                    <p className="text-[8px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Maximum</p>
                    <p className="text-xs font-bold text-white font-mono">{plan.max}</p>
                  </div>
                  <div className="bg-[#0A0A0A] border border-[#1A1A1A] p-3 rounded-2xl flex flex-col items-center">
                    <p className="text-[8px] text-zinc-500 font-bold uppercase tracking-widest mb-1">Cycle</p>
                    <p className="text-xs font-bold text-blue-500 uppercase tracking-wider">{plan.cycle}</p>
                  </div>
                </div>
                <div className="space-y-4">
                  <div className="flex items-center gap-2 text-zinc-500 text-[10px] font-medium leading-relaxed italic">
                    <ShieldCheck size={14} className="text-emerald-500" />
                    Smart Contract Audited
                  </div>
                  <button className="w-full py-4 bg-white/5 border border-white/10 text-white rounded-2xl font-bold text-xs uppercase tracking-widest hover:bg-blue-600 hover:border-blue-600 transition-all group-hover:shadow-[0_0_20px_rgba(37,99,235,0.2)] flex items-center justify-center gap-2">
                    Stake Now <ChevronRight size={14} />
                  </button>
                </div>
              </div>
            </motion.div>
          ))}
        </div>

        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-8 text-center space-y-4 relative overflow-hidden">
          <div className="w-16 h-16 bg-[#1A1A1A] rounded-3xl flex items-center justify-center mx-auto text-zinc-600 relative z-10">
            <Database size={32} />
          </div>
          <div className="relative z-10">
            <h3 className="text-lg font-bold text-white">Your Stakings</h3>
            <p className="text-zinc-500 text-sm">You haven't made any stakings yet. Choose a plan above to start earning.</p>
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-blue-600/5 to-transparent pointer-events-none" />
        </div>
      </div>
    </AppLayout>
  );
}
