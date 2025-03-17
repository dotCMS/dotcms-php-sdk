@php
$imageHtml = '';
if (isset($content['image'])) {
    $imageHtml = '<img src="https://demo.dotcms.com/dA/' . $content['identifier'] . '/image" 
                alt="' . htmlspecialchars($content['title'] ?? '', ENT_QUOTES, 'UTF-8') . '">';
}
@endphp

<article>
    {!! $imageHtml !!}
    <div>
        <h3>{{ $content['title'] ?? '' }}</h3>
        <div>{{ $content['retailPrice'] ?? '' }}</div>
        <div>{{ $content['salePrice'] ?? '' }}</div>
        <a href="/store/products/{{ $content['urlTitle'] ?? '#' }}">Buy Now</a>
    </div>
</article> 