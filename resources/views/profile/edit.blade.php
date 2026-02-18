<x-admin-layout>
    <x-slot name="header">
        {{ __('Profile Settings') }}
    </x-slot>

    @include('admin.partials.flash')

    <div class="container-fluid">
        <div class="row g-3">
            <div class="col-xl-6">
                @include('profile.partials.update-profile-information-form')
            </div>
            <div class="col-xl-6">
                @include('profile.partials.update-password-form')
            </div>
            <div class="col-xl-6">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-admin-layout>
