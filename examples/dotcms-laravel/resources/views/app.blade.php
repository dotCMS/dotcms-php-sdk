<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DotCMS Laravel</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div id="app">
        <h1>Welcome to DotCMS Laravel</h1>
        <p>This is the default view for all routes.</p>
        <p>Current URL Path: {{ request()->path() }}</p>
    </div>
</body>
</html>