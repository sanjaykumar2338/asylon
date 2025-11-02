<x-admin-layout>
    <x-slot name="header">
        {{ __('Profile Settings') }}
    </x-slot>

    <div class="row">
        <div class="col-xl-6">
            @include('profile.partials.update-profile-information-form')
        </div>
        <div class="col-xl-6">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</x-admin-layout>
