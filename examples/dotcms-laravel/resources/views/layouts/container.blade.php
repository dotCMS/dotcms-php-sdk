@php
$containerAttrs = [
    'data-dot-object' => 'container',
    'data-dot-identifier' => $containerRef->identifier ?? '',
    'data-dot-accept-types' => $containerRef->acceptTypes ?? '',
    'data-max-contentlets' => $containerRef->maxContentlets ?? '',
    'data-dot-uuid' => $containerRef->uuid ?? ''
];
@endphp

<div {!! $dotCmsHelpers->htmlAttr($containerAttrs) !!}>
    @foreach($containerRef->contentlets as $content)
        @php
        $contentAttrs = [
            'data-dot-object' => 'contentlet',
            'data-dot-identifier' => $content['identifier'] ?? '',
            'data-dot-basetype' => $content['baseType'] ?? '',
            'data-dot-title' => $content['widgetTitle'] ?? $content['title'] ?? '',
            'data-dot-inode' => $content['inode'] ?? '',
            'data-dot-type' => $content['contentType'] ?? '',
            'data-dot-container' => json_encode([
                'acceptTypes' => $containerRef->acceptTypes ?? '',
                'identifier' => $containerRef->identifier ?? '',
                'maxContentlets' => $containerRef->maxContentlets ?? '',
                'variantId' => $containerRef->variantId ?? '',
                'uuid' => $containerRef->uuid ?? ''
            ])
        ];
        @endphp
        
        <div {!! $dotCmsHelpers->htmlAttr($contentAttrs) !!}>
            {!! $dotCmsHelpers->generateHtmlBasedOnProperty($content) !!}
        </div>
    @endforeach
</div> 