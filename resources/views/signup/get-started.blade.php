<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('Get Started with Asylon') }}</h1>
        <p class="text-gray-600">{{ __('Create your organization and admin account to begin.') }}</p>
    </div>

    <form method="POST" action="{{ route('signup.store') }}" class="space-y-4">
        @csrf
        <div>
            <x-input-label for="org_name" :value="__('Organization Name')" />
            <x-text-input id="org_name" class="block mt-1 w-full" type="text" name="org_name" :value="old('org_name')" required autofocus />
            <x-input-error :messages="$errors->get('org_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="org_type" :value="__('Organization Type')" />
            <select id="org_type" name="org_type" class="block mt-1 w-full border-gray-300 rounded-md" required>
                @foreach (['school' => __('School'), 'church' => __('Church'), 'organization' => __('Organization'), 'other' => __('Other')] as $value => $label)
                    <option value="{{ $value }}" @selected(old('org_type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('org_type')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="name" :value="__('Admin Full Name')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="email" :value="__('Admin Work Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
            </div>
        </div>

        <div>
            <x-input-label for="plan_id" :value="__('Plan (optional)')" />
            <select id="plan_id" name="plan_id" class="block mt-1 w-full border-gray-300 rounded-md">
                <option value="">{{ __('Starter (default)') }}</option>
                @foreach ($plans as $plan)
                    <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>{{ $plan->name }} ({{ $plan->trial_days }} {{ __('day trial') }})</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
