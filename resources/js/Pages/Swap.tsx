import { useState, useEffect } from 'react';
import { Head, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowDownUp, Info, ChevronDown, Wallet } from 'lucide-react';

interface Pair {
  from: string;
  to: string;
  rate: number;
}

interface SwapProps {
  pairs: Pair[];
}

const icons: Record<string, string> = {
  USD: 'https://img.icons8.com/color/48/usa-circular.png',
  BTC: 'https://assets.coingecko.com/coins/images/1/small/bitcoin.png',
  ETH: 'https://assets.coingecko.com/coins/images/279/small/ethereum.png',
  USDT: 'https://assets.coingecko.com/coins/images/325/small/tether.png',
};

export default function Swap({ pairs }: SwapProps) {
  const { data, setData, post, processing, errors } = useForm({
    from: 'USD',
    to: 'BTC',
    amount: '',
  });
  const [quote, setQuote] = useState<{ rate: number; to_amount: number } | null>(null);

  const findRate = (from: string, to: string) => {
    const pair = pairs.find(p => p.from === from && p.to === to);
    return pair?.rate ?? null;
  };

  useEffect(() => {
    const rate = findRate(data.from, data.to);
    if (rate && data.amount) {
      setQuote({ rate, to_amount: parseFloat(data.amount) * rate * 0.9995 });
    } else {
      setQuote(null);
    }
  }, [data.from, data.to, data.amount, pairs]);

  const handleSwapSelection = () => {
    setData({ ...data, from: data.to, to: data.from });
  };

  const getIcon = (currency: string) => icons[currency] || '';

  return (
    <AppLayout>
      <Head title="Swap" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent capitalize tracking-tight">Swap</h1>
        <p className="text-zinc-500 text-sm font-medium">Instant balance conversion between supported assets.</p>
      </header>
      <div className="max-w-2xl mx-auto py-8">
        <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6 lg:p-8 space-y-8 relative overflow-hidden shadow-2xl">
          <div className="absolute top-0 right-0 w-32 h-32 bg-blue-600/5 rounded-full blur-3xl pointer-events-none" />
          <div className="space-y-6">
            <div className="space-y-2">
              <div className="flex justify-between items-center px-1">
                <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">From:</label>
              </div>
              <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4 flex items-center gap-4 group focus-within:border-blue-600/50 transition-all">
                <div className="flex items-center gap-3 bg-[#111] px-3 py-2 rounded-xl border border-[#222] min-w-[120px]">
                  <img src={getIcon(data.from)} className="w-6 h-6 rounded-full" alt="" />
                  <span className="text-sm font-bold text-white">{data.from}</span>
                  <ChevronDown size={14} className="ml-auto text-zinc-500" />
                </div>
                <input type="number" value={data.amount} onChange={(e) => setData('amount', e.target.value)} placeholder="0.00"
                  className="flex-1 bg-transparent border-none outline-none text-xl font-bold text-white placeholder-zinc-700 font-mono text-right" />
              </div>
            </div>

            <div className="relative h-2 flex items-center justify-center">
              <div className="absolute inset-0 flex items-center"><div className="w-full border-t border-[#1A1A1A]"></div></div>
              <button onClick={handleSwapSelection}
                className="relative z-10 w-10 h-10 bg-blue-600 rounded-xl border-4 border-[#111] flex items-center justify-center text-white hover:scale-110 active:scale-95 transition-all shadow-lg">
                <ArrowDownUp size={18} />
              </button>
            </div>

            <div className="space-y-2">
              <div className="flex justify-between items-center px-1">
                <label className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Swap to:</label>
              </div>
              <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4 flex items-center gap-4 group focus-within:border-blue-600/50 transition-all">
                <div className="flex items-center gap-3 bg-[#111] px-3 py-2 rounded-xl border border-[#222] min-w-[120px]">
                  <img src={getIcon(data.to)} className="w-6 h-6 rounded-full" alt="" />
                  <span className="text-sm font-bold text-white">{data.to}</span>
                  <ChevronDown size={14} className="ml-auto text-zinc-500" />
                </div>
                <div className="flex-1 text-xl font-bold text-zinc-600 font-mono text-right">
                  {quote ? quote.to_amount.toFixed(6) : '0.000000'}
                </div>
              </div>
            </div>
          </div>

          <div className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-2xl p-4 space-y-3">
            <div className="flex justify-between text-xs">
              <span className="text-zinc-500">Exchange Rate</span>
              <span className="text-emerald-500 font-mono">1 {data.from} ≈ {quote?.rate?.toFixed(6) || '0.000000'} {data.to}</span>
            </div>
            <div className="flex justify-between text-xs">
              <span className="text-zinc-500">Network Fee</span>
              <span className="text-zinc-400 font-mono">0.05%</span>
            </div>
            <div className="pt-2 border-t border-[#1A1A1A] flex items-center gap-2 text-zinc-500">
              <Info size={14} />
              <span className="text-[10px] font-medium leading-relaxed italic">Estimated price based on current market activity.</span>
            </div>
          </div>

          <form onSubmit={() => post(route('swap.store'))} className="space-y-4">
            <div className="flex items-center gap-2 text-[11px] font-medium text-zinc-500 px-1">
              <Wallet size={14} className="text-blue-500" />
              <span>Rate: {quote?.rate?.toFixed(6) || '0.000000'}</span>
            </div>
            {errors.amount && <p className="text-xs text-rose-500">{errors.amount}</p>}
            <button type="submit" disabled={processing || !data.amount}
              className="w-full py-5 bg-blue-600 text-white font-bold rounded-2xl hover:bg-blue-500 active:scale-[0.98] transition-all flex items-center justify-center gap-2 shadow-[0_4px_20px_rgba(37,99,235,0.2)] disabled:opacity-50 disabled:cursor-not-allowed">
              {processing ? 'Processing...' : 'Swap Assets'}
            </button>
          </form>
        </div>
      </div>
    </AppLayout>
  );
}
