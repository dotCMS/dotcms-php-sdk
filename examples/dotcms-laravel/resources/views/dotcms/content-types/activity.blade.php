@php
$imagePath = null;
if (isset($content['image'])) {
    $imagePath = env('DOTCMS_HOST') . '/dA/' . $content['identifier'] . '/image';
}
$title = $content['title'] ?? '';
$description = $content['description'] ?? '';
$urlTitle = $content['urlTitle'] ?? '#';
@endphp

<article class="p-4 overflow-hidden bg-white rounded shadow-lg">
    @if($imagePath)
        <img 
            class="w-full" 
            src="{{ $imagePath }}" 
            width="100" 
            height="100" 
            alt="Activity Image">
    @endif
    <div class="px-6 py-4">
        <p class="mb-2 text-xl font-bold">{{ $title }}</p>
        <p class="text-base line-clamp-3">{{ $description }}</p>
    </div>
    <div class="px-6 pt-4 pb-2">
        <a 
            href="/activities/{{ $urlTitle }}"
            class="inline-block px-4 py-2 font-bold text-white bg-purple-500 rounded-full hover:bg-purple-700"
        >
            Link to detail →
        </a>
    </div>
</article> 