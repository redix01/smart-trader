import InputError from '@/Components/InputError';
import { Transition } from '@headlessui/react';
import { Link, useForm, usePage } from '@inertiajs/react';
import { FormEventHandler } from 'react';
import { Save, CheckCircle } from 'lucide-react';

export default function UpdateProfileInformation({
    mustVerifyEmail,
    status,
    className = '',
}: {
    mustVerifyEmail: boolean;
    status?: string;
    className?: string;
}) {
    const user = usePage().props.auth.user;

    const { data, setData, patch, errors, processing, recentlySuccessful } =
        useForm({
            name: user.name,
            email: user.email,
        });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        patch(route('profile.update'));
    };

    return (
        <section className={className}>
            <div className="mb-6">
                <h2 className="text-lg font-bold text-white">Profile Information</h2>
                <p className="mt-1 text-sm text-zinc-500">Update your display name and email address.</p>
            </div>

            <form onSubmit={submit} className="space-y-5">
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div className="space-y-1.5">
                        <label htmlFor="name" className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                            Full Name
                        </label>
                        <input
                            id="name"
                            type="text"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            required
                            autoComplete="name"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-700 focus:border-blue-600 outline-none transition-colors"
                        />
                        <InputError message={errors.name} />
                    </div>

                    <div className="space-y-1.5">
                        <label htmlFor="email" className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                            Email Address
                        </label>
                        <input
                            id="email"
                            type="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            required
                            autoComplete="username"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-700 focus:border-blue-600 outline-none transition-colors"
                        />
                        <InputError message={errors.email} />
                    </div>
                </div>

                {mustVerifyEmail && user.email_verified_at === null && (
                    <div className="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-4">
                        <p className="text-sm text-amber-200/70">
                            Your email address is unverified.{' '}
                            <Link
                                href={route('verification.send')}
                                method="post"
                                as="button"
                                className="text-amber-500 font-bold hover:text-amber-400 underline underline-offset-2"
                            >
                                Resend verification email.
                            </Link>
                        </p>
                        {status === 'verification-link-sent' && (
                            <p className="mt-2 text-sm font-medium text-emerald-500 flex items-center gap-1.5">
                                <CheckCircle size={14} /> A new verification link has been sent.
                            </p>
                        )}
                    </div>
                )}

                <div className="flex items-center gap-4 pt-1">
                    <button
                        type="submit"
                        disabled={processing}
                        className="flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-500 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Save size={15} />
                        Save Changes
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
                            <CheckCircle size={14} /> Saved
                        </p>
                    </Transition>
                </div>
            </form>
        </section>
    );
}
