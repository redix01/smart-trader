import { useState } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { motion } from 'motion/react';
import { Building2, Home, Landmark, TrendingUp, Target, MapPin, ChevronRight, ArrowUpRight } from 'lucide-react';

interface Project {
  id: number;
  title: string;
  region: string;
  description: string;
  min: string;
  roi: string;
  strategy: string;
  status: string;
  image: string;
}

interface RealEstateProps {
  projects: Project[];
}

export default function RealEstate({ projects }: RealEstateProps) {
  const { data, setData, post, processing, reset } = useForm({
    project_id: 0,
    amount: '',
  });
  const [selectedProject, setSelectedProject] = useState<number | null>(null);

  const handleInvest = (projectId: number) => {
    setSelectedProject(projectId === selectedProject ? null : projectId);
  };

  return (
    <AppLayout>
      <Head title="Real Estate" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Real Estate</h1>
        <p className="text-zinc-500 text-sm font-medium">Invest in prime institutional-grade real estate.</p>
      </header>
      <div className="space-y-10">
        <div className="bg-[#111] border border-[#1A1A1A] rounded-[40px] p-8 lg:p-12 relative overflow-hidden">
          <div className="relative z-10 flex flex-col lg:flex-row items-center gap-12">
            <div className="space-y-6 lg:max-w-xl">
              <div className="inline-flex items-center gap-2 px-3 py-1 bg-amber-500/10 border border-amber-500/20 rounded-full">
                <Home className="text-amber-500" size={14} />
                <span className="text-[10px] font-bold text-amber-500 uppercase tracking-widest">Fractional Ownership</span>
              </div>
              <h2 className="text-4xl font-bold text-white tracking-tight leading-tight">
                Invest in Prime <span className="text-amber-500">Real Estate</span>
              </h2>
              <p className="text-zinc-500 text-lg leading-relaxed">Join exclusive institutional-grade real estate projects and earn passive income.</p>
            </div>
            <div className="flex-1 grid grid-cols-2 gap-4">
              {[
                { label: 'Properties Managed', value: '450+', icon: Building2 },
                { label: 'Total Asset Value', value: '$2.4B+', icon: Landmark },
                { label: 'Avg Monthly Yield', value: '8.4%', icon: TrendingUp },
                { label: 'Investors', value: '24K+', icon: Target },
              ].map((idx, i) => (
                <div key={i} className="bg-[#0A0A0A] border border-[#1A1A1A] p-6 rounded-3xl space-y-3 hover:border-amber-500/30 transition-all cursor-default group">
                  <div className="p-2 bg-white/5 w-fit rounded-xl text-zinc-500 group-hover:text-amber-500 transition-colors"><idx.icon size={20} /></div>
                  <div>
                    <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">{idx.label}</p>
                    <p className="text-xl font-bold text-white">{idx.value}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>

        <div>
          <h2 className="text-2xl font-bold text-white">Active Projects</h2>
          <p className="text-zinc-500 mt-1">Browse available investment opportunities.</p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10">
          {projects.map((p) => (
            <motion.div key={p.id} whileHover={{ y: -8 }}
              className="bg-[#111] border border-[#1A1A1A] rounded-[40px] overflow-hidden group shadow-2xl flex flex-col">
              <div className="h-64 relative overflow-hidden">
                <img src={p.image} className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="" />
                <div className="absolute inset-0 bg-gradient-to-t from-[#111] to-transparent" />
                <div className="absolute top-6 left-6 inline-flex items-center gap-2 px-3 py-1.5 bg-black/60 backdrop-blur-md rounded-xl text-[10px] font-bold text-white border border-white/10 uppercase tracking-widest">
                  <MapPin size={12} className="text-amber-500" /> {p.region}
                </div>
              </div>
              <div className="p-8 lg:p-10 space-y-10 flex-1">
                <div className="space-y-4">
                  <h3 className="text-2xl font-bold text-white leading-snug group-hover:text-amber-500 transition-colors">{p.title}</h3>
                  <p className="text-zinc-500 leading-relaxed font-medium">{p.description}</p>
                </div>
                <div className="grid grid-cols-3 gap-6">
                  <div className="space-y-1">
                    <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">Minimum</p>
                    <p className="text-lg font-bold text-white font-mono">{p.min}</p>
                  </div>
                  <div className="space-y-1">
                    <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">Target ROI</p>
                    <p className="text-lg font-bold text-emerald-500 font-mono">{p.roi}</p>
                  </div>
                  <div className="space-y-1">
                    <p className="text-[10px] text-zinc-600 font-bold uppercase tracking-widest">Strategy</p>
                    <p className="text-[11px] font-black text-amber-500 uppercase tracking-tight truncate leading-7">{p.strategy}</p>
                  </div>
                </div>
                <div className="flex gap-4 pt-4 border-t border-[#1A1A1A]">
                  <button className="flex-1 py-4 bg-white/5 border border-white/10 text-white font-bold rounded-2xl hover:bg-white/10 transition-all text-xs uppercase tracking-widest">View Project</button>
                  <button onClick={() => handleInvest(p.id)}
                    className="flex-1 py-4 bg-amber-600 text-white font-bold rounded-2xl hover:bg-amber-500 transition-all text-xs uppercase tracking-widest shadow-[0_4px_25px_rgba(217,119,6,0.25)] flex items-center justify-center gap-2">
                    Invest Now <ArrowUpRight size={16} />
                  </button>
                </div>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </AppLayout>
  );
}
