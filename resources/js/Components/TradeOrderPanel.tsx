import { useEffect, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { ArrowUpRight, Clock, TrendingDown, TrendingUp } from 'lucide-react';

export interface Pair {
  id: number;
  name: string;
  price: string;
  change: string;
  up: boolean;
  icon: string;
}

export interface Balance {
  label: string;
  value: string | number;
  color: string;
}

export type OrderType = 'Market' | 'Limit';
export type OrderMode = 'buy' | 'sell';
export type MarketType = 'crypto' | 'stocks' | 'forex';

const PCT_BTNS = ['25%', '50%', '75%', '100%'];

interface TradeOrderPanelProps {
  pairs: Pair[];
  stockPairs: Pair[];
  forexPairs: Pair[];
  marketType: MarketType;
  activePair?: Pair;
  balances: Balance[];
  submitRoute: string;
  submitLabelPrefix?: string;
  extraData?: Record<string, string | number>;
  onMarketTypeChange: (marketType: MarketType) => void;
  onActivePairChange: (pair: Pair) => void;
}

export default function TradeOrderPanel({
  pairs,
  stockPairs,
  forexPairs,
  marketType,
  activePair,
  balances,
  submitRoute,
  submitLabelPrefix = '',
  extraData = {},
  onMarketTypeChange,
  onActivePairChange,
}: TradeOrderPanelProps) {
  const [orderType, setOrderType] = useState<OrderType>('Market');
  const [orderMode, setOrderMode] = useState<OrderMode>('buy');
  const [price, setPrice] = useState('');
  const [amount, setAmount] = useState('');
  const { data, setData, post, processing, errors, reset } = useForm<Record<string, any>>({
    ...extraData,
    market_type: marketType,
    pair_id: activePair?.id ?? 0,
    pair: activePair?.name ?? '',
    side: 'buy',
    type: 'Market',
    amount: '',
    price: '',
  });

  const currentPairs = marketType === 'crypto' ? pairs : marketType === 'stocks' ? stockPairs : forexPairs;
  const nameParts = (activePair?.name ?? 'BTC/USDT').split('/');
  const baseAsset = nameParts[0] ?? 'BTC';
  const quoteAsset = nameParts[1] ?? 'USDT';
  const usdBalance = balances.find(b => b.label === 'USD')?.value ?? 0;
  const baseBalance = balances.find(b => b.label === baseAsset)?.value ?? 0;
  const availableBalance = orderMode === 'buy' ? usdBalance : baseBalance;
  const availableLabel = orderMode === 'buy' ? 'USD' : baseAsset;
  const limitPrice = price ? parseFloat(price.replace(/,/g, '')) : parseFloat((activePair?.price ?? '0').replace(/,/g, ''));
  const total = amount ? (limitPrice * parseFloat(amount)).toFixed(2) : '0.00';

  useEffect(() => {
    if (!activePair && currentPairs[0]) {
      onActivePairChange(currentPairs[0]);
    }
  }, [activePair, currentPairs, onActivePairChange]);

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

  useEffect(() => {
    Object.entries(extraData).forEach(([key, value]) => setData(key, value));
  }, [extraData, setData]);

  const applyPct = (p: string) => {
    const max = parseFloat(String(availableBalance)) || 0;
    const factor = parseInt(p) / 100;
    const cp = parseFloat((activePair?.price ?? '1').replace(/,/g, ''));
    const nextAmount = orderMode === 'buy' ? (max * factor / cp).toFixed(6) : (max * factor).toFixed(6);
    setAmount(nextAmount);
    setData('amount', nextAmount);
  };

  const submitTrade = () => {
    post(submitRoute, {
      preserveScroll: true,
      onSuccess: () => {
        reset('amount', 'price');
        setAmount('');
        setPrice('');
      },
    });
  };

  return (
    <div className="space-y-6">
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-5 space-y-4">
        <div>
          <p className="text-[9px] font-bold text-zinc-600 uppercase tracking-widest mb-2">Market</p>
          <div className="flex bg-[#0A0A0A] p-1 rounded-2xl border border-[#1A1A1A]">
            {(['crypto', 'stocks', 'forex'] as MarketType[]).map((m) => (
              <button key={m} onClick={() => onMarketTypeChange(m)}
                className={`flex-1 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all ${
                  marketType === m ? 'bg-[#1A1A1A] text-white shadow' : 'text-zinc-500 hover:text-zinc-300'
                }`}>
                {m}
              </button>
            ))}
          </div>
        </div>

        <div className="flex items-center justify-between gap-3 pb-3 border-b border-[#1A1A1A]">
          <select
            value={activePair?.id}
            onChange={(e) => {
              const pair = currentPairs.find(cp => cp.id === Number(e.target.value));
              if (pair) onActivePairChange(pair);
            }}
            className="min-w-0 flex-1 bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-3 py-2 text-sm font-bold text-white outline-none cursor-pointer"
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

        <div className="space-y-4">
          {errors.user_id && <p className="text-xs text-rose-500">{errors.user_id}</p>}
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

          <button type="button" onClick={submitTrade} disabled={processing || !amount || (orderType === 'Limit' && !price)} className={`w-full py-4 font-bold rounded-2xl transition-all flex items-center justify-center gap-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed ${
            orderMode === 'buy'
              ? 'bg-emerald-500 text-black hover:bg-emerald-400 shadow-[0_0_30px_rgba(16,185,129,0.2)]'
              : 'bg-rose-600 text-white hover:bg-rose-500 shadow-[0_0_30px_rgba(225,29,72,0.2)]'
          }`}>
            {orderMode === 'buy' ? <TrendingUp size={16} /> : <TrendingDown size={16} />}
            {processing ? 'Processing...' : `${submitLabelPrefix}${orderMode === 'buy' ? 'Buy' : 'Sell'} ${baseAsset}`}
            <ArrowUpRight size={16} />
          </button>
        </div>
      </div>

      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-5 space-y-4">
        <div className="flex items-center gap-2">
          <Clock size={16} className="text-blue-500" />
          <h3 className="text-sm font-bold text-white">Quick Balances</h3>
        </div>
        <div className="space-y-2">
          {balances.length === 0 ? (
            <p className="text-sm text-zinc-500">No balances available</p>
          ) : balances.map((b) => (
            <div key={b.label} className="flex items-center justify-between p-3 bg-[#0A0A0A] rounded-xl border border-[#1A1A1A]">
              <span className={`text-xs font-bold ${b.color}`}>{b.label}</span>
              <span className="text-sm font-bold text-white font-mono">{Number(b.value).toLocaleString()}</span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
