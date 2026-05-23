import { Link, usePage } from '@inertiajs/react';
import BrandLogo from '@/Components/BrandLogo';
import { 
  LayoutDashboard, 
  Wallet, 
  BarChart3, 
  ArrowLeftRight, 
  ArrowUpCircle, 
  Database, 
  ArrowDownCircle, 
  Zap, 
  Users, 
  Building2, 
  UserCircle, 
  LogOut,
  Pickaxe,
  Shield,
  LifeBuoy,
} from 'lucide-react';

interface SidebarProps {
  isOpen: boolean;
  setIsOpen: (isOpen: boolean) => void;
  isMobile: boolean;
}

const menuItems = [
  { label: 'Dashboard', icon: LayoutDashboard, route: 'dashboard' },
  { label: 'Assets', icon: Wallet, route: 'assets' },
  { label: 'Markets', icon: BarChart3, route: 'markets' },
  { label: 'Trades', icon: Zap, route: 'trades' },
  { label: 'Swap', icon: ArrowLeftRight, route: 'swap' },
  { label: 'Deposit', icon: ArrowUpCircle, route: 'deposit' },
  { label: 'Stakes', icon: Database, route: 'stakes' },
  { label: 'Withdraw', icon: ArrowDownCircle, route: 'withdraw' },
  { label: 'Mining', icon: Pickaxe, route: 'mining' },
  { label: 'Copy Experts', icon: Users, route: 'experts' },
  { label: 'Real Estate', icon: Building2, route: 'realestate' },
  { label: 'Support', icon: LifeBuoy, route: 'support' },
];

export default function Sidebar({ isOpen, setIsOpen, isMobile }: SidebarProps) {
  const { url, props } = usePage();
  const user = props.auth?.user as Record<string, any> | undefined;
  const isAdmin = user?.account_tier === 'admin' || user?.email === 'admin@example.com';

  const isActive = (routeName: string) => {
    try {
      const generated = route(routeName);
      // route() returns a full URL; usePage().url is pathname only — normalise both
      const routePath = generated.startsWith('http')
        ? new URL(generated).pathname
        : generated.split('?')[0];
      const currentPath = url.split('?')[0];
      return currentPath === routePath || currentPath.startsWith(routePath + '/');
    } catch {
      return false;
    }
  };

  return (
    <>
      {isMobile && isOpen && (
        <div
          onClick={() => setIsOpen(false)}
          className="fixed inset-0 bg-black/80 backdrop-blur-md z-40 lg:hidden"
        />
      )}
      <aside
        className={`fixed top-0 left-0 bottom-0 bg-[#0C0C0C] border-r border-[#1F1F1F] z-50 flex flex-col w-[260px] transition-transform duration-300 ${
          (!isMobile || isOpen) ? 'translate-x-0' : '-translate-x-full'
        }`}
      >
        <div className="p-6 border-b border-[#1A1A1A] flex items-center justify-between">
          <div className="flex items-center gap-3">
            <BrandLogo className="h-10 w-auto object-contain" />
          </div>
          {isMobile && (
            <button onClick={() => setIsOpen(false)} className="lg:hidden p-2 text-zinc-500 hover:text-white transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          )}
        </div>

        <nav className="flex-1 overflow-y-auto py-6 px-4 space-y-1.5 custom-scrollbar">
          {menuItems.map((item) => {
            const active = isActive(item.route);
            const Icon = item.icon;

            return (
              <Link
                key={item.route}
                href={route(item.route)}
                onClick={() => isMobile && setIsOpen(false)}
                className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative ${
                  active
                    ? 'bg-blue-600/10 text-white'
                    : 'text-zinc-500 hover:text-white hover:bg-[#1A1A1A]/80'
                }`}
              >
                {active && <div className="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-blue-600 rounded-full shadow-[0_0_8px_rgba(37,99,235,0.6)]" />}
                <Icon size={18} className={active ? 'text-blue-500' : 'text-zinc-500 group-hover:text-zinc-300 transition-colors'} />
                <span className={`font-medium text-sm tracking-wide ${active ? 'text-white' : ''}`}>{item.label}</span>
              </Link>
            );
          })}

          {isAdmin && (
            <Link
              href={route('admin.dashboard')}
              onClick={() => isMobile && setIsOpen(false)}
              className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative ${
                isActive('admin.dashboard')
                  ? 'bg-amber-600/10 text-white'
                  : 'text-zinc-500 hover:text-white hover:bg-[#1A1A1A]/80'
              }`}
            >
              {isActive('admin.dashboard') && <div className="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-amber-600 rounded-full shadow-[0_0_8px_rgba(217,119,6,0.6)]" />}
              <Shield size={18} className={isActive('admin.dashboard') ? 'text-amber-500' : 'text-zinc-500 group-hover:text-zinc-300 transition-colors'} />
              <span className={`font-medium text-sm tracking-wide ${isActive('admin.dashboard') ? 'text-white' : ''}`}>Admin</span>
            </Link>
          )}
        </nav>

        <div className="p-4 border-t border-[#1A1A1A] space-y-1.5">
          <Link
            href={route('profile.edit')}
            onClick={() => isMobile && setIsOpen(false)}
            className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group relative ${
              isActive('profile.edit')
                ? 'bg-blue-600/10 text-white'
                : 'text-zinc-500 hover:text-white hover:bg-[#1A1A1A]/80'
            }`}
          >
            {isActive('profile.edit') && <div className="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 bg-blue-600 rounded-full shadow-[0_0_8px_rgba(37,99,235,0.6)]" />}
            <UserCircle size={18} className={isActive('profile.edit') ? 'text-blue-500' : 'text-zinc-500 group-hover:text-zinc-300 transition-colors'} />
            <span className={`font-medium text-sm tracking-wide ${isActive('profile.edit') ? 'text-white' : ''}`}>Profile</span>
          </Link>
          <Link
            href={route('logout')}
            method="post"
            as="button"
            onClick={() => isMobile && setIsOpen(false)}
            className="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 group text-zinc-500 hover:text-white hover:bg-[#1A1A1A]/80"
          >
            <LogOut size={18} />
            <span className="font-medium text-sm tracking-wide">Logout</span>
          </Link>
        </div>

        <div className="p-4 border-t border-[#1A1A1A]">
          <div className="bg-[#141414] rounded-2xl p-4 flex items-center gap-3">
            <div className="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center font-bold text-white shadow-lg">
              {user?.name?.charAt(0)?.toUpperCase() || 'U'}
            </div>
            <div className="flex flex-col overflow-hidden">
              <span className="text-sm font-bold text-white truncate">{user?.name || 'User'}</span>
              <div className="flex items-center gap-1.5">
                <div className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse" />
                <span className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">{isAdmin ? 'Admin' : 'Verified Pro'}</span>
              </div>
            </div>
          </div>
        </div>
      </aside>
    </>
  );
}
