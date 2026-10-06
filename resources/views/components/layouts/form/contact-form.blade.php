@php
    use Statamic\Facades\GlobalSet;

    $set = GlobalSet::findByHandle('contact_label_information')?->inCurrentSite();
    $contactLabel = $set?->augmentedValue('data')?->value() ?? [];

    $successHtml = nl2br((string) ($set['success_message'] ?? ''));
    $failedHtml = nl2br((string) ($set['message_failed'] ?? ''));
@endphp

<statamic:form:contact class="contact-form flex flex-col gap-4">

    {{-- Fields Form --}}
    <div class="flex flex-wrap gap-4">
        @foreach ($fields as $field)
            <div
                class="contact-form-field text-sm flex flex-col gap-1 {{ ($field['width'] ?? 100) <= 50 ? 'w-full md:w-[calc(50%-0.5rem)]' : 'w-full' }}">
                @if (($field['type'] ?? 'text') === 'select')
                    <select name="{{ $field['handle'] }}"
                        {{ in_array('required', (array) ($field['validate'] ?? [])) || str_contains($field['validate'] ?? '', 'required') ? 'required' : '' }}
                        class="contact-form-input rounded-xl px-5 py-4 w-full border border-[#CECECE]">
                        <option value="" disabled selected>{{ $field['placeholder'] ?? ($field['display'] ?? '') }}
                        </option>
                        @foreach ($field['options'] ?? [] as $optKey => $optLabel)
                            <option value="{{ $optKey }}">{{ $optLabel }}</option>
                        @endforeach
                    </select>
                @elseif (($field['type'] ?? 'text') === 'textarea')
                    <textarea name="{{ $field['handle'] }}" rows="5"
                        placeholder="{{ $field['placeholder'] ?? ($field['display'] ?? '') }}"
                        class="contact-form-input rounded-xl px-5 py-4 w-full border border-[#CECECE] h-50 lg:h-76"></textarea>
                @else
                    <input type="{{ $field['input_type'] ?? 'text' }}" name="{{ $field['handle'] }}"
                        placeholder="{{ $field['placeholder'] ?? ($field['display'] ?? '') }}"
                        class="contact-form-input rounded-xl px-5 py-4 w-full border border-[#CECECE]" />
                @endif

                @if (!empty($field['error']))
                    <p class="text-sm text-red-600">{{ $field['error'] }}</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Button Submit --}}
    <div>
        <button type="submit" class="button button--primary">
            {{ $contactLabel['submit_button_label'] ?? 'Kirim' }}
        </button>
    </div>

    {{-- Success Summary --}}
    @if ($success)
        <div class="bard-content rounded-xl bg-green-50 px-5 py-4 text-(--color-primary)/50 border border-(--color-primary)/30">
            {!! $successHtml ?: $success !!}
        </div>
    @endif

    {{-- Error Summary --}}
    @if (count($errors))
        <div class="rounded-xl bg-red-50 px-5 py-4 text-red-800 border border-red-800/30">
            @if (!empty($failedHtml))
                <div class="bard-content mb-2 font-medium">{!! $failedHtml !!}</div>
            @endif
            <ul class="flex flex-col gap-1">
                @foreach ($errors as $error_message)
                    <li">{{ $error_message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

</statamic:form:contact>
