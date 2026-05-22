import { Menu, BarChart3 } from 'lucide-react';
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
