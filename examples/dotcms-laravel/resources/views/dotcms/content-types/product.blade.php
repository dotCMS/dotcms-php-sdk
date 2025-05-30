@php
$imagePath = null;
if (isset($content['image'])) {
    $imagePath = env('DOTCMS_HOST') . '/dA/' . $content['identifier'] . '/image';
}
$title = $content['title'] ?? '';
$retailPrice = $content['retailPrice'] ?? null;
$salePrice = $content['salePrice'] ?? null;
$urlTitle = $content['urlTitle'] ?? '#';

// Format price 
$formatPrice = function($price) {
    return '$' . number_format((float)$price, 2, '.', ',');
};
@endphp

<div class="overflow-hidden bg-white rounded shadow-lg">
    <div class="p-4">
        @if($imagePath)
            <img 
                class="object-contain w-full max-h-60" 
                src="{{ $imagePath }}" 
                width="100" 
                height="100" 
                alt="Product Image">
        @endif
    </div>
    <div class="px-6 py-4 bg-slate-100">
        <div class="mb-2 text-xl font-bold line-clamp-1">
            {{ $title }}
        </div>
        @if($retailPrice && $salePrice)
            <div class="text-gray-500 line-through">
                {{ $formatPrice($retailPrice) }}
            </div>
            <div class="text-3xl font-bold">
                {{ $formatPrice($salePrice) }}
            </div>
        @else
            <div class="min-h-6"></div>
            <div class="text-3xl font-bold">
                @if($retailPrice)
                    {{ $formatPrice($retailPrice) }}
                @elseif($salePrice)
                    {{ $formatPrice($salePrice) }}
                @else
                    $0.00
                @endif
            </div>
        @endif
        <a
            href="/store/products/{{ $urlTitle }}"
            class="inline-block px-4 py-2 mt-4 text-white bg-green-500 rounded hover:bg-green-600"
        >
            Buy Now
        </a>
    </div>
</div> 