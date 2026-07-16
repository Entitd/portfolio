<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create([
            ...$request->validated(),
            'request_id' => (string) Str::uuid(),
        ]);

        return response()->json([
            'message' => 'Successfully created contact!',
            'data' => [
                'id' => $contact->id,
                'request_id' => $contact->request_id,
            ],
        ], 201);
    }
}
