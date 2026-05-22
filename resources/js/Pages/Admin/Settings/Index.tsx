import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Save } from 'lucide-react';
import { useEffect } from 'react';

export default function SettingsIndex({ settings }: { settings: Record<string, { key: string; value: string; group: string }[]> }) {
  const { data, setData, post, processing } = useForm({ settings: [] as { key: string; value: string; group: string }[] });

  useEffect(() => {
    const flat = Object.entries(settings).flatMap(([group, items]) =>
      items.map(i => ({ key: i.key, value: i.value, group }))
    );
    if (flat.length > 0) setData('settings', flat);
  }, []);

  const updateSetting = (key: string, value: string) => {
    setData('settings', data.settings.map(s => s.key === key ? { ...s, value } : s));
  };

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('admin.settings.update'));
  };

  const defaultSettings: Record<string, { key: string; value: string; group: string }[]> = Object.keys(settings).length > 0
    ? settings
    : {
        'General': [
          { key: 'site_name', value: 'Cognizant-Pro', group: 'General' },
          { key: 'site_description', value: 'Trading Platform', group: 'General' },
          { key: 'support_email', value: 'support@acuity.com', group: 'General' },
        ],
        'Fees': [
          { key: 'trading_fee', value: '0.1', group: 'Fees' },
          { key: 'swap_fee', value: '0.5', group: 'Fees' },
          { key: 'withdrawal_fee', value: '2.0', group: 'Fees' },
        ],
        'Limits': [
          { key: 'min_deposit', value: '10', group: 'Limits' },
          { key: 'min_withdrawal', value: '5', group: 'Limits' },
          { key: 'max_withdrawal', value: '50000', group: 'Limits' },
        ],
      };

  const grouped = Object.keys(settings).length > 0 ? settings : defaultSettings;

  return (
    <AdminLayout>
      <Head title="Admin - Settings" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Platform Settings</h1>
        <p className="text-zinc-500 text-sm font-medium">Configure platform-wide settings, fees, and content.</p>
      </header>
      <form onSubmit={submit} className="space-y-6">
        {Object.entries(grouped).map(([group, items]) => (
          <div key={group} className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
            <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest mb-4">{group}</h3>
            <div className="space-y-3">
              {items.map(setting => (
                <div key={setting.key} className="flex items-center justify-between">
                  <label htmlFor={setting.key} className="text-sm text-white capitalize">{setting.key.replace(/_/g, ' ')}</label>
                  <input
                    id={setting.key}
                    value={data.settings.find(s => s.key === setting.key)?.value ?? setting.value}
                    onChange={e => updateSetting(setting.key, e.target.value)}
                    className="w-64 bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-2 text-sm text-white text-right font-mono focus:outline-none focus:border-zinc-600"
                  />
                </div>
              ))}
            </div>
          </div>
        ))}
        <div className="flex justify-end">
          <button type="submit" disabled={processing}
            className="flex items-center gap-2 px-6 py-3 bg-white text-black rounded-xl text-sm font-bold hover:bg-zinc-200 transition-all disabled:opacity-50">
            <Save size={16} /> Save Settings
          </button>
        </div>
      </form>
    </AdminLayout>
  );
}
