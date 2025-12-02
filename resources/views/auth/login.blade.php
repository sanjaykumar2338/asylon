<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="mt-1 flex items-center gap-2 max-w-md">
                <x-text-input id="password" class="block w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                <button type="button"
                        id="password-toggle"
                        class="h-10 w-10 flex items-center justify-center border border-gray-300 rounded-md text-gray-500 hover:text-gray-700 focus:outline-none"
                        aria-label="{{ __('Show password') }}">
                    <svg id="password-eye-open" class="h-5 w-5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M2.03555 12.3224C1.96647 12.1151 1.9664 11.8907 2.03536 11.6834C3.42372 7.50972 7.36079 4.5 12.0008 4.5C16.6387 4.5 20.5742 7.50692 21.9643 11.6776C22.0334 11.8849 22.0335 12.1093 21.9645 12.3166C20.5761 16.4903 16.6391 19.5 11.9991 19.5C7.36119 19.5 3.42564 16.4931 2.03555 12.3224Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg id="password-eye-closed" class="h-5 w-5 hidden" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3.97993 8.22257C3.05683 9.31382 2.35242 10.596 1.93436 12.0015C3.22565 16.338 7.24311 19.5 11.9991 19.5C12.9917 19.5 13.9521 19.3623 14.8623 19.1049M6.22763 6.22763C7.88389 5.13558 9.86771 4.5 12 4.5C16.756 4.5 20.7734 7.66205 22.0647 11.9985C21.3528 14.3919 19.8106 16.4277 17.772 17.772M6.22763 6.22763L3 3M6.22763 6.22763L9.87868 9.87868M17.772 17.772L21 21M17.772 17.772L14.1213 14.1213M14.1213 14.1213C14.6642 13.5784 15 12.8284 15 12C15 10.3431 13.6569 9 12 9C11.1716 9 10.4216 9.33579 9.87868 9.87868M14.1213 14.1213L9.87868 9.87868" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('password-toggle');
            const eyeOpen = document.getElementById('password-eye-open');
            const eyeClosed = document.getElementById('password-eye-closed');

            if (!passwordInput || !toggleButton || !eyeOpen || !eyeClosed) {
                return;
            }

            toggleButton.addEventListener('click', () => {
                passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
                const isVisible = passwordInput.type === 'text';

                eyeOpen.classList.toggle('hidden', isVisible);
                eyeClosed.classList.toggle('hidden', !isVisible);
                toggleButton.setAttribute('aria-label', isVisible ? '{{ __('Hide password') }}' : '{{ __('Show password') }}');
            });
        });
    </script>
</x-guest-layout>
