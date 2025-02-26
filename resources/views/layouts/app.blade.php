<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@3/dist/fp.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <div x-data="{ show: false, message: '' }"
            x-show="show"
            x-transition
            x-cloak
            @toast.window="message = $event.detail; show = true; setTimeout(() => show = false, 3000)"
            class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg">
            <span x-text="message"></span>
        </div>


        <div x-data="fingerprintHandler()" x-init="initFingerprint()">
            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <script>
            function fingerprintHandler() {
                return {
                    fingerprint: null,

                    initFingerprint() {
                        // Load FingerprintJS
                        FingerprintJS.load().then(fp => {
                            fp.get().then(result => {
                                this.fingerprint = result.visitorId; // Unique fingerprint
                                document.cookie = `fingerprint=${this.fingerprint}; path=/; max-age=31536000;`;
                            });
                        });
                    }
                }
            }
        </script>

    </div>
</body>

</html>