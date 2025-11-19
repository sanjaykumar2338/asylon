<x-guest-layout>
    <div class="mx-auto max-w-lg space-y-6">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Follow up on an existing case') }}</h1>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Paste the Case ID or follow-up link you saved after submitting your report.') }}
            </p>

            <form method="POST" action="{{ route('followup.redirect') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="case_id" class="block text-sm font-medium text-gray-700">
                        {{ __('Case ID or follow-up link') }}
                    </label>
                    <input type="text" name="case_id" id="case_id" required
                        value="{{ old('case_id') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        placeholder="{{ __('e.g. case ID or https://.../followup/your-token') }}">
                    @error('case_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-end">
                    <x-primary-button>{{ __('Continue') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
