{{-- Navigation component for DotCMS --}}
<nav>
    <ul class="flex space-x-4 text-white">
        @if(isset($navigation) && $navigation->hasChildren())
            @foreach($navigation->getChildren() as $item)
                <li>
                    <a href="{{ $item->href ?? $item['href'] }}" target="{{ $item->target ?? $item['target'] }}">{{ $item->title ?? $item['title'] }}</a>
                    @if($item->hasChildren())
                        <ul class="flex gap-4 mt-2">
                            @foreach($item->getChildren() as $child)
                                <li>
                                    <a href="{{ $child->href ?? $child['href'] }}" target="{{ $child->target ?? $child['target'] }}">{{ $child->title ?? $child['title'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        @endif
    </ul>
</nav> 