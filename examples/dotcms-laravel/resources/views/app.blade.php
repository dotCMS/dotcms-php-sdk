<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($page->page->title) ? $page->page->title : 'DotCMS Laravel' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div id="app">
        <h1>{{ isset($page->page->title) ? $page->page->title : 'DotCMS Laravel' }}</h1>
    </div>
</body>
</html>