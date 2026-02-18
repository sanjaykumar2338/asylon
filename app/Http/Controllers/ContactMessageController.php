<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageConfirmation;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $contactMessage = ContactMessage::create($request->validated());

        Mail::to($contactMessage->email)
            ->send(new ContactMessageConfirmation($contactMessage));

        return redirect()
            ->route('marketing.contact')
            ->with('success', 'Thanks! Your message has been sent. We will respond soon.');
    }
}
