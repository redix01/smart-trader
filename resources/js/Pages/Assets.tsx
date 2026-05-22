import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Search, ArrowUpCircle, ArrowDownCircle, ArrowLeftRight, MoreHorizontal, Filter } from 'lucide-react';

const icons: Record<string, string> = {
  BTC: 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png',
  ETH: 'https://assets.coingecko.com/coins/images/279/small/ethereum.png',
  USDT: 'https://assets.coingecko.com/coins/images/325/small/tether.png',
  SOL: 'https://assets.coingecko.com/coins/images/4128/small/solana.png',
  USD: 'https://img.icons8.com/color/48/usa-circular.png',
};

interface AssetsProps {
  assets: Array<{
    symbol: string;
    balance: string | number;
    locked: string | number;
    available: string | number;
  }>;
  deposits: Array<{
    id: number;
    method: string;
    currency: string;
    amount: string;
    fee: string;
    net: string;
    status: string;
    date: string;
  }>;
}

export default function Assets({ assets, deposits }: AssetsProps) {
  const [activeCategory, setActiveCategory] = useState<'crypto' | 'fiat' | 'all'>('crypto');
  const [search, setSearch] = useState('');

  const filtered = assets.filter((a) => {
    const cryptoSymbols = ['BTC', 'ETH', 'USDT', 'SOL', 'AVAX', 'MATIC', 'POL', 'DOT'];
    const matchesCategory = activeCategory === 'all' ||
      (activeCategory === 'crypto' ? cryptoSymbols.includes(a.symbol) : a.symbol === 'USD');
    const matchesSearch = !search || a.symbol.toLowerCase().includes(search.toLowerCase());
    return matchesCategory && matchesSearch;
  });

  return (
    <AppLayout>
      <Head title="Assets" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Assets</h1>
        <p className="text-zinc-500 text-sm font-medium">Your portfolio balances and holdings.</p>
      </header>
      <div className="space-y-6">
        <div className="flex flex-col md:flex-row gap-4 items-center justify-between">
          <div className="flex bg-[#111] p-1 rounded-2xl border border-[#1A1A1A]">
            {(['crypto', 'fiat', 'all'] as const).map((cat) => (
              <button key={cat} onClick={() => setActiveCategory(cat)}
                className={`px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition-all ${activeCategory === cat ? 'bg-blue-600 text-white shadow-lg' : 'text-zinc-500 hover:text-white'}`}>{cat}</button>
            ))}
          </div>
          <div className="flex items-center gap-3 w-full md:w-auto">
            <div className="flex-1 md:w-64 flex items-center gap-2 bg-[#111] border border-[#1A1A1A] rounded-xl px-4 py-2.5 focus-within:border-blue-600 transition-colors">
              <Search size={16} className="text-zinc-500" />
              <input type="text" value={search} onChange={(e) => setSearch(e.target.value)} placeholder="Search assets..." className="bg-transparent border-none outline-none text-sm text-white w-full" />
            </div>
            <button className="p-2.5 bg-[#111] border border-[#1A1A1A] rounded-xl text-zinc-400 hover:text-white transition-colors">
              <Filter size={20} />
            </button>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
          <Link href={route('deposit')} className="p-6 bg-[#111] border border-[#1A1A1A] rounded-3xl flex items-center gap-4 hover:bg-[#151515] hover:border-blue-600/50 transition-all group">
            <div className="p-3 bg-blue-600/10 rounded-2xl text-blue-600 group-hover:scale-110 transition-transform"><ArrowUpCircle size={24} /></div>
            <div className="text-left">
              <p className="text-sm font-bold text-white">Deposit</p>
              <p className="text-xs text-zinc-500">Fund your account</p>
            </div>
          </Link>
          <Link href={route('withdraw')} className="p-6 bg-[#111] border border-[#1A1A1A] rounded-3xl flex items-center gap-4 hover:bg-[#151515] hover:border-rose-600/50 transition-all group">
            <div className="p-3 bg-rose-600/10 rounded-2xl text-rose-600 group-hover:scale-110 transition-transform"><ArrowDownCircle size={24} /></div>
            <div className="text-left">
              <p className="text-sm font-bold text-white">Withdraw</p>
              <p className="text-xs text-zinc-500">Fast payout request</p>
            </div>
          </Link>
          <Link href={route('swap')} className="p-6 bg-[#111] border border-[#1A1A1A] rounded-3xl flex items-center gap-4 hover:bg-[#151515] hover:border-emerald-600/50 transition-all group">
            <div className="p-3 bg-emerald-600/10 rounded-2xl text-emerald-600 group-hover:scale-110 transition-transform"><ArrowLeftRight size={24} /></div>
            <div className="text-left">
              <p className="text-sm font-bold text-white">Convert</p>
              <p className="text-xs text-zinc-500">Instant exchange</p>
            </div>
          </Link>
        </div>

        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden shadow-2xl">
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="border-b border-[#1A1A1A]">
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Asset</th>
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Balance</th>
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Available</th>
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-center">Status</th>
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Action</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-[#0A0A0A]">
                {filtered.map((asset) => (
                  <tr key={asset.symbol} className="hover:bg-[#151515] active:bg-[#1A1A1A] transition-colors cursor-pointer group">
                    <td className="px-6 py-5">
                      <div className="flex items-center gap-4">
                        <div className="w-10 h-10 rounded-full bg-[#1A1A1A] border border-[#222] p-1.5 group-hover:border-blue-600/30 transition-colors">
                          <img src={icons[asset.symbol] || ''} alt={asset.symbol} className="w-full h-full object-contain" />
                        </div>
                        <div>
                          <p className="text-sm font-bold text-white">{asset.symbol}</p>
                          <p className="text-xs text-zinc-500 font-mono">{asset.symbol}</p>
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-5 text-right font-mono text-sm text-zinc-300">{Number(asset.balance).toLocaleString()}</td>
                    <td className="px-6 py-5 text-right font-mono text-sm font-bold text-white">{Number(asset.available).toLocaleString()}</td>
                    <td className="px-6 py-5 text-center">
                      <span className="text-[11px] font-bold px-2 py-1 rounded-md text-emerald-500 bg-emerald-500/10">Active</span>
                    </td>
                    <td className="px-6 py-5 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <button className="px-4 py-1.5 bg-[#1A1A1A] text-[10px] font-bold text-white rounded-lg hover:bg-blue-600 transition-colors border border-[#222]">TRADE</button>
                        <button className="p-2 text-zinc-500 hover:text-white"><MoreHorizontal size={16} /></button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden shadow-2xl">
          <div className="px-6 py-5 border-b border-[#1A1A1A]">
            <h2 className="text-sm font-bold text-white uppercase tracking-widest">Approved Deposits</h2>
            <p className="text-xs text-zinc-500 mt-1">Deposits that have been approved and credited to your wallet.</p>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full text-left border-collapse">
              <thead>
                <tr className="border-b border-[#1A1A1A]">
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Method</th>
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Currency</th>
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Net</th>
                  <th className="px-6 py-5 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Date</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-[#0A0A0A]">
                {deposits.length === 0 ? (
                  <tr>
                    <td className="px-6 py-8 text-sm text-zinc-500" colSpan={4}>No approved deposits yet.</td>
                  </tr>
                ) : deposits.map((deposit) => (
                  <tr key={deposit.id} className="hover:bg-[#151515] transition-colors">
                    <td className="px-6 py-5 text-sm text-white font-medium">{deposit.method}</td>
                    <td className="px-6 py-5 text-sm text-zinc-300 font-mono">{deposit.currency}</td>
                    <td className="px-6 py-5 text-right text-sm font-mono text-emerald-400">{Number(deposit.net).toLocaleString()}</td>
                    <td className="px-6 py-5 text-right text-sm text-zinc-400 font-mono">{deposit.date}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
