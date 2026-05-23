import { useState, useEffect, useRef } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { TrendingUp, TrendingDown, Star, ArrowUpRight, Clock } from 'lucide-react';

interface Pair {
  id: number;
  name: string;
  price: string;
  change: string;
  up: boolean;
  icon: string;
}

interface Balance {
  label: string;
  value: string | number;
  color: string;
}

interface TradesProps {
  pairs: Pair[];
  stockPairs: Pair[];
  forexPairs: Pair[];
  defaultPair: Pair | null;
  defaultMarketType: MarketType;
  balances: Balance[];
  history: Array<{
    id: number;
    pair: string;
    side: string;
    type: string;
    amount: string;
    price: string;
    total: string;
    fee: string;
    status: string;
    date: string;
  }>;
}

type OrderType = 'Market' | 'Limit';
type OrderMode = 'buy' | 'sell';
type MarketType = 'crypto' | 'stocks' | 'forex';

const PCT_BTNS = ['25%', '50%', '75%', '100%'];

export default function Trades({ pairs, stockPairs, forexPairs, defaultPair, defaultMarketType, balances, history }: TradesProps) {
  const [marketType, setMarketType] = useState<MarketType>(defaultMarketType);
  const [activePair, setActivePair] = useState<Pair | undefined>(defaultPair ?? pairs[0]);
  const [orderType, setOrderType] = useState<OrderType>('Market');
  const [orderMode, setOrderMode] = useState<OrderMode>('buy');
  const [price, setPrice] = useState('');
  const [amount, setAmount] = useState('');
  const [activeTab, setActiveTab] = useState<'orders' | 'history'>('orders');
  const { data, setData, post, processing, errors, reset } = useForm({
    market_type: defaultMarketType,
    pair_id: defaultPair?.id ?? 0,
    pair: defaultPair?.name ?? '',
    side: 'buy',
    type: 'Market',
    amount: '',
    price: '',
  });

  const tickerRef = useRef<HTMLDivElement>(null);
  const chartRef = useRef<HTMLDivElement>(null);

  const currentPairs = marketType === 'crypto' ? pairs : marketType === 'stocks' ? stockPairs : forexPairs;

  const nameParts = (activePair?.name ?? 'BTC/USDT').split('/');
  const baseAsset = nameParts[0] ?? 'BTC';
  const quoteAsset = nameParts[1] ?? 'USDT';

  const tvSymbol = (() => {
    if (marketType === 'stocks') return `NASDAQ:${baseAsset}`;
    if (marketType === 'forex') return `FX_IDC:${baseAsset}${quoteAsset}`;
    return `BINANCE:${baseAsset}${quoteAsset}`;
  })();

  const usdBalance = balances.find(b => b.label === 'USD')?.value ?? 0;
  const baseBalance = balances.find(b => b.label === baseAsset)?.value ?? 0;
  const availableBalance = orderMode === 'buy' ? usdBalance : baseBalance;
  const availableLabel = orderMode === 'buy' ? 'USD' : baseAsset;

  const applyPct = (p: string) => {
    const max = parseFloat(String(availableBalance)) || 0;
    const factor = parseInt(p) / 100;
    const cp = parseFloat((activePair?.price ?? '1').replace(/,/g, ''));
    const nextAmount = orderMode === 'buy' ? (max * factor / cp).toFixed(6) : (max * factor).toFixed(6);
    setAmount(nextAmount);
    setData('amount', nextAmount);
  };

  const limitPrice = price ? parseFloat(price.replace(/,/g, '')) : parseFloat((activePair?.price ?? '0').replace(/,/g, ''));
  const total = amount ? (limitPrice * parseFloat(amount)).toFixed(2) : '0.00';
  const previousMarketTypeRef = useRef<MarketType>(defaultMarketType);

  useEffect(() => {
    if (previousMarketTypeRef.current === marketType) {
      return;
    }

    previousMarketTypeRef.current = marketType;
    const pairList = marketType === 'crypto' ? pairs : marketType === 'stocks' ? stockPairs : forexPairs;
    const first = pairList[0];

    if (first) {
      setActivePair(first);
    }
  }, [marketType, pairs, stockPairs, forexPairs]);

  useEffect(() => {
    if (activePair) {
      setData('pair_id', activePair.id);
      setData('pair', activePair.name);
    }
  }, [activePair, setData]);

  useEffect(() => {
    setData('market_type', marketType);
  }, [marketType, setData]);

  useEffect(() => {
    setData('side', orderMode);
  }, [orderMode, setData]);

  useEffect(() => {
    setData('type', orderType);
  }, [orderType, setData]);

  useEffect(() => {
    setData('amount', amount);
  }, [amount, setData]);

  useEffect(() => {
    setData('price', price);
  }, [price, setData]);

  const submitTrade = () => {
    post(route('trades.store'), {
      onSuccess: () => {
        reset('amount', 'price');
        setAmount('');
        setPrice('');
      },
    });
  };

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

  // Advanced Chart — reload when symbol changes
  useEffect(() => {
    if (!chartRef.current) return;
    chartRef.current.innerHTML = '';
    const script = document.createElement('script');
    script.src = 'https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js';
    script.async = true;
    script.innerHTML = JSON.stringify({
      autosize: true,
      symbol: tvSymbol,
      interval: '60',
      timezone: 'Etc/UTC',
      theme: 'dark',
      style: '1',
      locale: 'en',
      backgroundColor: 'rgba(10,10,10,0)',
      gridColor: 'rgba(27,27,27,0.8)',
      hide_top_toolbar: false,
      hide_legend: false,
      allow_symbol_change: true,
      save_image: false,
      calendar: false,
    });
    chartRef.current.appendChild(script);
    return () => { if (chartRef.current) chartRef.current.innerHTML = ''; };
  }, [tvSymbol]);

  return (
    <AppLayout>
      <Head title="Trades" />
      <header className="mb-6 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Trades</h1>
        <p className="text-zinc-500 text-sm font-medium">Place and manage your market orders.</p>
      </header>

      {/* TradingView Ticker Tape */}
      <div className="mb-6 rounded-3xl border border-[#1A1A1A] bg-[#111] overflow-hidden">
        <div ref={tickerRef} style={{ minHeight: '56px' }} />
      </div>

      <div className="grid grid-cols-1 xl:grid-cols-5 gap-6">
        {/* Left — chart + history */}
        <div className="xl:col-span-3 space-y-6">
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
            {/* TradingView Advanced Chart */}
            <div ref={chartRef} className="h-[480px] w-full" />
          </div>

          {/* Orders / Trade History */}
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
            <div className="flex items-center gap-1 p-1 bg-[#0A0A0A] border-b border-[#1A1A1A] m-4 rounded-2xl">
              {(['orders', 'history'] as const).map((tab) => (
                <button key={tab} onClick={() => setActiveTab(tab)}
                  className={`flex-1 py-3 rounded-xl text-xs font-bold uppercase tracking-widest transition-all ${
                    activeTab === tab ? 'bg-blue-600 text-white' : 'text-zinc-500 hover:text-white'
                  }`}>
                  {tab === 'orders' ? 'Open Orders' : 'Trade History'}
                </button>
              ))}
            </div>
            <div className="px-4 pb-6">
              {activeTab === 'orders' ? (
                <div className="py-10 text-center text-zinc-500">
                  <p className="text-sm font-medium">Orders execute immediately and settle into your wallet balance.</p>
                </div>
              ) : history.length === 0 ? (
                <div className="py-10 text-center text-zinc-500">
                  <p className="text-sm font-medium">No trade history yet</p>
                </div>
              ) : (
                <div className="space-y-3">
                  {history.map((entry) => (
                    <div key={entry.id} className="flex items-center justify-between rounded-2xl border border-[#1A1A1A] bg-[#0A0A0A] px-4 py-3 text-sm">
                      <div>
                        <p className="font-bold text-white">{entry.side.toUpperCase()} {entry.pair}</p>
                        <p className="text-[11px] text-zinc-500">{entry.date}</p>
                      </div>
                      <div className="text-right font-mono">
                        <p className="text-white">{entry.amount}</p>
                        <p className="text-zinc-500">${entry.total}</p>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>

        {/* Right — order panel */}
        <div className="xl:col-span-2 space-y-6">
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-5 space-y-4">

            {/* Market type selector */}
            <div>
              <p className="text-[9px] font-bold text-zinc-600 uppercase tracking-widest mb-2">Market</p>
              <div className="flex bg-[#0A0A0A] p-1 rounded-2xl border border-[#1A1A1A]">
                {(['crypto', 'stocks', 'forex'] as MarketType[]).map((m) => (
                  <button key={m} onClick={() => setMarketType(m)}
                    className={`flex-1 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all ${
                      marketType === m ? 'bg-[#1A1A1A] text-white shadow' : 'text-zinc-500 hover:text-zinc-300'
                    }`}>
                    {m}
                  </button>
                ))}
              </div>
            </div>

            {/* Pair selector + order type */}
            <div className="flex items-center justify-between pb-3 border-b border-[#1A1A1A]">
              <select
                value={activePair?.id}
                onChange={(e) => {
                  const p = currentPairs.find(cp => cp.id === Number(e.target.value));
                  if (p) setActivePair(p);
                }}
                className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-3 py-2 text-sm font-bold text-white outline-none cursor-pointer"
              >
                {currentPairs.map(p => <option key={p.id} value={p.id}>{p.name}</option>)}
              </select>
              <div className="flex items-center gap-1 bg-[#0A0A0A] p-0.5 rounded-lg border border-[#1A1A1A]">
                {(['Market', 'Limit'] as OrderType[]).map(t => (
                  <button key={t} onClick={() => setOrderType(t)}
                    className={`px-2.5 py-1 rounded-md text-[10px] font-bold transition-all ${orderType === t ? 'bg-[#1A1A1A] text-white' : 'text-zinc-500 hover:text-zinc-300'}`}>
                    {t}
                  </button>
                ))}
              </div>
            </div>

            {/* Buy / Sell tabs */}
            <div className="flex bg-[#0A0A0A] p-1 rounded-2xl border border-[#1A1A1A]">
              {(['buy', 'sell'] as OrderMode[]).map((mode) => (
                <button key={mode} onClick={() => setOrderMode(mode)}
                  className={`flex-1 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all ${
                    orderMode === mode
                      ? mode === 'buy' ? 'bg-emerald-500 text-black shadow-lg'
                      :                  'bg-rose-500 text-white shadow-lg'
                      : 'text-zinc-500 hover:text-white'
                  }`}>
                  {mode}
                </button>
              ))}
            </div>

            {/* BUY / SELL form */}
            {(
                <div className="space-y-4">
                {errors.pair && <p className="text-xs text-rose-500">{errors.pair}</p>}
                {errors.amount && <p className="text-xs text-rose-500">{errors.amount}</p>}
                {errors.price && <p className="text-xs text-rose-500">{errors.price}</p>}
                <div className="flex items-center justify-between bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-3 py-2.5">
                  <span className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Available</span>
                  <span className="text-xs font-bold text-white font-mono">
                    {Number(availableBalance).toLocaleString(undefined, { maximumFractionDigits: 6 })}{' '}
                    <span className="text-zinc-500">{availableLabel}</span>
                  </span>
                </div>

                {orderType === 'Limit' && (
                  <div className="space-y-1.5">
                    <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Price ({quoteAsset})</label>
                    <div className="relative">
                      <input type="text" value={price} onChange={(e) => setPrice(e.target.value)}
                        placeholder={activePair?.price}
                        className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 pr-14 text-sm text-white focus:border-blue-600 outline-none transition-colors font-mono" />
                      <span className="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-zinc-600">{quoteAsset}</span>
                    </div>
                  </div>
                )}

                <div className="space-y-1.5">
                  <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Amount ({baseAsset})</label>
                  <div className="relative">
                    <input type="number" value={amount} onChange={(e) => setAmount(e.target.value)}
                      placeholder="0.00"
                      className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 pr-14 text-sm text-white focus:border-blue-600 outline-none transition-colors font-mono" />
                    <span className="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-bold text-zinc-600">{baseAsset}</span>
                  </div>
                  <div className="grid grid-cols-4 gap-1 mt-1">
                    {PCT_BTNS.map((p) => (
                      <button type="button" key={p} onClick={() => applyPct(p)}
                        className="py-1 text-[9px] font-bold text-zinc-500 bg-[#0A0A0A] border border-[#1A1A1A] rounded-lg hover:bg-[#1A1A1A] hover:text-white transition-all">
                        {p}
                      </button>
                    ))}
                  </div>
                </div>

                <div className="space-y-1.5">
                  <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Total ({quoteAsset})</label>
                  <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm text-zinc-400 font-mono">${total}</div>
                </div>

                <div className="flex justify-between text-[10px] text-zinc-500 px-0.5">
                  <span>Fee <span className="text-zinc-400 font-mono">0.1%</span></span>
                  <span>Leverage <span className="text-white font-bold font-mono">1x</span></span>
                </div>
                {errors.amount && <p className="text-xs text-rose-500">{errors.amount}</p>}
                {errors.price && <p className="text-xs text-rose-500">{errors.price}</p>}

                <button type="button" onClick={submitTrade} disabled={processing || !amount || (orderType === 'Limit' && !price)} className={`w-full py-4 font-bold rounded-2xl transition-all flex items-center justify-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed ${
                  orderMode === 'buy'
                    ? 'bg-emerald-500 text-black hover:bg-emerald-400 shadow-[0_0_30px_rgba(16,185,129,0.2)]'
                    : 'bg-rose-600 text-white hover:bg-rose-500 shadow-[0_0_30px_rgba(225,29,72,0.2)]'
                }`}>
                  {orderMode === 'buy' ? <TrendingUp size={16} /> : <TrendingDown size={16} />}
                  {processing ? 'Processing...' : `${orderMode === 'buy' ? 'Buy' : 'Sell'} ${baseAsset}`}
                  <ArrowUpRight size={16} />
                </button>
              </div>
            )}

          </div>

          {/* Quick Balances */}
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-5 space-y-4">
            <div className="flex items-center gap-2">
              <Clock size={16} className="text-blue-500" />
              <h3 className="text-sm font-bold text-white">Quick Balances</h3>
            </div>
            <div className="space-y-2">
              {balances.map((b) => (
                <div key={b.label} className="flex items-center justify-between p-3 bg-[#0A0A0A] rounded-xl border border-[#1A1A1A]">
                  <span className={`text-xs font-bold ${b.color}`}>{b.label}</span>
                  <span className="text-sm font-bold text-white font-mono">{Number(b.value).toLocaleString()}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </AppLayout>
  );
}
