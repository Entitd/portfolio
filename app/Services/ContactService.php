<?php

namespace App\Services;

use App\Contracts\AiAnalyzerInterface;
use App\Mail\ContactOwnerNotification;
use App\Mail\ContactUserNotification;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ContactService
{
    public function __construct(
        private readonly AiAnalyzerInterface $aiAnalyzer,
    ) {}

    /**
     * @param  array{name: string, email: string, phone: string, comment: string}  $data
     */
    public function create(array $data, ?string $requestId = null): Contact
    {
        $contact = Contact::create([
            ...$data,
            'request_id' => $requestId ?? (string) Str::uuid(),
        ]);

        $analysis = $this->aiAnalyzer->analyze($contact->comment);

        $contact->update([
            'ai_answer' => $analysis->answer,
            'ai_category' => $analysis->category,
            'ai_sentiment' => $analysis->sentiment,
            'ai_status' => $analysis->status,
            'ai_processed_at' => now(),
        ]);

        Mail::to(config('contact.owner_email'))
            ->send(new ContactOwnerNotification($contact));

        Mail::to($contact->email)
            ->send(new ContactUserNotification($contact));

        return $contact;
    }
}
