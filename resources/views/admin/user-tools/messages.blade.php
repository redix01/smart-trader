@extends('admin.layouts.app')

@php
    $preselected = collect(old('user_ids', $selectedUser ? [$selectedUser] : []))->map(fn ($v) => (string) $v)->all();
    $field = 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white';
@endphp

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">User Messages</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Send an in-app message. It appears in the user's notification bell and at the top of their dashboard until they read it.</p>
    </div>

    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
            <ul class="list-disc list-inside">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">
        <form method="POST" action="{{ route('admin.user-messages.send') }}" class="xl:col-span-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6 space-y-5" id="messageForm">
            @csrf

            <fieldset>
                <legend class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Send to</legend>
                <div class="flex flex-wrap gap-4 text-sm text-gray-700 dark:text-gray-300">
                    <label class="inline-flex items-center gap-2"><input type="radio" name="audience" value="selected" {{ old('audience', 'selected') === 'selected' ? 'checked' : '' }}> Selected users</label>
                    <label class="inline-flex items-center gap-2"><input type="radio" name="audience" value="all" {{ old('audience') === 'all' ? 'checked' : '' }}> All users ({{ $users->count() }})</label>
                </div>
            </fieldset>

            <div id="userPicker">
                <div class="flex items-center justify-between mb-2">
                    <label for="userSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Users <span id="pickedCount" class="text-gray-500"></span></label>
                </div>
                <input type="search" id="userSearch" placeholder="Search name or email" class="{{ $field }} mb-2">
                <div class="max-h-64 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $u)
                        <label class="flex items-center gap-3 px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50" data-search="{{ strtolower($u->name.' '.$u->email) }}">
                            <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" {{ in_array((string) $u->id, $preselected, true) ? 'checked' : '' }}>
                            <span class="min-w-0">
                                <span class="block font-medium text-gray-900 dark:text-white truncate">{{ $u->name }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400 truncate">{{ $u->email }}</span>
                            </span>
                        </label>
                    @empty
                        <p class="px-3 py-4 text-sm text-gray-500">No users yet.</p>
                    @endforelse
                </div>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                <input type="text" id="title" name="title" maxlength="120" required value="{{ old('title') }}" class="{{ $field }}" placeholder="e.g. Your account review is complete">
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                <textarea id="message" name="message" rows="5" maxlength="2000" required class="{{ $field }}" placeholder="Write the message the user will see">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg">Send message</button>
        </form>

        <div class="xl:col-span-2 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Recently sent</h2>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($sent as $n)
                    <li class="py-3">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $n->title }}</p>
                            <span class="shrink-0 text-xs px-2 py-0.5 rounded-full {{ $n->read_at ? 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">{{ $n->read_at ? 'Read' : 'Unread' }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $n->message }}</p>
                        <p class="text-xs text-gray-500 mt-1">To {{ $n->user->name ?? 'deleted user' }} · {{ $n->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="py-6 text-sm text-gray-500 dark:text-gray-400">No messages sent yet.</li>
                @endforelse
            </ul>
            <div class="mt-4">{{ $sent->links() }}</div>
        </div>
    </div>
</div>

<script>
(function () {
    const form = document.getElementById('messageForm');
    const picker = document.getElementById('userPicker');
    const count = document.getElementById('pickedCount');
    const sync = () => {
        const all = form.querySelector('input[name="audience"]:checked').value === 'all';
        picker.classList.toggle('opacity-40', all);
        picker.querySelectorAll('input').forEach(i => i.disabled = all);
        const n = picker.querySelectorAll('input[type=checkbox]:checked').length;
        count.textContent = n ? '(' + n + ' selected)' : '';
    };
    form.addEventListener('change', sync);
    document.getElementById('userSearch').addEventListener('input', e => {
        const q = e.target.value.trim().toLowerCase();
        picker.querySelectorAll('[data-search]').forEach(row => row.classList.toggle('hidden', q && !row.dataset.search.includes(q)));
    });
    sync();
})();
</script>
@endsection
