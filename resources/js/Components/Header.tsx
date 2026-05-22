import { Bell, Globe, ChevronDown, Menu, BarChart3 } from 'lucide-react';
import { usePage } from '@inertiajs/react';

interface HeaderProps {
  onMenuClick: () => void;
}

export default function Header({ onMenuClick }: HeaderProps) {
  const { props } = usePage();
  const user = props.auth?.user as Record<string, any> | undefined;
  const isAdmin = user?.account_tier === 'admin';

  return (
    <header className="h-16 border-b border-[#1A1A1A] bg-[#0A0A0A]/80 backdrop-blur-md sticky top-0 z-30 px-6 flex items-center justify-between">
      <div className="flex items-center gap-4">
        <button
          onClick={onMenuClick}
          className="lg:hidden p-2 text-zinc-400 hover:text-white hover:bg-[#1A1A1A] rounded-lg"
        >
          <Menu size={24} />
        </button>
      </div>
      <div className="flex items-center gap-4 lg:gap-6">
        <div className="flex items-center gap-2 md:gap-4">
          {isAdmin && (
            <a
              href="/admin"
              target="_blank"
              rel="noopener noreferrer"
              className="flex items-center gap-2 px-4 py-2 bg-amber-600/10 border border-amber-600/20 rounded-xl text-amber-500 hover:bg-amber-600 hover:text-white transition-all text-sm font-bold"
            >
              <BarChart3 size={16} />
              <span className="hidden sm:inline">Admin Panel</span>
            </a>
          )}
          <button className="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-[#1A1A1A] hover:bg-[#1A1A1A] transition-colors text-zinc-300">
            <Globe size={18} />
            <span className="text-sm font-medium hidden sm:block">EN</span>
            <ChevronDown size={14} className="text-zinc-500" />
          </button>
          <button className="relative p-2.5 text-zinc-400 hover:text-white hover:bg-[#1A1A1A] rounded-full transition-all">
            <Bell size={20} />
            <span className="absolute top-2 right-2 w-2 h-2 bg-red-500 border-2 border-[#0A0A0A] rounded-full"></span>
          </button>
          <div className="h-8 w-[1px] bg-[#1A1A1A] mx-1"></div>
          <div className="flex items-center gap-3 pl-2">
            <img
              src="https://api.dicebear.com/7.x/avataaars/svg?seed=John"
              alt="Avatar"
              className="w-9 h-9 rounded-full ring-2 ring-[#222]"
            />
          </div>
        </div>
      </div>
    </header>
  );
}
