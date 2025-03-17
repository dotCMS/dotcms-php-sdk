@php
$imageHtml = '';
if (isset($content['image'])) {
    $imageHtml = '<img src="https://demo.dotcms.com/dA/' . $content['identifier'] . '/image" 
                alt="' . htmlspecialchars($content['title'] ?? '', ENT_QUOTES, 'UTF-8') . '">';
}
@endphp

<article>
    {!! $imageHtml !!}
    @if(isset($content['title']))<h2>{{ $content['title'] }}</h2>@endif
    @if(isset($content['caption']))<p>{{ $content['caption'] }}</p>@endif
    @if(isset($content['buttonText']))
        <a href="{{ $content['link'] ?? '#' }}">{{ $content['buttonText'] }}</a>
    @endif
</article> 