import InputError from '@/Components/InputError';
import Modal from '@/Components/Modal';
import { useForm } from '@inertiajs/react';
import { FormEventHandler, useRef, useState } from 'react';
import { Trash2, AlertTriangle } from 'lucide-react';

export default function DeleteUserForm({ className = '' }: { className?: string }) {
    const [confirmingUserDeletion, setConfirmingUserDeletion] = useState(false);
    const passwordInput = useRef<HTMLInputElement>(null);

    const { data, setData, delete: destroy, processing, reset, errors, clearErrors } = useForm({
        password: '',
    });

    const confirmUserDeletion = () => setConfirmingUserDeletion(true);

    const deleteUser: FormEventHandler = (e) => {
        e.preventDefault();
        destroy(route('profile.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput.current?.focus(),
            onFinish: () => reset(),
        });
    };

    const closeModal = () => {
        setConfirmingUserDeletion(false);
        clearErrors();
        reset();
    };

    return (
        <section className={className}>
            <div className="mb-6">
                <h2 className="text-lg font-bold text-white">Danger Zone</h2>
                <p className="mt-1 text-sm text-zinc-500">
                    Once deleted, all account data is permanently removed and cannot be recovered.
                </p>
            </div>

            <div className="bg-rose-500/5 border border-rose-500/20 rounded-2xl p-4 mb-5">
                <div className="flex items-start gap-3">
                    <AlertTriangle size={16} className="text-rose-500 flex-shrink-0 mt-0.5" />
                    <p className="text-xs text-rose-300/70 leading-relaxed">
                        This action is irreversible. Download any data you wish to keep before proceeding.
                    </p>
                </div>
            </div>

            <button
                onClick={confirmUserDeletion}
                className="flex items-center gap-2 px-6 py-2.5 bg-rose-500/10 text-rose-500 border border-rose-500/20 text-sm font-bold rounded-xl hover:bg-rose-500 hover:text-white hover:border-rose-500 active:scale-[0.98] transition-all"
            >
                <Trash2 size={15} />
                Delete Account
            </button>

            <Modal show={confirmingUserDeletion} onClose={closeModal}>
                <form onSubmit={deleteUser} className="p-6 bg-[#111]">
                    <div className="flex items-center gap-3 mb-4">
                        <div className="w-10 h-10 bg-rose-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <Trash2 size={18} className="text-rose-500" />
                        </div>
                        <h2 className="text-lg font-bold text-white">Delete Account?</h2>
                    </div>

                    <p className="text-sm text-zinc-400 mb-6 leading-relaxed">
                        All your data — wallets, transactions, and settings — will be permanently erased.
                        Enter your password to confirm.
                    </p>

                    <div className="space-y-1.5 mb-6">
                        <label htmlFor="delete_password" className="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                            Confirm Password
                        </label>
                        <input
                            id="delete_password"
                            type="password"
                            ref={passwordInput}
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            placeholder="Enter your password"
                            className="w-full bg-[#0A0A0A] border border-[#1A1A1A] rounded-xl px-4 py-3 text-sm text-white placeholder-zinc-700 focus:border-rose-600 outline-none transition-colors"
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <button
                            type="button"
                            onClick={closeModal}
                            className="px-5 py-2.5 text-sm font-bold text-zinc-400 hover:text-white bg-[#1A1A1A] border border-[#222] rounded-xl transition-all"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={processing}
                            className="flex items-center gap-2 px-5 py-2.5 text-sm font-bold bg-rose-600 text-white rounded-xl hover:bg-rose-500 active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <Trash2 size={14} />
                            Delete Permanently
                        </button>
                    </div>
                </form>
            </Modal>
        </section>
    );
}
