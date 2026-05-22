import { useState, useEffect, useRef } from 'react';
import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { motion } from 'motion/react';
import { TrendingUp, TrendingDown, Eye, EyeOff, ShieldCheck, Clock, ArrowUpRight, ArrowLeftRight } from 'lucide-react';

function ChevronDown({ size, className }: { size: number, className: string }) {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className={className}>
      <path d="m6 9 6 6 6-6"/>
    </svg>
  );
}

interface DashboardProps {
  kpi: {
    total_balance: string;
    active_trades: number;
    open_profit: string;
    pending_deposit: string;
    pending_withdrawal: string;
    monthly_change_pct: string;
  };
  wallets: Array<{
    symbol: string;
    balance: string | number;
    locked: string | number;
    available: string | number;
  }>;
  kyc_status: string;
}

export default function Dashboard({ kpi, wallets, kyc_status }: DashboardProps) {
  const [showBalance, setShowBalance] = useState(true);
  const [quickTradeTab, setQuickTradeTab] = useState<'Buy' | 'Sell' | 'Swap'>('Buy');

  const tickerRef = useRef<HTMLDivElement>(null);
  const chartRef = useRef<HTMLDivElement>(null);

  // Ticker tape — inject once
  useEffect(() => {
    if (!tickerRef.current) return;
    tickerRef.current.innerHTML = '';
    const script = document.createElement('script');
    script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js';
    script.async = true;
    script.innerHTML = JSON.stringify({
      symbols: [
        { proName: 'BITSTAMP:BTCUSD', title: 'Bitcoin' },
        { proName: 'BITSTAMP:ETHUSD', title: 'Ethereum' },
        { proName: 'BINANCE:SOLUSDT', title: 'Solana' },
        { proName: 'BINANCE:BNBUSDT', title: 'BNB' },
        { proName: 'BINANCE:XRPUSDT', title: 'XRP' },
        { proName: 'FX:EURUSD', title: 'EUR/USD' },
        { proName: 'FX:GBPUSD', title: 'GBP/USD' },
        { proName: 'NASDAQ:AAPL', title: 'Apple' },
        { proName: 'NASDAQ:TSLA', title: 'Tesla' },
        { proName: 'NASDAQ:NVDA', title: 'NVIDIA' },
      ],
      showSymbolLogo: true,
      isTransparent: true,
      displayMode: 'adaptive',
      colorTheme: 'dark',
      locale: 'en',
    });
    tickerRef.current.appendChild(script);
    return () => { if (tickerRef.current) tickerRef.current.innerHTML = ''; };
  }, []);

  // BTC/USDT area chart — inject once
  useEffect(() => {
    if (!chartRef.current) return;
    chartRef.current.innerHTML = '';
    const script = document.createElement('script');
    script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js';
    script.async = true;
    script.innerHTML = JSON.stringify({
      autosize: true,
      symbol: 'BINANCE:BTCUSDT',
      interval: 'D',
      timezone: 'Etc/UTC',
      theme: 'dark',
      style: '3',
      locale: 'en',
      backgroundColor: 'rgba(10,10,10,0)',
      gridColor: 'rgba(27,27,27,0.8)',
      hide_top_toolbar: false,
      hide_legend: true,
      allow_symbol_change: true,
      save_image: false,
      calendar: false,
    });
    chartRef.current.appendChild(script);
    return () => { if (chartRef.current) chartRef.current.innerHTML = ''; };
  }, []);

  const [swapFrom, setSwapFrom] = useState('BTC');
  const [swapTo, setSwapTo] = useState('ETH');
  const [swapAmount, setSwapAmount] = useState('');
  const swapRate = 18.5;
  const swapEstimate = swapAmount ? (parseFloat(swapAmount || '0') * swapRate).toFixed(6) : '0.00';

  return (
    <AppLayout>
      <Head title="Dashboard" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Dashboard</h1>
        <p className="text-zinc-500 text-sm font-medium">Welcome back, here's what's happening today.</p>
      </header>
      <div className="space-y-6">
        {/* TradingView Ticker Tape */}
        <div className="rounded-3xl border border-[#1A1A1A] bg-[#111] overflow-hidden">
          <div ref={tickerRef} style={{ minHeight: '56px' }} />
        </div>

        {kyc_status === 'unverified' && (
          <Link href={route('kyc')} className="bg-blue-600/10 border border-blue-600/20 px-6 py-4 rounded-2xl flex items-center justify-between group hover:bg-blue-600/15 transition-all">
            <div className="flex items-center gap-4">
              <div className="p-2 bg-blue-600 rounded-lg">
                <ShieldCheck className="text-white" size={20} />
              </div>
              <div>
                <p className="text-sm font-medium text-white">Identity Verification Required</p>
                <p className="text-xs text-blue-100/60 font-medium">To unlock full features, please verify your account.</p>
              </div>
            </div>
            <span className="text-xs font-bold uppercase tracking-wider text-blue-400">Verify Now</span>
          </Link>
        )}

        <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
          <div className="xl:col-span-2 space-y-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div className="bg-[#111] border border-[#1A1A1A] p-6 rounded-3xl relative overflow-hidden group">
                <div className="relative z-10 flex flex-col justify-between h-full min-h-[160px]">
                  <div className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <span className="text-sm text-zinc-400">Total Balance</span>
                      <button onClick={() => setShowBalance(!showBalance)} className="text-zinc-500 hover:text-zinc-300">
                        {showBalance ? <Eye size={16} /> : <EyeOff size={16} />}
                      </button>
                    </div>
                    <div className="px-3 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-full">
                      <span className="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">Live Account</span>
                    </div>
                  </div>
                  <div>
                    <h2 className="text-4xl font-bold text-white tracking-tight">{showBalance ? `$${kpi.total_balance}` : '••••••'}</h2>
                    <div className="flex items-center gap-2 mt-2 text-emerald-500">
                      <TrendingUp size={16} />
                      <span className="text-sm font-medium">{kpi.monthly_change_pct} this month</span>
                    </div>
                  </div>
                  <div className="space-y-2 mt-4">
                    <div className="flex justify-between text-xs font-medium">
                      <span className="text-zinc-500 font-medium uppercase tracking-wider">Trading Progress</span>
                      <span className="text-white">68%</span>
                    </div>
                    <div className="h-1.5 w-full bg-[#1A1A1A] rounded-full overflow-hidden">
                      <motion.div initial={{ width: 0 }} animate={{ width: '68%' }} transition={{ duration: 1, delay: 0.5 }} className="h-full bg-blue-600 rounded-full" />
                    </div>
                  </div>
                </div>
                <div className="absolute top-[-50px] right-[-50px] w-48 h-48 bg-blue-600/5 rounded-full blur-3xl group-hover:bg-blue-600/10 transition-colors" />
              </div>

              <div className="grid grid-cols-2 gap-4">
                {[
                  { label: 'Active Trades', value: String(kpi.active_trades), icon: TrendingUp, color: 'text-emerald-500', bg: 'bg-emerald-500/5' },
                  { label: 'Open Profit', value: kpi.open_profit, icon: TrendingUp, color: 'text-emerald-500', bg: 'bg-emerald-500/5' },
                  { label: 'Pending Deposit', value: kpi.pending_deposit, icon: Clock, color: 'text-zinc-500', bg: 'bg-zinc-500/5' },
                  { label: 'Withdrawal', value: kpi.pending_withdrawal, icon: TrendingDown, color: 'text-rose-500', bg: 'bg-rose-500/5' },
                ].map((stat, i) => (
                  <div key={i} className="bg-[#111] border border-[#1A1A1A] p-4 rounded-2xl flex flex-col justify-between">
                    <div className={`p-2 w-fit rounded-lg ${stat.bg}`}>
                      <stat.icon className={stat.color} size={18} />
                    </div>
                    <div>
                      <p className="text-[10px] text-zinc-500 font-bold uppercase tracking-widest mb-1">{stat.label}</p>
                      <p className="text-lg font-bold text-white">{stat.value}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden h-[400px] flex flex-col">
              <div className="flex items-center justify-between px-6 py-4 border-b border-[#1A1A1A] flex-shrink-0">
                <div className="flex items-center gap-3">
                  <img src="https://assets.coingecko.com/coins/images/1/small/bitcoin.png" className="w-6 h-6 rounded-full" alt="btc" />
                  <div>
                    <p className="text-sm font-bold text-white">BTC / USDT</p>
                    <p className="text-[10px] text-zinc-500 font-medium">Market Overview</p>
                  </div>
                </div>
                <Link href={route('trades')} className="text-[10px] font-bold text-blue-500 hover:text-blue-400 transition-colors flex items-center gap-1 uppercase tracking-widest">
                  Full Chart <ArrowUpRight size={12} />
                </Link>
              </div>
              <div ref={chartRef} className="flex-1 w-full" />
            </div>
          </div>

          <div className="space-y-6">
            <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 space-y-6">
                <div className="flex p-1 bg-[#0A0A0A] rounded-2xl border border-[#1A1A1A]">
                  {(['Buy', 'Sell', 'Swap'] as const).map((tab) => (
                    <button key={tab} onClick={() => setQuickTradeTab(tab)}
                      className={`flex-1 py-3 rounded-xl text-xs font-bold uppercase tracking-widest transition-all ${
                        quickTradeTab === tab
                          ? tab === 'Buy'  ? 'bg-emerald-500 text-black shadow-lg'
                          : tab === 'Sell' ? 'bg-rose-500 text-white shadow-lg'
                          :                  'bg-blue-600 text-white shadow-lg'
                          : 'text-zinc-500 hover:text-white'
                      }`}>{tab}</button>
                  ))}
                </div>
              {quickTradeTab === 'Swap' ? (
                <div className="space-y-4">
                  <div className="space-y-1.5">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">From</label>
                    <div className="flex gap-2">
                      <select value={swapFrom} onChange={e => setSwapFrom(e.target.value)}
                        className="w-28 bg-[#1A1A1A] border border-[#222] rounded-xl px-3 py-3 text-sm text-white focus:border-blue-600 outline-none">
                        {['BTC', 'ETH', 'USDT', 'SOL', 'XRP'].map(c => (
                          <option key={c} value={c}>{c}</option>
                        ))}
                      </select>
                      <input type="number" value={swapAmount} onChange={e => setSwapAmount(e.target.value)}
                        placeholder="0.00"
                        className="flex-1 bg-[#1A1A1A] border border-[#222] rounded-xl px-4 py-3 text-sm text-white focus:border-blue-600 outline-none font-mono" />
                    </div>
                  </div>
                  <div className="flex justify-center">
                    <button onClick={() => { const t = swapFrom; setSwapFrom(swapTo); setSwapTo(t); }}
                      className="p-2 bg-[#1A1A1A] rounded-xl text-zinc-400 hover:text-white transition-all">
                      <ArrowLeftRight size={18} />
                    </button>
                  </div>
                  <div className="space-y-1.5">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">To</label>
                    <div className="flex gap-2">
                      <select value={swapTo} onChange={e => setSwapTo(e.target.value)}
                        className="w-28 bg-[#1A1A1A] border border-[#222] rounded-xl px-3 py-3 text-sm text-white focus:border-blue-600 outline-none">
                        {['ETH', 'BTC', 'USDT', 'SOL', 'XRP'].map(c => (
                          <option key={c} value={c}>{c}</option>
                        ))}
                      </select>
                      <div className="flex-1 bg-[#1A1A1A] border border-[#222] rounded-xl px-4 py-3 text-sm text-white font-mono">
                        {swapEstimate}
                      </div>
                    </div>
                  </div>
                  <div className="bg-[#1A1A1A] border border-[#222] rounded-xl p-3 space-y-1.5">
                    <div className="flex justify-between text-[10px]">
                      <span className="text-zinc-500">Rate</span>
                      <span className="text-zinc-300 font-mono">1 {swapFrom} ≈ {swapRate} {swapTo}</span>
                    </div>
                    <div className="flex justify-between text-[10px]">
                      <span className="text-zinc-500">Fee</span>
                      <span className="text-zinc-400 font-mono">0.5%</span>
                    </div>
                  </div>
                  <button className="w-full py-4 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-500 transition-all flex items-center justify-center gap-2 shadow-[0_0_30px_rgba(37,99,235,0.2)]">
                    Swap {swapFrom} → {swapTo} <ArrowLeftRight size={18} />
                  </button>
                </div>
              ) : (
                <div className="space-y-4">
                  <div className="space-y-1.5">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Order Type</label>
                    <div className="relative group">
                      <select className="w-full bg-[#1A1A1A] border border-[#222] rounded-xl px-4 py-3 text-sm text-white appearance-none focus:border-blue-600 outline-none">
                        <option>Market Order</option>
                        <option>Limit Order</option>
                      </select>
                      <ChevronDown className="absolute right-4 top-1/2 -translate-y-1/2 text-zinc-500 group-focus-within:text-blue-600" size={16} />
                    </div>
                  </div>
                  <div className="space-y-1.5">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Entry Price (USDT)</label>
                    <div className="bg-[#1A1A1A] border border-[#222] rounded-xl px-4 py-3 text-sm text-zinc-400 font-mono">94,560.20</div>
                  </div>
                  <div className="space-y-1.5">
                    <div className="flex justify-between items-end">
                      <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Amount</label>
                      <span className="text-[10px] text-zinc-600 font-medium italic">Max: 10.42 BTC</span>
                    </div>
                    <div className="relative flex items-center">
                      <input type="number" placeholder="0.00" className="w-full bg-[#1A1A1A] border border-[#222] rounded-xl pl-4 pr-12 py-3 text-sm text-white focus:border-blue-600 outline-none font-mono" />
                      <span className="absolute right-4 text-[10px] font-bold text-zinc-500">BTC</span>
                    </div>
                  </div>
                  <div className="grid grid-cols-2 gap-4">
                    <div className="space-y-1.5">
                      <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Stop Loss</label>
                      <input type="number" placeholder="Auto" className="w-full bg-[#1A1A1A] border border-[#222] rounded-xl px-4 py-3 text-sm text-white focus:border-blue-600 outline-none font-mono" />
                    </div>
                    <div className="space-y-1.5">
                      <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Take Profit</label>
                      <input type="number" placeholder="Auto" className="w-full bg-[#1A1A1A] border border-[#222] rounded-xl px-4 py-3 text-sm text-white focus:border-blue-600 outline-none font-mono" />
                    </div>
                  </div>
                  <div className="pt-4 space-y-3">
                    <div className="flex justify-between text-xs">
                      <span className="text-zinc-500">Est. Total</span>
                      <span className="text-white font-mono">$0.00</span>
                    </div>
                    <div className="flex justify-between text-xs">
                      <span className="text-zinc-500">Fees</span>
                      <span className="text-zinc-400 font-mono">0.1%</span>
                    </div>
                    <Link href={route('trades')} className={`w-full py-4 font-bold rounded-2xl transition-all flex items-center justify-center gap-2 ${
                      quickTradeTab === 'Sell'
                        ? 'bg-rose-600 text-white hover:bg-rose-500 shadow-[0_0_30px_rgba(225,29,72,0.2)]'
                        : 'bg-emerald-500 text-black hover:bg-emerald-400 shadow-[0_0_30px_rgba(16,185,129,0.2)]'
                    }`}>
                      {quickTradeTab === 'Sell' ? 'Sell on Trades' : 'Buy on Trades'} <ArrowUpRight size={18} />
                    </Link>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
