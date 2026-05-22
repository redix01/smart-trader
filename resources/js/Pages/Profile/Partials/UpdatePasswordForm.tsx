import InputError from '@/Components/InputError';
import { Transition } from '@headlessui/react';
import { useForm } from '@inertiajs/react';
import { FormEventHandler, useRef } from 'react';
import { Lock, CheckCircle } from 'lucide-react';

export default function UpdatePasswordForm({ className = '' }: { className?: string }) {
    const passwordInput = useRef<HTMLInputElement>(null);
    const currentPasswordInput = useRef<HTMLInputElement>(null);

    const { data, setData, errors, put, reset, processing, recentlySuccessful } = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const updatePassword: FormEventHandler = (e) => {
        e.preventDefault();
        put(route('password.update'), {
            preserveScroll: true,
            onSuccess: () => reset(),
            onError: (errors) => {
                if (errors.password) {
                    reset('password', 'password_confirmation');
                    passwordInput.current?.focus();
                }
                if (errors.current_password) {
                    reset('current_password');
                    currentPasswordInput.current?.focus();
                }
            },
        });
    };

    return (
        <section className={className}>
            <div className="mb-6">
                <h2 className="text-lg font-bold text-white">Update Password</h2>
                <p className="mt-1 text-sm text-zinc-500">Use a long, random password to keep your account secure.</p>
            </div>

            <form onSubmit={updatePassword} className="space-y-5">
                <div className="space-y-1.5">
                    <label htmlFor="current_password" className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                        Current Password
                    </label>
                    <input
                        id="current_password"
                        ref={currentPasswordInput}
                        type="password"
                        value={data.current_password}
                        onChange={(e) => setData('current_password', e.target.value)}
                        autoComplete="current-password"
                        className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-700 focus:border-blue-600 outline-none transition-colors"
                    />
                    <InputError message={errors.current_password} />
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div className="space-y-1.5">
                        <label htmlFor="password" className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                            New Password
                        </label>
                        <input
                            id="password"
                            ref={passwordInput}
                            type="password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            autoComplete="new-password"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-700 focus:border-blue-600 outline-none transition-colors"
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="space-y-1.5">
                        <label htmlFor="password_confirmation" className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                            Confirm Password
                        </label>
                        <input
                            id="password_confirmation"
                            type="password"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            autoComplete="new-password"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-700 focus:border-blue-600 outline-none transition-colors"
                        />
                        <InputError message={errors.password_confirmation} />
                    </div>
                </div>

                <div className="flex items-center gap-4 pt-1">
                    <button
                        type="submit"
                        disabled={processing}
                        className="flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-500 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Lock size={15} />
                        Update Password
                    </button>
                    <Transition
                        show={recentlySuccessful}
                        enter="transition ease-in-out duration-200"
                        enterFrom="opacity-0 translate-y-1"
                        enterTo="opacity-100 translate-y-0"
                        leave="transition ease-in-out duration-150"
                        leaveTo="opacity-0"
                    >
                        <p className="text-sm text-emerald-500 font-medium flex items-center gap-1.5">
                            <CheckCircle size={14} /> Updated
                        </p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}
