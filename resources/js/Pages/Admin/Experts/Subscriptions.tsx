import { Head, router } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';

interface Subscriber {
  id: number; user: { id: number; name: string; email: string };
  allocation_amount: string; current_value: string;
  status: string; created_at: string; cancelled_at: string | null;
}

export default function ExpertSubscriptions({ expert, subscriptions }: { expert: { id: number; name: string }; subscriptions: { data: Subscriber[] } }) {
  const cancel = (sub: Subscriber) => {
    if (confirm(`Cancel ${sub.user.name}'s subscription to ${expert.name}? No profits will be transferred.`)) {
      router.post(route('admin.experts.subscriptions.cancel', sub.id));
    }
  };

  return (
    <AdminLayout>
      <Head title={`Admin - ${expert.name} Subscriptions`} />
      <header className="mb-8">
        <div className="flex flex-col gap-1">
          <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">{expert.name}</h1>
          <p className="text-zinc-500 text-sm font-medium">Manage copy-trading subscribers for this expert strategy.</p>
        </div>
      </header>
      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="overflow-x-auto">
        <table className="w-full text-left">
          <thead><tr className="border-b border-[#1A1A1A]">
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">User</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Allocated</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Current Value</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Status</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase">Created</th>
            <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase text-right">Actions</th>
          </tr></thead>
          <tbody className="divide-y divide-[#0A0A0A]">
            {subscriptions.data.map(sub => (
              <tr key={sub.id} className="hover:bg-[#151515] transition-colors">
                <td className="px-6 py-4">
                  <div className="text-sm font-bold text-white">{sub.user.name}</div>
                  <div className="text-xs text-zinc-500">{sub.user.email}</div>
                </td>
                <td className="px-6 py-4 text-sm text-zinc-400 font-mono">${sub.allocation_amount}</td>
                <td className="px-6 py-4 text-sm text-zinc-400 font-mono">${sub.current_value}</td>
                <td className="px-6 py-4">
                  <span className={`text-xs font-bold px-2 py-1 rounded-full ${sub.status === 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-zinc-500/10 text-zinc-400'}`}>
                    {sub.status}
                  </span>
                </td>
                <td className="px-6 py-4 text-sm text-zinc-400">{sub.created_at}</td>
                <td className="px-6 py-4 text-right">
                  {sub.status === 'active' && (
                    <button onClick={() => cancel(sub)} className="px-3 py-1.5 text-xs font-bold text-rose-500 bg-rose-500/10 rounded-xl hover:bg-rose-500 hover:text-white transition-all">
                      Cancel
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
        </div>
      </div>
    </AdminLayout>
  );
}