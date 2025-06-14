{{-- 
Container template using SDK functionality for empty state handling and UVE compatibility
--}}
{!! $dotCmsHelpers->renderContainer($containerRef, $containerRef->contentlets ?? [], $mode ?? null) !!} 