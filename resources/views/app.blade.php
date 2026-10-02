@php
    $settings = app(\App\Services\SystemSettingsService::class)->all();
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <title>{{ $settings['systemName'] }}</title>

    <script defer src="https://cloud.umami.is/script.js"
        data-website-id="af43aa23-ee8d-4988-85ee-504b83234836"></script>

    <style>
        @layer vuetify-core, vuetify-components, vuetify-overrides, vuetify-utilities, uno, vuetify-final;
    </style>

    <script>
        window.__SYSTEM_SETTINGS__ = {{ Js::from($settings) }};
        window.__AUTH_USER__ = {{ Js::from(
    auth()->user() ? [
        'id' => auth()->user()->id,
        'name' => auth()->user()->name,
        'email' => auth()->user()->email,
    ]
    : null
) }};
    </script>

    @vite(['resources/js/app.js'])

</head>

<body>
    <div id="app"></div>
</body>

</html>