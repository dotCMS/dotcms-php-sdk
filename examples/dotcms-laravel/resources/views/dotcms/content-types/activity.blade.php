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
        <p>{{ $content['description'] ?? '' }}</p>
        <a href="/activities/{{ $content['urlTitle'] ?? '#' }}">Link to detail →</a>
    </div>
</article> 