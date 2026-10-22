@php
    use Statamic\Facades\Entry;

    $currentPageId = null;

    $currentEntry = Entry::findByUri('/' . ltrim(request()->getPathInfo(), '/'));

    if ($currentEntry) {
        $currentPageId = $currentEntry->id();
    } elseif (isset($page) && is_object($page) && method_exists($page, 'id')) {
        $currentPageId = $page->id();
    } elseif (isset($id)) {
        $currentPageId = is_object($id) ? (string) $id : $id;
    }

    $popUps = Entry::query()
        ->where('collection', 'pop_up')
        ->get()
        ->filter(function ($popUp) use ($currentPageId) {
            $locations = $popUp->value('pop_up_location');

            if (blank($locations)) {
                return false;
            }

            $locationIds = collect(is_iterable($locations) ? $locations : [$locations])
                ->map(fn($loc) => is_object($loc) && method_exists($loc, 'id') ? $loc->id() : (string) $loc)
                ->filter()
                ->all();

            return in_array((string) $currentPageId, $locationIds, true);
        });

    $resolvePopUpUrl = function ($value) {
        if (blank($value)) {
            return null;
        }
        if (is_string($value) && str_starts_with($value, 'entry::')) {
            return Entry::find(str_replace('entry::', '', $value))?->url();
        }
        return is_string($value) ? $value : null;
    };
@endphp

@foreach ($popUps as $popUp)
    @php
        $image = $popUp->augmentedValue('pop_up_image')->value();
        $imageUrl = is_object($image) && method_exists($image, 'url') ? $image->url() : $image;
        $linkUrl = $resolvePopUpUrl($popUp->value('url'));
        $isExternal = $linkUrl && str_starts_with($linkUrl, 'http');

        $popUpKey = 'popup-' . $popUp->id();
    @endphp

    <dialog id="site-popup-{{ $popUp->id() }}" class="site-popup" data-popup-key="{{ $popUpKey }}"
        data-auto-open="true">
        <div class="site-popup-inner aspect-square w-full">

            {{-- Icon close --}}
            <button type="button"
                class="site-popup-close focus:outline-none focus:border-0 absolute cursor-pointer border-0 z-10 text-white text-xl right-2 top-1 md:text-3xl md:right-3 md:top-1"
                onclick="this.closest('dialog').close()" aria-label="Tutup">
                &times;
            </button>

            @if ($linkUrl)
                <a href="{{ $linkUrl }}" class="block h-full"
                    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif>
            @endif

            @if ($imageUrl)
                <img src="{{ \App\Support\Img::url($imageUrl, 1200) }}" alt="{{ (is_object($image) && method_exists($image, 'alt') ? $image->alt : null) ?? $popUp->value('title') ?? '' }}"
                    class="w-full h-full object-cover pointer-events-none block">
            @else
                <div class="p-8 text-center">
                    <h2>{{ $popUp->value('title') ?? '' }}</h2>
                </div>
            @endif

            @if ($linkUrl)
                </a>
            @endif

        </div>
    </dialog>
@endforeach
