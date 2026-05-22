import { Head, Link, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Search, ChevronDown } from 'lucide-react';

interface User {
  id: number;
  name: string;
  email: string;
  account_tier: string;
  kyc_level: string;
  created_at: string;
}

interface Props {
  users: { data: User[]; current_page: number; last_page: number; total: number };
  filters: { search?: string; tier?: string; kyc?: string };
}

export default function UsersIndex({ users, filters }: Props) {
  const { get, setData, data } = useForm(filters);

  return (
    <AdminLayout>
      <Head title="Admin - Users" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">User Management</h1>
        <p className="text-zinc-500 text-sm font-medium">View, filter, and manage platform user accounts.</p>
      </header>

      <div className="bg-[#111] border border-[#1A1A1A] rounded-3xl overflow-hidden">
        <div className="p-4 border-b border-[#1A1A1A] flex items-center gap-4">
          <div className="flex-1 relative">
            <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-500" />
            <input
              value={data.search ?? ''}
              onChange={e => setData('search', e.target.value)}
              onKeyDown={e => e.key === 'Enter' && get(route('admin.users.index'))}
              placeholder="Search users..."
              className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl pl-10 pr-4 py-2 text-sm text-white placeholder-zinc-500 focus:outline-none focus:border-zinc-600"
            />
          </div>
          <select value={data.tier ?? ''} onChange={e => { setData('tier', e.target.value); get(route('admin.users.index')); }}
            className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-3 py-2 text-sm text-white focus:outline-none">
            <option value="">All Tiers</option>
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
          <select value={data.kyc ?? ''} onChange={e => { setData('kyc', e.target.value); get(route('admin.users.index')); }}
            className="bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-3 py-2 text-sm text-white focus:outline-none">
            <option value="">All KYC</option>
            <option value="unverified">Unverified</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-left">
            <thead>
              <tr className="border-b border-[#1A1A1A]">
                <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Name</th>
                <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Email</th>
                <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Role</th>
                <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">KYC</th>
                <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Joined</th>
                <th className="px-6 py-4 text-[10px] font-bold text-zinc-500 uppercase tracking-widest text-right">Actions</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-[#0A0A0A]">
              {users.data.map(user => (
                <tr key={user.id} className="hover:bg-[#151515] transition-colors">
                  <td className="px-6 py-4"><span className="text-sm font-bold text-white">{user.name}</span></td>
                  <td className="px-6 py-4 text-xs text-zinc-400">{user.email}</td>
                  <td className="px-6 py-4"><span className={`text-xs font-bold px-2 py-1 rounded-full ${user.account_tier === 'admin' ? 'bg-purple-500/10 text-purple-500' : 'bg-zinc-500/10 text-zinc-400'}`}>{user.account_tier}</span></td>
                  <td className="px-6 py-4">
                    <span className={`text-xs font-bold px-2 py-1 rounded-full ${user.kyc_level === 'verified' ? 'bg-emerald-500/10 text-emerald-500' : user.kyc_level === 'pending' ? 'bg-amber-500/10 text-amber-500' : user.kyc_level === 'rejected' ? 'bg-rose-500/10 text-rose-500' : 'bg-zinc-500/10 text-zinc-400'}`}>{user.kyc_level}</span>
                  </td>
                  <td className="px-6 py-4 text-xs text-zinc-500 font-mono">{user.created_at}</td>
                  <td className="px-6 py-4 text-right">
                    <Link href={route('admin.users.show', user.id)} className="text-xs font-bold text-blue-500 hover:text-blue-400">View</Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {users.last_page > 1 && (
          <div className="p-4 border-t border-[#1A1A1A] flex items-center justify-between text-sm text-zinc-500">
            <span>Page {users.current_page} of {users.last_page} ({users.total} total)</span>
            <div className="flex gap-2">
              {users.current_page > 1 && <Link href={route('admin.users.index', { ...filters, page: users.current_page - 1 })} className="px-3 py-1 bg-[#0A0A0A] rounded-lg hover:text-white">Prev</Link>}
              {users.current_page < users.last_page && <Link href={route('admin.users.index', { ...filters, page: users.current_page + 1 })} className="px-3 py-1 bg-[#0A0A0A] rounded-lg hover:text-white">Next</Link>}
            </div>
          </div>
        )}
      </div>
    </AdminLayout>
  );
}
