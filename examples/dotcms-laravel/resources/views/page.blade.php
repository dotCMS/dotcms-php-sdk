@extends('layouts.app')

@section('title', isset($pageAsset->page->title) ? $pageAsset->page->title : 'DotCMS Laravel')

@section('content')

    {{-- Page Content --}}
    @if(isset($pageAsset->layout) && isset($pageAsset->layout->body) && isset($pageAsset->layout->body['rows']))
        @foreach($pageAsset->layout->body['rows'] as $row)
            <div class="container">
                <div data-dot-object="row" class="row{{ isset($row['styleClass']) ? ' ' . $row['styleClass'] : '' }}">
                    @if(isset($row['columns']) && !empty($row['columns']))
                        @foreach($row['columns'] as $column)
                            @php
                                $startClass = 'col-start-' . ($column['leftOffset'] ?? 0);
                                $endClass = 'col-end-' . (($column['width'] ?? 12) + ($column['leftOffset'] ?? 0));
                            @endphp
                            
                            <div data-dot-object="column" class="{{ $startClass }} {{ $endClass }}{{ isset($column['styleClass']) ? ' ' . $column['styleClass'] : '' }}">
                                @if(isset($column['containers']) && !empty($column['containers']))
                                    @foreach($column['containers'] as $container)
                                        @include('layouts.container', [
                                            'container' => $container,
                                            'containers' => $pageAsset->containers ?? []
                                        ])
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="container" style="margin-top: 20px;">
            <div class="notice">
                <p>The page layout is either empty or has a different structure than expected.</p>
                
                @if(isset($pageAsset->entity) && isset($pageAsset->entity->layout))
                    {{-- Handle dotCMS API response format where layout is inside 'entity' --}}
                    <div class="container">
                        <h2>Content from entity structure</h2>
                        <div class="content-area">
                            @if(isset($pageAsset->entity->containers) && !empty($pageAsset->entity->containers))
                                @foreach($pageAsset->entity->containers as $containerId => $container)
                                    <div class="container-wrapper">
                                        <h3>{{ $containerId }}</h3>
                                        @if(isset($container->contentlets))
                                            @foreach($container->contentlets as $uuid => $contentletArray)
                                                @foreach($contentletArray as $contentlet)
                                                    <div class="contentlet">
                                                        <h4>{{ $contentlet->title ?? $contentlet->name ?? 'Unnamed Content' }}</h4>
                                                        @if(isset($contentlet->body))
                                                            <div class="body">{!! $contentlet->body !!}</div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
@endsection