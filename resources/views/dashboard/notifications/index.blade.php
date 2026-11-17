@extends('dashboard.layout.app')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <p class="text-xs font-mono uppercase tracking-[0.16em] text-gold-400">Account</p>
            <h1 class="text-xl sm:text-2xl font-bold text-white mt-1">Notifications</h1>
            <p class="text-sm text-gray-400 mt-0.5">Updates about your deposits, withdrawals, trades and account.</p>
        </div>
        <div class="grid grid-cols-2 sm:flex items-center gap-2 w-full sm:w-auto">
            <button type="button" id="markAllReadBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-gray-700 hover:bg-gray-600 border border-gray-600 text-white text-sm font-semibold">
                Mark All as Read
            </button>
            <button type="button" id="clearAllBtn" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-red-500/40 text-red-400 hover:bg-red-500/10 text-sm font-semibold">
                Clear All
            </button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <nav class="flex flex-wrap gap-2" aria-label="Filter notifications">
            <button type="button" id="allTab" class="tab-button active px-3 min-h-[40px] rounded-lg border bg-gray-800 text-sm font-semibold border-blue-500 text-blue-400">
                All
            </button>
            <button type="button" id="unreadTab" class="tab-button px-3 min-h-[40px] rounded-lg border bg-gray-800 text-sm font-semibold border-transparent text-gray-400 hover:text-gray-300">
                Unread
            </button>
            <button type="button" id="depositTab" class="tab-button px-3 min-h-[40px] rounded-lg border bg-gray-800 text-sm font-semibold border-transparent text-gray-400 hover:text-gray-300">
                Deposits
            </button>
            <button type="button" id="withdrawalTab" class="tab-button px-3 min-h-[40px] rounded-lg border bg-gray-800 text-sm font-semibold border-transparent text-gray-400 hover:text-gray-300">
                Withdrawals
            </button>
            <button type="button" id="tradingTab" class="tab-button px-3 min-h-[40px] rounded-lg border bg-gray-800 text-sm font-semibold border-transparent text-gray-400 hover:text-gray-300">
                Trading
            </button>
            <button type="button" id="systemTab" class="tab-button px-3 min-h-[40px] rounded-lg border bg-gray-800 text-sm font-semibold border-transparent text-gray-400 hover:text-gray-300">
                System
            </button>
    </nav>

    <!-- Notifications List -->
    <div class="space-y-3">
        @forelse($notifications as $notification)
        <div class="notification-item rounded-2xl border border-gray-700 bg-gray-800 p-4 sm:p-5 hover:border-gray-600 transition-colors {{ !$notification->read_at ? 'border-l-4 border-l-blue-500' : '' }}" 
             data-type="{{ $notification->type }}" 
             data-id="{{ $notification->id }}">
            
            <div class="flex flex-col gap-3">
                <!-- Top Section: Icon, Title and Message -->
                <div class="flex items-start gap-3 sm:gap-4">
                    <!-- Notification Icon -->
                    <div class="flex-shrink-0">
                        @php
                            $t = $notification->type;
                            $tone = match (true) {
                                in_array($t, ['deposit', 'deposit_submitted', 'deposit_approved', 'bot_created', 'bot_started', 'bot_resumed']) => 'bg-emerald-500/15 text-emerald-400',
                                in_array($t, ['withdrawal', 'withdrawal_submitted', 'withdrawal_approved', 'copy_trade_stopped', 'bot_stopped']) => 'bg-red-500/15 text-red-400',
                                in_array($t, ['trading', 'copy_trade', 'copy_trade_started']) => 'bg-blue-500/15 text-blue-300',
                                in_array($t, ['bot_trade', 'bot_trade_executed', 'bot_paused']) => 'bg-amber-500/15 text-amber-300',
                                default => 'bg-gray-700 text-gray-300',
                            };
                        @endphp
                        <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $tone }}">
                            @if($notification->type === 'deposit' || $notification->type === 'deposit_submitted' || $notification->type === 'deposit_approved')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            @elseif($notification->type === 'withdrawal' || $notification->type === 'withdrawal_submitted' || $notification->type === 'withdrawal_approved')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4m16 0l-4-4m4 4l-4 4"></path>
                                </svg>
                            @elseif($notification->type === 'trading')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            @elseif($notification->type === 'copy_trade' || $notification->type === 'copy_trade_started')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            @elseif($notification->type === 'copy_trade_stopped')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                                </svg>
                            @elseif($notification->type === 'bot_trade' || $notification->type === 'bot_trade_executed')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            @elseif($notification->type === 'bot_created')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            @elseif($notification->type === 'bot_started')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($notification->type === 'bot_paused')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($notification->type === 'bot_resumed')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @elseif($notification->type === 'bot_stopped')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 19h6v-6H4v6z"></path>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <!-- Notification Content -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm sm:text-base font-semibold text-white break-words">{{ $notification->title }}</h3>
                        <p class="text-sm text-gray-300 mt-1 break-words">{{ $notification->message }}</p>
                        
                        @if($notification->data)
                            <div class="mt-3 p-3 rounded-xl border border-gray-700 bg-gray-900/50 space-y-1">
                                @if(isset($notification->data['amount']))
                                    <div class="text-sm text-gray-300">
                                        <span class="font-medium">Amount:</span> {{ auth()->user()->formatAmount($notification->data['amount']) }}
                                    </div>
                                @endif
                                @if(isset($notification->data['status']))
                                    <div class="text-sm text-gray-300 mt-1">
                                        <span class="font-medium">Status:</span> 
                                        <span class="px-2 py-0.5 rounded-md text-xs font-semibold {{ $notification->data['status'] === 'completed' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-amber-500/15 text-amber-300' }}">
                                            {{ ucfirst($notification->data['status']) }}
                                        </span>
                                    </div>
                                @endif
                                @if(isset($notification->data['symbol']))
                                    <div class="text-sm text-gray-300 mt-1">
                                        <span class="font-medium">Symbol:</span> {{ $notification->data['symbol'] }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Bottom Section: Time, Status Badge, and Action Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-gray-700">
                    <!-- Time and Status -->
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                        @if(!$notification->read_at)
                            <span class="px-2 py-0.5 rounded-md text-xs font-semibold bg-blue-100 !bg-blue-500/15 text-blue-300">
                                New
                            </span>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex items-center gap-1">
                        @if(!$notification->read_at)
                            <button type="button" class="mark-read-btn px-3 min-h-[40px] rounded-lg text-sm font-semibold text-gold-400 hover:text-gold-300 hover:bg-gray-700/50" 
                                    data-id="{{ $notification->id }}">
                                Mark Read
                            </button>
                        @endif
                        <button type="button" class="delete-notification-btn px-3 min-h-[40px] rounded-lg text-sm font-semibold text-red-400 hover:text-red-300 hover:bg-red-500/10" 
                                data-id="{{ $notification->id }}">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="rounded-2xl border border-gray-700 bg-gray-800 px-4 py-12 text-center">
            <div class="mx-auto w-12 h-12 rounded-full bg-gray-700 grid place-items-center text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 01-3.4 0"/></svg>
            </div>
            <div class="mt-3 text-white font-medium">You're all caught up</div>
            <p class="mt-1 text-sm text-gray-400">New notifications about your account will appear here.</p>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="pt-2">
            {{ $notifications->links() }}
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabs = document.querySelectorAll('.tab-button');
    const notificationItems = document.querySelectorAll('.notification-item');
    
    // Get button references
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const clearAllBtn = document.getElementById('clearAllBtn');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            tabs.forEach(t => {
                t.classList.remove('active', 'border-blue-500', 'text-blue-400');
                t.classList.add('border-transparent', 'text-gray-400');
            });

            // Add active class to clicked tab
            this.classList.add('active', 'border-blue-500', 'text-blue-400');
            this.classList.remove('border-transparent', 'text-gray-400');

            // Filter notifications
            const filterType = this.id.replace('Tab', '');
            filterNotifications(filterType);
        });
    });

    function filterNotifications(type) {
        notificationItems.forEach(item => {
            if (type === 'all') {
                item.style.display = 'block';
            } else if (type === 'unread') {
                item.style.display = item.querySelector('.bg-blue-100') ? 'block' : 'none';
            } else {
                const itemType = item.getAttribute('data-type');
                item.style.display = itemType === type ? 'block' : 'none';
            }
        });
    }

    // Mark as read functionality
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-id');
            markAsRead(notificationId);
        });
    });

    // Delete notification functionality
    document.querySelectorAll('.delete-notification-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-id');
            deleteNotification(notificationId);
        });
    });

    // Mark all as read
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            markAllAsRead();
        });
    }

    // Clear all notifications
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            uiConfirm('Are you sure you want to clear all notifications? This action cannot be undone.').then(function (ok) {
                if (ok) clearAllNotifications();
            });
        });
    }

    function markAsRead(notificationId) {
        fetch(`/user/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                uiAlert('Failed to mark notification as read', { tone: 'danger', title: 'Something went wrong' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            uiAlert('An error occurred while marking notification as read', { tone: 'danger', title: 'Something went wrong' });
        });
    }

    function markAllAsRead() {
        console.log('markAllAsRead function called');
        const button = document.getElementById('markAllReadBtn');
        button.disabled = true;
        button.textContent = 'Processing...';
        
        console.log('Making fetch request to mark all as read');
        fetch('/user/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            console.log('Response received:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                console.log('Successfully marked all as read, reloading page');
                location.reload();
            } else {
                console.log('Failed to mark all as read:', data);
                uiAlert('Failed to mark all notifications as read', { tone: 'danger', title: 'Something went wrong' });
                button.disabled = false;
                button.textContent = 'Mark All as Read';
            }
        })
        .catch(error => {
            console.error('Error marking all as read:', error);
            uiAlert('An error occurred while marking notifications as read: ' + error.message, { tone: 'danger', title: 'Something went wrong' });
            button.disabled = false;
            button.textContent = 'Mark All as Read';
        });
    }

    function deleteNotification(notificationId) {
        fetch(`/user/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function clearAllNotifications() {
        console.log('clearAllNotifications function called');
        const button = document.getElementById('clearAllBtn');
        button.disabled = true;
        button.textContent = 'Processing...';
        
        console.log('Making fetch request to clear all notifications');
        fetch('/user/notifications/clear-all', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => {
            console.log('Response received:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                console.log('Successfully cleared all notifications, reloading page');
                location.reload();
            } else {
                console.log('Failed to clear notifications:', data);
                uiAlert('Failed to clear all notifications', { tone: 'danger', title: 'Something went wrong' });
                button.disabled = false;
                button.textContent = 'Clear All';
            }
        })
        .catch(error => {
            console.error('Error clearing notifications:', error);
            uiAlert('An error occurred while clearing notifications: ' + error.message, { tone: 'danger', title: 'Something went wrong' });
            button.disabled = false;
            button.textContent = 'Clear All';
        });
    }
});
</script>
@endsection
