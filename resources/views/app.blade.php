<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <title>{{ config('app.name') }}</title>

    @vite(['resources/js/app.js'])
</head>

<body>
    <div id="app"></div>

    <script>
        window.__AUTH_USER__ = {{ Js::from(
    auth()->user() ? [
        'id' => auth()->user()->id,
        'name' => auth()->user()->name,
        'email' => auth()->user()->email,
    ]
    : null
) }};
    </script>
</body>

</html>