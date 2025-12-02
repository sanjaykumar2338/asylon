<x-guest-layout container-class="w-full max-w-4xl mt-8 px-6 sm:px-10 py-10 bg-white shadow-md overflow-hidden sm:rounded-lg">
    <h1 class="text-3xl font-semibold text-gray-900 mb-6">SMS Opt-In (HR Onboarding Example)</h1>

    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 space-y-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700">Staff Name</label>
            <div class="mt-1 h-10 bg-white border border-gray-300 rounded-md px-3 flex items-center text-gray-500">Sample Name</div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700">Phone Number</label>
            <div class="mt-1 h-10 bg-white border border-gray-300 rounded-md px-3 flex items-center text-gray-500">+1 (555) 123-4567</div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-semibold text-gray-700">Consent</label>
            <div class="mt-1 bg-white border border-gray-300 rounded-md px-3 py-2 text-gray-800">
                I acknowledge that I will receive internal safety alerts via SMS from Asylon.
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Signature</label>
                <div class="mt-1 h-10 bg-white border-b-2 border-gray-400"></div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700">Date</label>
                <div class="mt-1 h-10 bg-white border-b-2 border-gray-400"></div>
            </div>
        </div>
    </div>

    <p class="text-sm text-gray-600 mt-4">
        This static mock illustrates the HR onboarding packet staff complete to provide written consent for SMS alerts. It is for carrier verification only and does not collect data.
    </p>
</x-guest-layout>
