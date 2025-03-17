<header class="container">
    <div class="grid">
        <div>
            <h1>{{ $pageAsset->page->title ?? 'DotCMS Laravel' }}</h1>
        </div>
        <div>
            @include('dotcms.components.navigation')
        </div>
    </div>
</header> 