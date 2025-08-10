<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        @include('layouts.navigation')

        <main class="py-4 mx-5 px-0 px-xl-5">
            <div class="container-fluid px-0">
                @if (View::hasSection('breadcrumbs'))
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            @yield('breadcrumbs')
                        </ol>
                    </nav>
                @endif

                <div class="row">
                    <div class="col">
                        @yield('content')
                    </div>
                </div>
            </div>
            @stack('scripts')
        </main>
    </body>
</html>
