<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminBroadcastMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MailController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Mail/Index', [
            'users' => User::query()
                ->where('account_tier', '!=', 'admin')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
            'defaults' => [
                'subject' => 'Important account update',
                'heading' => 'Account update',
                'header_label' => config('app.name'),
                'header_color' => '#09090b',
                'accent_color' => '#d97706',
                'footer_text' => 'You are receiving this email because you have an account with '.config('app.name').'.',
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_ids' => 'array',
            'user_ids.*' => 'integer|exists:users,id',
            'manual_emails' => 'nullable|string|max:5000',
            'subject' => 'required|string|max:150',
            'heading' => 'required|string|max:150',
            'message' => 'required|string|max:10000',
            'header_label' => 'nullable|string|max:80',
            'header_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'accent_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'footer_text' => 'required|string|max:500',
            'action_label' => 'nullable|string|max:60',
            'action_url' => 'nullable|url|max:500',
        ]);

        $recipients = $this->recipientsFrom($data);

        if ($recipients->isEmpty()) {
            throw ValidationException::withMessages([
                'recipients' => 'Select at least one user or enter at least one email address.',
            ]);
        }

        $recipients->each(function (array $recipient) use ($data) {
            Mail::to($recipient['email'], $recipient['name'])->send(new AdminBroadcastMail(
                subjectLine: $data['subject'],
                heading: $data['heading'],
                messageBody: $data['message'],
                headerColor: $data['header_color'],
                accentColor: $data['accent_color'],
                footerText: $data['footer_text'],
                headerLabel: $data['header_label'] ?? null,
                actionLabel: $data['action_label'] ?? null,
                actionUrl: $data['action_url'] ?? null,
            ));
        });

        return redirect()->route('admin.mail.index')
            ->with('success', 'Mail sent to '.$recipients->count().' recipient'.($recipients->count() === 1 ? '.' : 's.'));
    }

    private function recipientsFrom(array $data): Collection
    {
        $selectedUsers = User::query()
            ->whereIn('id', $data['user_ids'] ?? [])
            ->get(['name', 'email'])
            ->map(fn (User $user) => [
                'name' => $user->name,
                'email' => $user->email,
            ]);

        $manualRecipients = collect(preg_split('/[\s,;]+/', $data['manual_emails'] ?? '', -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn (string $email) => trim($email))
            ->filter();

        $invalidEmails = $manualRecipients
            ->reject(fn (string $email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->values();

        if ($invalidEmails->isNotEmpty()) {
            throw ValidationException::withMessages([
                'manual_emails' => 'Invalid email address: '.$invalidEmails->first(),
            ]);
        }

        return $selectedUsers
            ->concat($manualRecipients->map(fn (string $email) => [
                'name' => null,
                'email' => $email,
            ]))
            ->unique(fn (array $recipient) => strtolower($recipient['email']))
            ->values();
    }
}
