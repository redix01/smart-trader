<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\WebsiteSettingsHelper;
use App\Http\Controllers\Controller;
use App\Mail\AdminBroadcastMail;
use App\Models\AdminMailLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->select('id', 'name', 'email', 'status')
            ->whereNotNull('email')
            ->orderBy('name')
            ->get();

        $mailHistory = AdminMailLog::query()
            ->latest('sent_at')
            ->latest()
            ->paginate(10);

        return view('admin.notifications.index', [
            'users' => $users,
            'mailHistory' => $mailHistory,
            'defaultHeaderColor' => WebsiteSettingsHelper::getPrimaryColor(),
            'defaultSenderOptions' => $this->getSenderOptions(),
        ]);
    }

    public function sendNotification(Request $request)
    {
        $validated = $request->validate([
            'recipient_source' => 'required|in:users,manual,all_users',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
            'manual_emails' => 'nullable|string',
            'from_identity' => 'required|in:admin,support,custom',
            'custom_from_email' => 'nullable|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'header_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_label' => 'nullable|string|max:40',
            'footer_note' => 'nullable|string|max:255',
        ]);

        $emails = $this->resolveRecipients(
            $validated['recipient_source'],
            $validated['user_ids'] ?? [],
            $validated['manual_emails'] ?? ''
        );

        if (empty($emails)) {
            return back()
                ->withInput()
                ->with('error', 'Please provide at least one valid recipient email.');
        }

        $fromEmail = $this->resolveFromEmail(
            $validated['from_identity'],
            $validated['custom_from_email'] ?? null
        );

        if (!$fromEmail) {
            return back()
                ->withInput()
                ->with('error', 'A valid sender email is required.');
        }

        $siteName = WebsiteSettingsHelper::getSiteName();
        $fromName = $siteName . ' ' . ucfirst($validated['from_identity']);
        $sentCount = 0;

        try {
            foreach ($emails as $email) {
                Mail::to($email)->send(
                    new AdminBroadcastMail([
                        'subject' => $validated['subject'],
                        'message' => $validated['message'],
                        'header_color' => $validated['header_color'],
                        'accent_label' => $validated['accent_label'] ?? null,
                        'footer_note' => $validated['footer_note'] ?? null,
                        'recipient_email' => $email,
                        'from_email' => $fromEmail,
                        'from_name' => $fromName,
                    ])
                );

                $sentCount++;
            }

            AdminMailLog::create([
                'admin_user_id' => auth()->id(),
                'sender_email' => $fromEmail,
                'sender_name' => $fromName,
                'recipient_source' => $validated['recipient_source'],
                'recipients' => $emails,
                'recipient_count' => count($emails),
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'header_color' => $validated['header_color'],
                'accent_label' => $validated['accent_label'] ?? null,
                'footer_note' => $validated['footer_note'] ?? null,
                'sent_at' => now(),
            ]);

            Log::info('Admin mail broadcast sent', [
                'admin_id' => auth()->id(),
                'sender_email' => $fromEmail,
                'recipient_count' => count($emails),
                'recipient_source' => $validated['recipient_source'],
                'subject' => $validated['subject'],
            ]);

            return back()->with('success', "Mail sent successfully to {$sentCount} recipient(s) from {$fromEmail}.");
        } catch (\Throwable $e) {
            Log::error('Failed to send admin mail broadcast', [
                'admin_id' => auth()->id(),
                'sender_email' => $fromEmail,
                'recipient_count' => count($emails),
                'error' => $e->getMessage(),
            ]);

            $errorMsg = 'Mail sending failed. ';
            $mailer = config('mail.default');
            if ($mailer === 'log') {
                $errorMsg .= 'Your mail driver is set to "log" — emails are written to logs but not delivered. Set MAIL_MAILER=sendmail or MAIL_MAILER=smtp in your .env file.';
            } elseif ($mailer === 'sendmail') {
                $errorMsg .= 'Sendmail failed — verify that sendmail/postfix is running on the server. Error: ' . $e->getMessage();
            } else {
                $errorMsg .= 'Check your mail configuration. Error: ' . $e->getMessage();
            }

            return back()
                ->withInput()
                ->with('error', $errorMsg);
        }
    }

    public function getUserDetails(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->string('user_id'));

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'status' => $user->status,
            'created_at' => optional($user->created_at)->format('M d, Y'),
        ]);
    }

    public function getStats()
    {
        return response()->json([
            'total_users' => User::where('role', 'user')->count(),
            'mail_enabled_users' => User::where('role', 'user')->whereNotNull('email')->count(),
            'mail_count' => AdminMailLog::count(),
        ]);
    }

    public function getHistory()
    {
        return response()->json(
            AdminMailLog::query()->latest('sent_at')->latest()->paginate(20)
        );
    }

    public function show(string $id)
    {
        return response()->json(AdminMailLog::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $mailLog = AdminMailLog::findOrFail($id);

        $mailLog->update($request->validate([
            'accent_label' => 'nullable|string|max:40',
            'footer_note' => 'nullable|string|max:255',
            'header_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]));

        return response()->json(['success' => true]);
    }

    public function destroy(string $id)
    {
        AdminMailLog::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    protected function resolveRecipients(string $source, array $userIds, string $manualEmails): array
    {
        if ($source === 'all_users') {
            return User::where('role', 'user')
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->map(fn ($email) => Str::lower(trim($email)))
                ->unique()
                ->values()
                ->all();
        }

        if ($source === 'users') {
            return User::whereIn('id', $userIds)
                ->whereNotNull('email')
                ->pluck('email')
                ->filter()
                ->map(fn ($email) => Str::lower(trim($email)))
                ->unique()
                ->values()
                ->all();
        }

        $emails = preg_split('/[\s,;]+/', $manualEmails, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return collect($emails)
            ->map(fn ($email) => Str::lower(trim($email)))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    protected function resolveFromEmail(string $identity, ?string $customEmail): ?string
    {
        if ($identity === 'custom') {
            return $customEmail && filter_var($customEmail, FILTER_VALIDATE_EMAIL)
                ? Str::lower(trim($customEmail))
                : null;
        }

        $options = $this->getSenderOptions();

        return $options[$identity] ?? null;
    }

    protected function getSenderOptions(): array
    {
        $defaultFrom = config('mail.from.address', 'support@fortismarketpro.com');
        $defaultDomain = Str::after($defaultFrom, '@');

        if (!$defaultDomain || $defaultDomain === $defaultFrom) {
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            $defaultDomain = $appHost ?: 'fortismarketpro.com';
        }

        return [
            'admin' => 'admin@' . $defaultDomain,
            'support' => 'support@' . $defaultDomain,
        ];
    }
}
