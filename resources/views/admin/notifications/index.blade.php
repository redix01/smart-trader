@extends('admin.layouts.app')

@php
    $selectedUsersCount = $users->count();
    $mailCount = $mailHistory->total();
@endphp

@section('content')
<div class="p-6 space-y-8">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-sky-500">Admin Mail</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">Send branded emails from the admin panel</h1>
            <p class="mt-2 max-w-3xl text-sm text-gray-600 dark:text-gray-400">
                Choose recipients from users or enter direct email addresses, set the sender identity, tweak the header color, and send a polished email template with your platform branding.
            </p>
        </div>
        <div class="rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700 dark:border-sky-900/60 dark:bg-sky-950/40 dark:text-sky-200">
            Current mailer: <span class="font-semibold">{{ config('mail.default') }}</span>
        </div>
    </div>

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/40 dark:text-rose-200">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-900/60 dark:bg-amber-950/40 dark:text-amber-200">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Reachable Users</p>
            <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $selectedUsersCount }}</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Users with email addresses available for direct outreach.</p>
        </div>
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Emails Logged</p>
            <p class="mt-3 text-3xl font-semibold text-gray-900 dark:text-white">{{ $mailCount }}</p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Stored admin mail sends from this panel.</p>
        </div>
        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Default Senders</p>
            <div class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-300">
                <div class="rounded-xl bg-gray-50 px-3 py-2 dark:bg-gray-700/70">{{ $defaultSenderOptions['admin'] }}</div>
                <div class="rounded-xl bg-gray-50 px-3 py-2 dark:bg-gray-700/70">{{ $defaultSenderOptions['support'] }}</div>
            </div>
        </div>
    </div>

    <div class="grid gap-8 xl:grid-cols-[1.2fr_0.8fr]">
        <div class="rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Compose Mail</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Use one of the sender identities below and send to users or external addresses.</p>
            </div>

            <form action="{{ route('admin.notifications.send') }}" method="POST" class="space-y-8 p-6" id="admin-mail-form">
                @csrf

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-3">
                        <label for="from_identity" class="text-sm font-medium text-gray-700 dark:text-gray-300">Send From</label>
                        <select
                            id="from_identity"
                            name="from_identity"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="admin" @selected(old('from_identity') === 'admin')>Admin ({{ $defaultSenderOptions['admin'] }})</option>
                            <option value="support" @selected(old('from_identity') === 'support')>Support ({{ $defaultSenderOptions['support'] }})</option>
                            <option value="custom" @selected(old('from_identity') === 'custom')>Other email address</option>
                        </select>
                    </div>

                    <div id="custom-from-wrapper" class="space-y-3 {{ old('from_identity') === 'custom' ? '' : 'hidden' }}">
                        <label for="custom_from_email" class="text-sm font-medium text-gray-700 dark:text-gray-300">Custom Sender Email</label>
                        <input
                            type="email"
                            id="custom_from_email"
                            name="custom_from_email"
                            value="{{ old('custom_from_email') }}"
                            placeholder="team@example.com"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Recipients</label>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Choose user records, paste direct emails, or broadcast to all users with email addresses.</p>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <label class="cursor-pointer rounded-2xl border border-gray-200 p-4 transition hover:border-sky-400 dark:border-gray-700">
                            <input type="radio" name="recipient_source" value="users" class="mail-recipient-source sr-only" @checked(old('recipient_source', 'users') === 'users')>
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Select users</span>
                            <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">Pick one or more registered users.</span>
                        </label>
                        <label class="cursor-pointer rounded-2xl border border-gray-200 p-4 transition hover:border-sky-400 dark:border-gray-700">
                            <input type="radio" name="recipient_source" value="manual" class="mail-recipient-source sr-only" @checked(old('recipient_source') === 'manual')>
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">Enter emails</span>
                            <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">Paste comma, space, or line-separated emails.</span>
                        </label>
                        <label class="cursor-pointer rounded-2xl border border-gray-200 p-4 transition hover:border-sky-400 dark:border-gray-700">
                            <input type="radio" name="recipient_source" value="all_users" class="mail-recipient-source sr-only" @checked(old('recipient_source') === 'all_users')>
                            <span class="block text-sm font-semibold text-gray-900 dark:text-white">All users</span>
                            <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">Send to every user with a valid email.</span>
                        </label>
                    </div>

                    <div id="users-panel" class="space-y-3">
                        <label for="user_ids" class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose Users</label>
                        <select
                            id="user_ids"
                            name="user_ids[]"
                            multiple
                            size="8"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(collect(old('user_ids', []))->contains($user->id))>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Hold Cmd or Ctrl to select multiple users.</p>
                    </div>

                    <div id="manual-panel" class="space-y-3 {{ old('recipient_source') === 'manual' ? '' : 'hidden' }}">
                        <label for="manual_emails" class="text-sm font-medium text-gray-700 dark:text-gray-300">Manual Emails</label>
                        <textarea
                            id="manual_emails"
                            name="manual_emails"
                            rows="5"
                            placeholder="first@example.com, second@example.com"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >{{ old('manual_emails') }}</textarea>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-3 md:col-span-2">
                        <label for="subject" class="text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                        <input
                            type="text"
                            id="subject"
                            name="subject"
                            value="{{ old('subject') }}"
                            maxlength="255"
                            placeholder="Enter email subject"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div class="space-y-3">
                        <label for="header_color" class="text-sm font-medium text-gray-700 dark:text-gray-300">Header Color</label>
                        <div class="flex items-center gap-3 rounded-2xl border border-gray-300 bg-white px-4 py-2 dark:border-gray-600 dark:bg-gray-700">
                            <input type="color" id="header_color_picker" value="{{ old('header_color', $defaultHeaderColor) }}" class="h-10 w-14 cursor-pointer rounded-lg border-0 bg-transparent p-0">
                            <input
                                type="text"
                                id="header_color"
                                name="header_color"
                                value="{{ old('header_color', $defaultHeaderColor) }}"
                                class="w-full border-0 bg-transparent px-0 py-0 text-sm text-gray-900 focus:outline-none focus:ring-0 dark:text-white"
                            >
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label for="accent_label" class="text-sm font-medium text-gray-700 dark:text-gray-300">Header Tag</label>
                        <input
                            type="text"
                            id="accent_label"
                            name="accent_label"
                            value="{{ old('accent_label', 'Admin Update') }}"
                            maxlength="40"
                            placeholder="Admin Update"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <div class="space-y-3 md:col-span-2">
                        <label for="message" class="text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                        <textarea
                            id="message"
                            name="message"
                            rows="8"
                            maxlength="5000"
                            placeholder="Write the main email message here..."
                            class="w-full rounded-3xl border border-gray-300 bg-white px-4 py-4 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >{{ old('message') }}</textarea>
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span>Plain text is converted into styled paragraphs in the email body.</span>
                            <span><span id="message-count">0</span>/5000</span>
                        </div>
                    </div>

                    <div class="space-y-3 md:col-span-2">
                        <label for="footer_note" class="text-sm font-medium text-gray-700 dark:text-gray-300">Footer Note</label>
                        <input
                            type="text"
                            id="footer_note"
                            name="footer_note"
                            value="{{ old('footer_note', 'This mailbox is monitored by the support team for urgent replies.') }}"
                            maxlength="255"
                            placeholder="Optional support or reply note"
                            class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-gray-200 pt-6 dark:border-gray-700 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Each recipient is sent individually to avoid exposing other email addresses.</p>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-sky-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-700">
                        Send Mail
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-6">
            <div class="overflow-hidden rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div id="preview-header" class="px-6 py-6" style="background: linear-gradient(135deg, {{ old('header_color', $defaultHeaderColor) }} 0%, #0f172a 160%);">
                    <div class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-white" id="preview-accent">
                        {{ old('accent_label', 'Admin Update') }}
                    </div>
                    <div class="mt-4 flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/10 p-2 backdrop-blur">
                            @if(\App\Helpers\WebsiteSettingsHelper::hasImageLogo())
                                <img src="{{ \App\Helpers\WebsiteSettingsHelper::getLogoUrl() }}" alt="{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }}" class="max-h-10 w-auto">
                            @elseif(\App\Helpers\WebsiteSettingsHelper::hasTextLogo())
                                <span class="text-lg font-bold text-white">{{ \App\Helpers\WebsiteSettingsHelper::getTextLogo() }}</span>
                            @else
                                <span class="text-lg font-bold text-white">{{ strtoupper(substr(\App\Helpers\WebsiteSettingsHelper::getSiteName(), 0, 1)) }}</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-white">{{ \App\Helpers\WebsiteSettingsHelper::getSiteName() }}</p>
                            <p class="text-sm text-white/80">{{ \App\Helpers\WebsiteSettingsHelper::getSiteTagline() ?: 'Official platform communication' }}</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-5 p-6">
                    <div class="rounded-2xl bg-gray-50 p-4 dark:bg-gray-700/60">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Subject</p>
                        <p class="mt-2 text-lg font-semibold text-gray-900 dark:text-white" id="preview-subject">{{ old('subject', 'Your subject will appear here') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Message Preview</p>
                        <div id="preview-message" class="mt-3 space-y-3 text-sm leading-7 text-gray-600 dark:text-gray-300">
                            <p>{{ old('message', 'Write your email content on the left and the preview updates here.') }}</p>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-dashed border-gray-300 p-4 dark:border-gray-600">
                        <p class="text-xs uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">Footer Note</p>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300" id="preview-footer">{{ old('footer_note', 'This mailbox is monitored by the support team for urgent replies.') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-[28px] border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Recent Mail Sends</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest messages sent from the admin panel.</p>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($mailHistory as $log)
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $log->subject }}</p>
                                    <p class="mt-1 text-xs uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">{{ $log->sender_email }}</p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold" style="background-color: {{ $log->header_color }}20; color: {{ $log->header_color }};">
                                    {{ $log->recipient_count }} recipients
                                </span>
                            </div>
                            <p class="mt-3 line-clamp-3 text-sm text-gray-600 dark:text-gray-300">{{ $log->message }}</p>
                            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ ucfirst(str_replace('_', ' ', $log->recipient_source)) }}</span>
                                <span>{{ optional($log->sent_at)->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-500 dark:text-gray-400">
                            No admin emails have been logged yet.
                        </div>
                    @endforelse
                </div>
                <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">
                    {{ $mailHistory->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fromIdentity = document.getElementById('from_identity');
    const customFromWrapper = document.getElementById('custom-from-wrapper');
    const recipientInputs = Array.from(document.querySelectorAll('.mail-recipient-source'));
    const usersPanel = document.getElementById('users-panel');
    const manualPanel = document.getElementById('manual-panel');
    const subject = document.getElementById('subject');
    const message = document.getElementById('message');
    const headerColor = document.getElementById('header_color');
    const headerColorPicker = document.getElementById('header_color_picker');
    const accentLabel = document.getElementById('accent_label');
    const footerNote = document.getElementById('footer_note');
    const previewHeader = document.getElementById('preview-header');
    const previewSubject = document.getElementById('preview-subject');
    const previewMessage = document.getElementById('preview-message');
    const previewAccent = document.getElementById('preview-accent');
    const previewFooter = document.getElementById('preview-footer');
    const messageCount = document.getElementById('message-count');

    function syncSenderVisibility() {
        customFromWrapper.classList.toggle('hidden', fromIdentity.value !== 'custom');
    }

    function syncRecipientPanels() {
        const selected = recipientInputs.find((input) => input.checked)?.value;
        usersPanel.classList.toggle('hidden', selected !== 'users');
        manualPanel.classList.toggle('hidden', selected !== 'manual');

        recipientInputs.forEach((input) => {
            const card = input.closest('label');
            card.classList.toggle('border-sky-500', input.checked);
            card.classList.toggle('bg-sky-50', input.checked);
            card.classList.toggle('dark:bg-sky-950/20', input.checked);
        });
    }

    function renderPreview() {
        const color = /^#[0-9A-Fa-f]{6}$/.test(headerColor.value) ? headerColor.value : '#3B82F6';
        previewHeader.style.background = `linear-gradient(135deg, ${color} 0%, #0f172a 160%)`;
        previewSubject.textContent = subject.value.trim() || 'Your subject will appear here';
        previewAccent.textContent = accentLabel.value.trim() || 'Admin Update';
        previewFooter.textContent = footerNote.value.trim() || 'No footer note provided.';
        messageCount.textContent = message.value.length;

        const paragraphs = message.value.trim()
            ? message.value.trim().split(/\n{2,}/).map((paragraph) => `<p>${paragraph.replace(/\n/g, '<br>')}</p>`).join('')
            : '<p>Write your email content on the left and the preview updates here.</p>';

        previewMessage.innerHTML = paragraphs;
    }

    fromIdentity.addEventListener('change', syncSenderVisibility);
    recipientInputs.forEach((input) => input.addEventListener('change', syncRecipientPanels));
    [subject, message, accentLabel, footerNote, headerColor].forEach((input) => input.addEventListener('input', renderPreview));

    headerColorPicker.addEventListener('input', function () {
        headerColor.value = this.value;
        renderPreview();
    });

    headerColor.addEventListener('input', function () {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            headerColorPicker.value = this.value;
        }
    });

    syncSenderVisibility();
    syncRecipientPanels();
    renderPreview();
});
</script>
@endsection
