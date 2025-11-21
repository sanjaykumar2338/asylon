@props(['class' => 'lang-switch flex items-center justify-end gap-2 text-sm'])

@php
    $languages = config('asylon.languages', []);
    $currentLocale = app()->getLocale();
@endphp

@if (count($languages) > 1)
    <form method="GET" action="{{ url()->current() }}" class="{{ $class }} ml-auto">
        @foreach (request()->except('lang') as $name => $value)
            @if (is_array($value))
                @foreach ($value as $item)
                    <input type="hidden" name="{{ $name }}[]" value="{{ $item }}">
                @endforeach
            @elseif($value !== null)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endif
        @endforeach

        <label class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-gray-500" viewBox="0 0 16 16" aria-hidden="true">
                <path d="M4.5 8a7.5 7.5 0 0 0 7.11 7.475A8 8 0 1 1 8 0c.262 0 .52.013.775.038A7.5 7.5 0 0 0 4.5 8Zm8.5 5.746A6.5 6.5 0 0 1 5.019 8H8a.5.5 0 0 1 .478.645l-.437 1.308.829 1.238a.5.5 0 0 1-.108.663l-.916.733.183 1.099a.5.5 0 0 1-.542.581L6.04 14.32l-1.064.532a.5.5 0 0 1-.658-.214l-.72-1.265a6.5 6.5 0 0 1 9.402.373Z"/>
                <path d="M12 5.466C12 6.88 11.536 8 10 8c-1.091 0-1.92-.458-2.566-1.03-.367-.322-.683-.676-.973-1.026-.303-.364-.585-.74-.902-1.05C4.79 4.28 3.771 4.041 2.5 4.246a.5.5 0 0 1-.686-.47 7.468 7.468 0 0 1 .483-1.932.52.52 0 0 1 .304-.28C3.698 1.143 4.84 1 6 1c2.697 0 5.231 1.45 6.423 3.522a.52.52 0 0 1 .076.276c-.013.229-.021.46-.021.692a4.1 4.1 0 0 1-.478.636Z"/>
            </svg>
            <span class="sr-only">{{ __('common.language_selector_label') }}</span>
            <select
                name="lang"
                class="border-0 bg-transparent pr-2 text-gray-900 focus:border-0 focus:outline-none focus:ring-0"
                onchange="this.form.submit()"
            >
                @foreach ($languages as $code => $label)
                    <option value="{{ $code }}" @selected($currentLocale === $code)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </label>
    </form>
@endif
