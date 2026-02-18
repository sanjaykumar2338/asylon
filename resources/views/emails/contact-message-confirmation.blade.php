@extends('emails.layouts.brand')

@section('content')
    <h1>{{ __('Thank you for reaching out, :firstName!', ['firstName' => $contactMessage->first_name]) }}</h1>

    <p>
        {{ __('We received your message and one of our team members will reply within 1 business day. In the meantime, feel free to reply to this email if you have more information to share.') }}
    </p>

    <div class="panel">
        <p><strong>{{ __('First name:') }}</strong> {{ $contactMessage->first_name }}</p>
        <p><strong>{{ __('Last name:') }}</strong> {{ $contactMessage->last_name }}</p>
        <p><strong>{{ __('Email:') }}</strong> {{ $contactMessage->email }}</p>
        <p><strong>{{ __('Message:') }}</strong></p>
        <p>{{ $contactMessage->message }}</p>
    </div>

    <p class="meta">{{ __('Thanks again for getting in touch, and talk soon!') }}</p>
@endsection
