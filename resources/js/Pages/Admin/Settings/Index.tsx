import { Head, useForm } from '@inertiajs/react';
import AdminLayout from '@/Layouts/AdminLayout';
import { Save } from 'lucide-react';
import { useEffect } from 'react';

type SettingItem = {
  key: string;
  label: string;
  description: string;
  group: string;
  type: string;
  value: string;
  placeholder?: string | null;
};

type SettingGroup = {
  name: string;
  description?: string | null;
  settings: SettingItem[];
};

export default function SettingsIndex({ groups }: { groups: SettingGroup[] }) {
  const initialSettings = groups.flatMap((group) =>
    group.settings.map((setting) => ({
      key: setting.key,
      value: setting.value ?? '',
    })),
  );

  const { data, setData, post, processing } = useForm({
    settings: initialSettings as { key: string; value: string }[],
  });

  useEffect(() => {
    setData(
      'settings',
      groups.flatMap((group) =>
        group.settings.map((setting) => ({
          key: setting.key,
          value: setting.value ?? '',
        })),
      ),
    );
  }, [groups]);

  const updateSetting = (key: string, value: string) => {
    setData('settings', data.settings.map((setting) => setting.key === key ? { ...setting, value } : setting));
  };

  const submit = (e: React.FormEvent) => {
    e.preventDefault();
    post(route('admin.settings.update'));
  };

  const valueFor = (key: string, fallback: string) => data.settings.find((setting) => setting.key === key)?.value ?? fallback;

  return (
    <AdminLayout>
      <Head title="Admin - Settings" />
      <header className="mb-8 flex flex-col gap-1">
        <h1 className="text-3xl font-bold bg-gradient-to-r from-white to-zinc-500 bg-clip-text text-transparent tracking-tight">Platform Settings</h1>
        <p className="text-zinc-500 text-sm font-medium">Configure platform-wide contact, mail, fee, limit, and compliance settings.</p>
      </header>
      <form onSubmit={submit} className="space-y-6">
        {groups.map((group) => (
          <div key={group.name} className="bg-[#111] border border-[#1A1A1A] rounded-3xl p-6">
            <div className="mb-5">
              <h3 className="text-sm font-bold text-zinc-400 uppercase tracking-widest">{group.name}</h3>
              {group.description ? (
                <p className="mt-2 text-sm text-zinc-500">{group.description}</p>
              ) : null}
            </div>
            <div className="space-y-4">
              {group.settings.map((setting) => (
                <div key={setting.key} className="grid gap-3 border-t border-[#1A1A1A] pt-4 md:grid-cols-[minmax(0,1fr)_340px] md:items-start">
                  <div>
                    <label htmlFor={setting.key} className="text-sm font-semibold text-white">{setting.label}</label>
                    <p className="mt-1 text-sm text-zinc-500">{setting.description}</p>
                  </div>
                  {setting.type === 'textarea' ? (
                    <textarea
                      id={setting.key}
                      value={valueFor(setting.key, setting.value)}
                      onChange={(e) => updateSetting(setting.key, e.target.value)}
                      placeholder={setting.placeholder ?? undefined}
                      rows={4}
                      className="w-full resize-none rounded-2xl border border-[#1A1A1A] bg-[#0A0A0A] px-4 py-3 text-sm text-white focus:border-zinc-600 focus:outline-none"
                    />
                  ) : (
                    <input
                      id={setting.key}
                      type={setting.type === 'number' ? 'number' : setting.type}
                      step={setting.type === 'number' ? 'any' : undefined}
                      value={valueFor(setting.key, setting.value)}
                      onChange={(e) => updateSetting(setting.key, e.target.value)}
                      placeholder={setting.placeholder ?? undefined}
                      className="w-full rounded-2xl border border-[#1A1A1A] bg-[#0A0A0A] px-4 py-3 text-sm text-white focus:border-zinc-600 focus:outline-none"
                    />
                  )}
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
