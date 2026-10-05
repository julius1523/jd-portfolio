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

    @php
        $weights = [100, 300, 400, 500, 700, 900];
        $fontFiles = [];
        foreach ($weights as $w) {
            $match = glob(public_path("build/assets/roboto-latin-{$w}-normal-*.woff2"))[0] ?? null;
            if ($match) {
                $fontFiles[] = basename($match);
            }
        }
    @endphp
    @foreach ($fontFiles as $file)
        <link rel="preload" as="font" type="font/woff2" crossorigin href="/build/assets/{{ $file }}">
    @endforeach

    @vite(['resources/js/app.js'])

</head>

<body>
    <div id="app"></div>
</body>

</html>