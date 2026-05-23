import { Head } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import LiveChatWidget from '@/Components/LiveChatWidget';
import { Headset, Mail, Phone } from 'lucide-react';

interface SupportProps {
  support: {
    email?: string | null;
    phone?: string | null;
    livechat_widget_code?: string | null;
  };
}

export default function Support({ support }: SupportProps) {
  return (
    <AppLayout>
      <Head title="Support" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-3xl font-bold tracking-tight text-transparent">Support</h1>
        <p className="text-sm font-medium text-zinc-500">Get help from the support team or start a live chat if it is enabled.</p>
      </header>

      <div className="grid gap-6 lg:grid-cols-[420px_minmax(0,1fr)]">
        <section className="rounded-3xl border border-[#1A1A1A] bg-[#111] p-6">
          <div className="mb-6 flex items-center gap-3">
            <div className="rounded-2xl bg-blue-600/10 p-3 text-blue-500">
              <Headset size={22} />
            </div>
            <div>
              <h2 className="text-lg font-bold text-white">Contact Support</h2>
              <p className="text-sm text-zinc-500">Use the details below for direct assistance.</p>
            </div>
          </div>

          <div className="space-y-4">
            <div className="rounded-2xl border border-[#1A1A1A] bg-[#0A0A0A] p-4">
              <div className="mb-2 flex items-center gap-2 text-zinc-400">
                <Mail size={16} />
                <span className="text-xs font-bold uppercase tracking-widest">Email</span>
              </div>
              <p className="text-sm font-medium text-white">{support.email || 'Not configured'}</p>
            </div>

            <div className="rounded-2xl border border-[#1A1A1A] bg-[#0A0A0A] p-4">
              <div className="mb-2 flex items-center gap-2 text-zinc-400">
                <Phone size={16} />
                <span className="text-xs font-bold uppercase tracking-widest">Phone</span>
              </div>
              <p className="text-sm font-medium text-white">{support.phone || 'Not configured'}</p>
            </div>
          </div>
        </section>

        <section className="rounded-3xl border border-[#1A1A1A] bg-[#111] p-6">
          <h2 className="text-lg font-bold text-white">Live Chat</h2>
          <p className="mt-2 text-sm text-zinc-500">If the admin has configured a live chat widget, it will load below.</p>
          <div className="mt-6 min-h-[420px] rounded-3xl border border-dashed border-[#262626] bg-[#0A0A0A] p-4">
            {support.livechat_widget_code?.trim() ? (
              <LiveChatWidget code={support.livechat_widget_code} />
            ) : (
              <div className="flex h-full min-h-[360px] items-center justify-center text-sm text-zinc-500">
                Live chat is not configured yet.
              </div>
            )}
          </div>
        </section>
      </div>
    </AppLayout>
  );
}
