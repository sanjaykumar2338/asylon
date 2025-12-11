<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        Log::info('Stripe webhook received', [
            'event_id' => $request->input('id'),
            'type' => $request->input('type'),
        ]);

        return response()->noContent();
    }
}
