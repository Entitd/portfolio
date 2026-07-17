<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactRequest;
use App\Services\ContactService;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(
        StoreContactRequest $request,
        ContactService $contactService,
    ): JsonResponse {
        $contact = $contactService->create(
            data: $request->validated(),
            requestId: $request->attributes->get('request_id'),
        );

        return response()->json([
            'message' => 'Обращение успешно принято.',
            'data' => [
                'id' => $contact->id,
                'request_id' => $contact->request_id,
                'ai' => [
                    'answer' => $contact->ai_answer,
                    'category' => $contact->ai_category,
                    'sentiment' => $contact->ai_sentiment,
                    'status' => $contact->ai_status,
                ],
            ],
        ], 201);
    }
}
