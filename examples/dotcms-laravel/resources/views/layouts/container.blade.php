@php
$containerObject = $dotCmsHelpers->getContainersData($containers, $container);
$containerContentKey = 'uuid-' . $container['uuid'];
$containerContent = isset($containerObject['contentlets'][$containerContentKey]) 
    ? $containerObject['contentlets'][$containerContentKey] 
    : [];

$containerAttrs = [
    'data-dot-object' => 'container',
    'data-dot-identifier' => $container['identifier'] ?? '',
    'data-dot-accept-types' => $containerObject['acceptTypes'] ?? '',
    'data-max-contentlets' => $containerObject['maxContentlets'] ?? '',
    'data-dot-uuid' => $container['uuid'] ?? ''
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
                'identifier' => $container['identifier'] ?? '',
                'maxContentlets' => $containerObject['maxContentlets'] ?? '',
                'variantId' => $containerObject['variantId'] ?? '',
                'uuid' => $container['uuid'] ?? ''
            ])
        ];
        @endphp
        
        <div {!! $dotCmsHelpers->htmlAttr($contentAttrs) !!}>
            {!! $dotCmsHelpers->generateHtmlBasedOnProperty($content) !!}
        </div>
    @endforeach
</div> 