<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" type='text/css' href="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/devicon.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxStyles
    </head>
    <body class="min-h-screen">
        <livewire:navigation />

        <flux:main container>
            @include('layouts.partials.messages')

            @if (isset($header))
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <div class="w-full sm:w-auto">
                            <h5 class="text-2xl lg:text-4xl font-bold">
                                {{ $header }}
                            </h5>
    
                            @if (isset($subheader))
                                <div class="mt-1">
                                    {{ $subheader }}
                                </div>
                            @endif
                        </div>
    
                        @if (isset($action))
                            <div>
                                {{ $action }}
                            </div>
                        @endif
                    </div>

                    <flux:separator />
                </div>
            @endif

            <div>
                {{ $slot }}
            </div>
        </flux:main>

        @persist('toast')
            <flux:toast position="top right" />
        @endpersist
        @fluxScripts
    </body>
</html>
