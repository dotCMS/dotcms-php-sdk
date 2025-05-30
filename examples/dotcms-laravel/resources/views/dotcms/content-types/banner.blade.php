@php
$imagePath = null;
if (isset($content['image'])) {
    $imagePath = env('DOTCMS_HOST') . '/dA/' . $content['identifier'] . '/image';
}
$title = $content['title'] ?? '';
$caption = $content['caption'] ?? '';
$buttonText = $content['buttonText'] ?? '';
$link = $content['link'] ?? '#';
@endphp

<div class="relative w-full p-4 bg-gray-200 h-96">
    @if($imagePath)
        <div class="absolute inset-0">
            <img src="{{ $imagePath }}" class="object-cover w-full h-full" alt="{{ $title }}">
        </div>
    @endif
    <div class="absolute inset-0 flex flex-col items-center justify-center p-4 text-center text-white">
        <h2 class="mb-2 text-6xl font-bold text-shadow">
            {{ $title }}
        </h2>
        <p class="mb-4 text-xl text-shadow">{{ $caption }}</p>
        <a 
            class="p-4 text-xl transition duration-300 bg-purple-500 rounded hover:bg-purple-600"
            href="{{ $link }}">
            {{ $buttonText }}
        </a>
    </div>
</div> 