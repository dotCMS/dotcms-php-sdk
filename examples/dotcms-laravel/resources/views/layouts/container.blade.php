@php
$containerObject = $dotCmsHelpers->getContainerData($containers, $containerRef);
$containerContent = $containerObject['contentlets'] ?? [];

$containerAttrs = [
    'data-dot-object' => 'container',
    'data-dot-identifier' => $containerRef->identifier ?? '',
    'data-dot-accept-types' => $containerObject['acceptTypes'] ?? '',
    'data-max-contentlets' => $containerObject['maxContentlets'] ?? '',
    'data-dot-uuid' => $containerRef->uuid ?? ''
];
@endphp

<div {!! $dotCmsHelpers->htmlAttr($containerAttrs) !!}>
    @foreach($containerContent as $content)
        @php
        $contentAttrs = [
            'data-dot-object' => 'contentlet',
            'data-dot-identifier' => $content['identifier'] ?? '',
            'data-dot-basetype' => $content['baseType'] ?? '',
            'data-dot-title' => $content['widgetTitle'] ?? $content['title'] ?? '',
            'data-dot-inode' => $content['inode'] ?? '',
            'data-dot-type' => $content['contentType'] ?? '',
            'data-dot-container' => json_encode([
                'acceptTypes' => $containerObject['acceptTypes'] ?? '',
                'identifier' => $containerRef->identifier ?? '',
                'maxContentlets' => $containerObject['maxContentlets'] ?? '',
                'variantId' => $containerObject['variantId'] ?? '',
                'uuid' => $containerRef->uuid ?? ''
            ])
        ];
        @endphp
        
        <div {!! $dotCmsHelpers->htmlAttr($contentAttrs) !!}>
            {!! $dotCmsHelpers->generateHtmlBasedOnProperty($content) !!}
        </div>
    @endforeach
</div> 