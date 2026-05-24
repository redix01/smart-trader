import { useState, useEffect, useRef } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import TradeOrderPanel, { Balance, MarketType, Pair } from '@/Components/TradeOrderPanel';

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

export default function Trades({ pairs, stockPairs, forexPairs, defaultPair, defaultMarketType, balances, history }: TradesProps) {
  const [marketType, setMarketType] = useState<MarketType>(defaultMarketType);
  const [activePair, setActivePair] = useState<Pair | undefined>(defaultPair ?? pairs[0]);
  const [activeTab, setActiveTab] = useState<'orders' | 'history'>('orders');

  const tickerRef = useRef<HTMLDivElement>(null);
  const chartRef = useRef<HTMLDivElement>(null);

  const nameParts = (activePair?.name ?? 'BTC/USDT').split('/');
  const baseAsset = nameParts[0] ?? 'BTC';
  const quoteAsset = nameParts[1] ?? 'USDT';

  const tvSymbol = (() => {
    if (marketType === 'stocks') return `NASDAQ:${baseAsset}`;
    if (marketType === 'forex') return `FX_IDC:${baseAsset}${quoteAsset}`;
    return `BINANCE:${baseAsset}${quoteAsset}`;
  })();

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
          <TradeOrderPanel
            pairs={pairs}
            stockPairs={stockPairs}
            forexPairs={forexPairs}
            marketType={marketType}
            activePair={activePair}
            balances={balances}
            submitRoute={route('trades.store')}
            onMarketTypeChange={setMarketType}
            onActivePairChange={setActivePair}
          />
        </div>
      </div>
    </AppLayout>
  );
}
