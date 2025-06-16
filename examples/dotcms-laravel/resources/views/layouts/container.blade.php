{{-- Use the SDK's renderContainer method which includes empty container logic --}}
{!! $dotCmsHelpers->renderContainer($containerRef, $containerRef->contentlets ?? [], $mode ?? null) !!} 