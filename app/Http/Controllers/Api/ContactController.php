<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Contact\StoreContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;

class ContactController extends ApiController
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::create($request->validated());

        return $this->successResponse(new ContactResource($contact), 'Contact request submitted successfully.', 201);
    }
}
