<x-layout>
    <x-slot:heading>
        Money Organiser Dashboard
    </x-slot:heading>

    <!-- Vue App Mount Point -->
    <div id="app">
        <home-page></home-page>
    </div>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
</x-layout>
