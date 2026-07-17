<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Mail\ContactOwnerNotification;
use App\Mail\ContactUserNotification;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create([
            ...$request->validated(),
            'request_id' => (string) Str::uuid(),
        ]);

        Mail::to(config('contact.owner_email'))
            ->send(new ContactOwnerNotification($contact));

        Mail::to($contact->email)
            ->send(new ContactUserNotification($contact));

        return response()->json([
            'message' => 'Successfully created contact!',
            'data' => [
                'id' => $contact->id,
                'request_id' => $contact->request_id,
            ],
        ], 201);
    }
}
