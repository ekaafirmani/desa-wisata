<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kolam') - Desa Wisata</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    
    <div class="md:hidden fixed top-0 left-0 right-0 z-50 bg-cyan-800 shadow px-4 py-3 flex items-center justify-between">
        <span class="font-bold text-white">Desa Wisata</span>
        <button onclick="toggleSidebar()" class="text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div id="overlay" onclick="toggleSidebar()"
        class="hidden fixed inset-0 bg-black/50 z-30 md:hidden"></div>


    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside id="sidebar" class="w-64 bg-cyan-800 text-white flex flex-col fixed inset-y-0 left-0 z-40 -translate-x-full transition-transform duration-300 md:relative md:translate-x-0">
            <div class="p-6 border-b border-cyan-700">
                <h1 class="text-xl font-bold">Desa Wisata</h1>
                <p class="text-cyan-200 text-sm mt-1">Kolam</p>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('kolam.tiket') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-cyan-700 {{ request()->routeIs('kolam.tiket') ? 'bg-cyan-700' : '' }}">
                    <span>🎫</span> Tiket Kolam
                </a>
                <a href="{{ route('kolam.pelampung') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-cyan-700 {{ request()->routeIs('kolam.pelampung*') ? 'bg-cyan-700' : '' }}">
                    <span>🛟</span> Sewa Pelampung
                </a>
                <a href="{{ route('kolam.pakan_ikan') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-cyan-700 {{ request()->routeIs('kolam.pakan_ikan') ? 'bg-cyan-700' : '' }}">
                    <span>🐟</span> Pakan Ikan
                </a>
            </nav>

            {{-- Info user & logout --}}
            <div class="p-4 border-t border-cyan-700">
                <p class="text-sm text-cyan-200">Login sebagai</p>
                <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 rounded-lg hover:bg-cyan-700 text-sm text-cyan-100">
                        🚪 Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Konten utama --}}
        <main class="flex-1 p-8 pt-14 md:pt-8">

            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>

</body>
</html>