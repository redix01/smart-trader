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
  favorites: number[];
}

export default function Markets({ markets, favorites }: MarketsProps) {
  const [activeCat, setActiveCat] = useState('All');
  const [search, setSearch] = useState('');
  const categoryFiltered = activeCat === 'All'
    ? markets
    : activeCat === 'Favorites'
      ? markets.filter((market) => favorites.includes(market.id))
      : markets;

  const filtered = categoryFiltered.filter((market) => {
    if (!search) {
      return true;
    }

    const needle = search.toLowerCase();

    return market.name.toLowerCase().includes(needle) || market.symbol.toLowerCase().includes(needle);
  });

  return (
    <AppLayout>
      <Head title="Markets" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Markets</h1>
        <p className="text-zinc-500 text-sm font-medium">Discover and track up to 100 live crypto markets.</p>
      </header>
      <div className="space-y-6">
        <div className="space-y-6">
          <div className="flex flex-col sm:flex-row sm:items-center gap-3">
            <div className="flex gap-2 p-1 bg-[#111] border border-[#1A1A1A] rounded-xl self-start">
              {['All', 'Favorites', 'Crypto'].map((cat) => (
                <button key={cat} onClick={() => setActiveCat(cat)}
                  className={`px-4 py-1.5 rounded-lg text-xs font-bold transition-all ${activeCat === cat ? 'bg-blue-600/10 text-blue-500' : 'text-zinc-500 hover:text-white'}`}>{cat}</button>
              ))}
            </div>
            <div className="flex items-center gap-2 bg-[#111] border border-[#1A1A1A] rounded-xl px-4 py-2 focus-within:border-blue-600 transition-colors sm:ml-auto">
              <Search size={16} className="text-zinc-500 flex-shrink-0" />
              <input
                type="text"
                value={search}
                onChange={(event) => setSearch(event.target.value)}
                placeholder="Search markets..."
                className="bg-transparent border-none outline-none text-xs text-white w-full sm:w-64"
              />
            </div>
          </div>
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden min-h-[70vh]">
            <div className="overflow-x-auto">
              <table className="w-full text-left">
                <thead>
                  <tr className="border-b border-[#1A1A1A]">
                    <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Market / Pair</th>
                    <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Price</th>
                    <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-center">24h Change</th>
                    <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right hidden md:table-cell">Vol (24h)</th>
                    <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right hidden lg:table-cell">Market Cap</th>
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
                      <td className="px-6 py-5 text-right font-mono text-xs text-zinc-500 hidden lg:table-cell">${m.cap}</td>
                      <td className="px-6 py-5 text-right">
                        <button className="p-2 bg-blue-600/10 text-blue-600 rounded-lg opacity-0 group-hover:opacity-100 transition-all hover:bg-blue-600 hover:text-white"><ChevronRight size={18} /></button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
              {filtered.length === 0 && (
                <div className="px-6 py-10 text-sm text-zinc-500">No markets matched your search.</div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
