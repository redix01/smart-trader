import { useState } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Search, TrendingUp, TrendingDown, Star, ChevronRight } from 'lucide-react';

interface Market {
  id: number;
  name: string;
  symbol: string;
  price: string;
  change: string;
  high: string;
  low: string;
  volume: string;
  cap: string;
  icon: string;
  up: boolean;
}

interface MarketsProps {
  markets: Market[];
  topGainers: Market[];
  overview: {
    total_market_cap: string;
    total_volume_24h: string;
    active_pairs: number;
  };
  favorites: number[];
}

export default function Markets({ markets, topGainers, overview, favorites }: MarketsProps) {
  const [activeCat, setActiveCat] = useState('All');
  const filtered = activeCat === 'All' ? markets : activeCat === 'Favorites' ? markets.filter(m => favorites.includes(m.id)) : markets;

  return (
    <AppLayout>
      <Head title="Markets" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Markets</h1>
        <p className="text-zinc-500 text-sm font-medium">Discover and track market movements.</p>
      </header>
      <div className="space-y-6">
        <div className="flex flex-col lg:flex-row gap-6">
          <div className="flex-1 space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              {[
                { label: 'BTC Dominance', value: '54.2%', change: '+0.4%', up: true },
                { label: 'Market Cap', value: overview.total_market_cap, change: '+1.2%', up: true },
                { label: 'Volume (24h)', value: overview.total_volume_24h, change: '-12.5%', up: false },
              ].map((idx, i) => (
                <div key={i} className="bg-[#111] border border-[#1A1A1A] p-4 rounded-2xl flex flex-col justify-between">
                  <span className="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">{idx.label}</span>
                  <div className="flex items-end justify-between mt-1">
                    <span className="text-xl font-bold text-white font-mono">{idx.value}</span>
                    <span className={`text-xs font-bold ${idx.up ? 'text-emerald-500' : 'text-rose-500'}`}>{idx.change}</span>
                  </div>
                </div>
              ))}
            </div>

            <div className="flex flex-col sm:flex-row sm:items-center gap-3">
              <div className="flex gap-2 p-1 bg-[#111] border border-[#1A1A1A] rounded-xl self-start">
                {['All', 'Favorites', 'Crypto'].map((cat) => (
                  <button key={cat} onClick={() => setActiveCat(cat)}
                    className={`px-4 py-1.5 rounded-lg text-xs font-bold transition-all ${activeCat === cat ? 'bg-blue-600/10 text-blue-500' : 'text-zinc-500 hover:text-white'}`}>{cat}</button>
                ))}
              </div>
              <div className="flex items-center gap-2 bg-[#111] border border-[#1A1A1A] rounded-xl px-4 py-2 focus-within:border-blue-600 transition-colors sm:ml-auto">
                <Search size={16} className="text-zinc-500 flex-shrink-0" />
                <input type="text" placeholder="Search..." className="bg-transparent border-none outline-none text-xs text-white w-full sm:w-48" />
              </div>
            </div>

            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left">
                  <thead>
                    <tr className="border-b border-[#1A1A1A]">
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Market / Pair</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Price</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-center">24h Change</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right hidden md:table-cell">Vol (24h)</th>
                      <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Trade</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#0A0A0A]">
                    {filtered.map((m) => (
                      <tr key={m.id} className="group hover:bg-[#151515] transition-colors cursor-pointer">
                        <td className="px-6 py-5">
                          <div className="flex items-center gap-4">
                            <button className={`transition-colors ${favorites.includes(m.id) ? 'text-yellow-500' : 'text-zinc-600 hover:text-yellow-500'}`}><Star size={16} fill={favorites.includes(m.id) ? 'currentColor' : 'none'} /></button>
                            <div className="w-8 h-8 rounded-full overflow-hidden p-1 bg-[#1A1A1A]">
                              <img src={m.icon} className="w-full h-full object-contain" alt={m.name} />
                            </div>
                            <div>
                              <p className="text-sm font-bold text-white">{m.name}</p>
                              <p className="text-[10px] text-zinc-600 font-mono italic">{m.symbol}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-5 text-right font-mono text-sm font-bold text-white">${m.price}</td>
                        <td className="px-6 py-5 text-center">
                          <div className={`inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[11px] font-bold ${m.up ? 'text-emerald-500 bg-emerald-500/10' : 'text-rose-500 bg-rose-500/10'}`}>
                            {m.up ? <TrendingUp size={12} /> : <TrendingDown size={12} />}{m.change}
                          </div>
                        </td>
                        <td className="px-6 py-5 text-right font-mono text-xs text-zinc-500 hidden md:table-cell">${m.volume}</td>
                        <td className="px-6 py-5 text-right">
                          <button className="p-2 bg-blue-600/10 text-blue-600 rounded-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-blue-600 hover:text-white"><ChevronRight size={18} /></button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div className="w-full lg:w-80 space-y-6">
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
              <h3 className="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <Star size={16} className="text-yellow-500 fill-yellow-500" /> Top Gainers
              </h3>
              <div className="space-y-4">
                {topGainers.map((m) => (
                  <div key={m.id} className="flex items-center justify-between p-2 hover:bg-[#1A1A1A] rounded-xl transition-colors cursor-pointer group">
                    <div className="flex items-center gap-3">
                      <img src={m.icon} className="w-6 h-6 p-0.5 bg-[#222] rounded-full" alt="" />
                      <div>
                        <p className="text-xs font-bold text-white">{m.name}</p>
                        <p className="text-[10px] text-zinc-500">{m.symbol.split('/')[0]}</p>
                      </div>
                    </div>
                    <div className="text-right">
                      <p className="text-xs font-bold text-white">${m.price}</p>
                      <p className="text-[10px] text-emerald-500 font-bold">{m.change}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-3xl p-6 relative overflow-hidden group">
              <div className="relative z-10 space-y-4">
                <p className="text-xs font-bold text-white/60 uppercase tracking-widest">New Feature</p>
                <h4 className="text-xl font-bold text-white leading-tight">Professional Analysis Tools</h4>
                <p className="text-sm text-white/70 leading-relaxed">Upgrade to Pro level and get real-time indicators and trade signals.</p>
                <button className="w-full py-3 bg-white text-blue-600 font-bold rounded-2xl text-xs hover:scale-[1.02] transition-transform shadow-xl">Get Started Today</button>
              </div>
              <div className="absolute right-[-20px] bottom-[-20px] w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:scale-125 transition-transform" />
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
