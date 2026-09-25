<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kuliner') - Desa Wisata</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    <div class="flex min-h-screen">

    {{-- Tombol hamburger (hanya muncul di mobile) --}}
    <div class="md:hidden fixed top-0 left-0 right-0 z-50  bg-purple-800 px-4 py-3 flex items-center justify-between">
        <span class="font-bold text-white">Desa Wisata</span>
        <button onclick="toggleSidebar()" class="text-white focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    {{-- Overlay gelap saat sidebar terbuka di mobile --}}
    <div id="overlay" onclick="toggleSidebar()"
        class="hidden fixed inset-0 bg-black/50 z-30 md:hidden"></div>

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="w-64  bg-purple-800 text-white flex flex-col fixed inset-y-0 left-0 z-40 -translate-x-full transition-transform duration-300 md:relative md:translate-x-0"
        id="sidebar">
            <div class="p-6 border-b border-purple-700">
                <h1 class="text-xl font-bold">Desa Wisata</h1>
                <p class="text-purple-300 text-sm mt-1">Kuliner</p>
            </div>

            <nav class="flex-1 p-4 space-y-1">
                <a href="{{ route('kuliner.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-purple-700 {{ request()->routeIs('kuliner.dashboard') ? 'bg-purple-700' : '' }}">
                    <span>🍽️</span> Transaksi
                </a>
                <a href="{{ route('kuliner.menu') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-purple-700 {{ request()->routeIs('kuliner.menu*') ? 'bg-purple-700' : '' }}">
                    <span>📋</span> Kelola Menu
                </a>
                <a href="{{ route('kuliner.pakan_ikan') }}"
                   class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-purple-700 {{ request()->routeIs('kuliner.pakan_ikan') ? 'bg-purple-700' : '' }}">
                    <span>🐟</span> Pakan Ikan
                </a>
                <a href="{{ route('kuliner.gasebo') }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-purple-700 {{ request()->routeIs('kuliner.gasebo*') ? 'bg-purple-700' : '' }}">
                    <span>🏠</span> Gasebo
                </a>
            </nav>

            {{-- Info user & logout --}}
            <div class="p-4 border-t border-purple-700">
                <p class="text-sm text-purple-300">Login sebagai</p>
                <p class="font-medium truncate">{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 rounded-lg hover:bg-purple-700 text-sm text-purple-200">
                        🚪 Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- Konten utama --}}
        <main class="flex-1 p-8 pt-14 md:pt-8">
            
            {{-- Notifikasi sukses --}}
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Notifikasi error --}}
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