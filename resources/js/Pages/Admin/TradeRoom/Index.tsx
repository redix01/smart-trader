import { useEffect, useMemo, useRef, useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import TradeOrderPanel, { Balance, MarketType, Pair } from '@/Components/TradeOrderPanel';
import { History, UserRound } from 'lucide-react';

interface AdminUser {
  id: number;
  name: string;
  email: string;
}

interface TradeHistory {
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
}

interface Props {
  users: AdminUser[];
  selectedUser: AdminUser | null;
  pairs: Pair[];
  stockPairs: Pair[];
  forexPairs: Pair[];
  defaultPair: Pair | null;
  defaultMarketType: MarketType;
  balances: Balance[];
  history: TradeHistory[];
}

export default function TradeRoomIndex({
  users,
  selectedUser,
  pairs,
  stockPairs,
  forexPairs,
  defaultPair,
  defaultMarketType,
  balances,
  history,
}: Props) {
  const [marketType, setMarketType] = useState<MarketType>(defaultMarketType);
  const [activePair, setActivePair] = useState<Pair | undefined>(defaultPair ?? pairs[0]);
  const previousMarketTypeRef = useRef<MarketType>(defaultMarketType);
  const extraData = useMemo(() => ({ user_id: selectedUser?.id ?? '' }), [selectedUser?.id]);

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

  const changeUser = (userId: string) => {
    router.get(route('admin.trade-room.index'), { user_id: userId }, {
      preserveScroll: true,
      preserveState: false,
    });
  };

  return (
    <AdminLayout>
      <Head title="Admin - Trade Room" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Trade Room</h1>
        <p className="text-zinc-500 text-sm font-medium">Select a user and place market or limit trades into their account.</p>
      </header>

      <div className="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <div className="xl:col-span-3 space-y-6">
          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-5">
            <div className="flex items-center gap-2 mb-4">
              <UserRound size={16} className="text-amber-500" />
              <h2 className="text-sm font-bold text-white">User Account</h2>
            </div>
            <select
              value={selectedUser?.id ?? ''}
              onChange={(event) => changeUser(event.target.value)}
              className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm font-bold text-white outline-none"
            >
              {users.map(user => (
                <option key={user.id} value={user.id}>{user?.name ?? 'Unknown'} - {user?.email ?? ''}</option>
              ))}
            </select>
            {selectedUser && (
              <div className="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-2xl border border-[#1A1A1A] bg-[#0A0A0A] p-4">
                <div>
                  <p className="text-xs text-zinc-500">Selected User</p>
                  <p className="text-white font-bold">{selectedUser.name}</p>
                  <p className="text-xs text-zinc-500">{selectedUser.email}</p>
                </div>
                <Link href={route('admin.users.show', selectedUser.id)} className="text-xs font-bold text-blue-500 hover:text-blue-400">
                  View Profile
                </Link>
              </div>
            )}
          </div>

          <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
            <div className="flex items-center gap-2 p-5 border-b border-[#1A1A1A]">
              <History size={16} className="text-blue-500" />
              <h2 className="text-sm font-bold text-white">Recent User Trades</h2>
            </div>
            {history.length === 0 ? (
              <div className="py-12 text-center text-zinc-500">
                <p className="text-sm font-medium">No trade history for this user</p>
              </div>
            ) : (
              <div className="overflow-x-auto">
                <table className="w-full text-left">
                  <thead>
                    <tr className="border-b border-[#1A1A1A]">
                      <th className="px-5 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Trade</th>
                      <th className="px-5 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Amount</th>
                      <th className="px-5 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Total</th>
                      <th className="px-5 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Date</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-[#0A0A0A]">
                    {history.map(entry => (
                      <tr key={entry.id} className="hover:bg-[#151515] transition-colors">
                        <td className="px-5 py-4">
                          <span className={`text-xs font-bold px-2 py-1 rounded-full ${entry.side === 'buy' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500'}`}>{entry.side}</span>
                          <span className="ml-3 text-sm text-white font-bold">{entry.pair}</span>
                        </td>
                        <td className="px-5 py-4 text-right text-sm text-white font-mono">{entry.amount}</td>
                        <td className="px-5 py-4 text-right text-sm text-zinc-400 font-mono">${entry.total}</td>
                        <td className="px-5 py-4 text-xs text-zinc-500 font-mono">{entry.date}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        </div>

        <div className="xl:col-span-2">
          <TradeOrderPanel
            pairs={pairs}
            stockPairs={stockPairs}
            forexPairs={forexPairs}
            marketType={marketType}
            activePair={activePair}
            balances={balances}
            submitRoute={route('admin.trade-room.store')}
            submitLabelPrefix="Place "
            extraData={extraData}
            onMarketTypeChange={setMarketType}
            onActivePairChange={setActivePair}
          />
        </div>
      </div>
    </AdminLayout>
  );
}
