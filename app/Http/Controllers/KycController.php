<?php

namespace App\Http\Controllers;

use App\Models\KycSubmission;
use App\Services\UserNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KycController extends Controller
{
    public function __construct(private UserNotificationService $notifications) {}

    public function index(Request $request)
    {
        $submission = $request->user()
            ->kycSubmissions()
            ->latest()
            ->first();

        return Inertia::render('Kyc', [
            'submission' => $submission ? [
                'id' => $submission->id,
                'status' => $submission->status,
                'id_document_type' => $submission->id_document_type,
                'rejection_reason' => $submission->rejection_reason,
                'submitted_at' => $submission->created_at?->format('Y-m-d H:i'),
            ] : null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_document_type' => ['required', 'string', 'max:32'],
            'id_document' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
            'selfie' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
            'address_proof' => ['nullable', 'file', 'mimes:png,jpg,jpeg,pdf', 'max:5120'],
        ]);

        $user = $request->user();
        $folder = 'kyc/' . $user->id;

        $submission = KycSubmission::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'id_document_type' => $data['id_document_type'],
            'id_document_path' => $request->file('id_document')?->store($folder, 'public'),
            'selfie_path' => $request->file('selfie')?->store($folder, 'public'),
            'address_proof_path' => $request->file('address_proof')?->store($folder, 'public'),
        ]);

        $user->update(['kyc_level' => 'pending']);
        $this->notifications->sendKycSubmitted($user, $submission);

        return redirect()->route('kyc')->with('success', 'KYC submission sent for review.');
    }
}
