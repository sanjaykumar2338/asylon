<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemoFormRequest;
use App\Models\DemoRequest;
use Illuminate\Http\RedirectResponse;

class DemoRequestController extends Controller
{
    public function store(StoreDemoFormRequest $request): RedirectResponse
    {
        DemoRequest::create($request->validated());

        return redirect()
            ->route('marketing.demo')
            ->with('success', 'Thanks! Your demo request has been received. We will contact you soon.');
    }
}
