<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Desa Wisata</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans min-h-screen flex">

    {{-- Kiri: Ilustrasi/Branding --}}
    <div class="hidden lg:flex lg:w-1/2 bg-green-700 flex-col justify-between p-12">
        <div>
            <div class="flex items-center gap-3 mb-12">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <span class="text-white text-xl">🌿</span>
                </div>
                <span class="text-white font-bold text-xl">Desa Wisata</span>
            </div>

            <h2 class="text-white text-4xl font-bold leading-tight mb-4">
                Sistem Manajemen<br>Terpadu
            </h2>
            <p class="text-green-200 text-lg leading-relaxed">
                Kelola seluruh operasional wisata dari satu platform — tiket, wahana, kuliner, hingga laporan pendapatan.
            </p>
        </div>

        {{-- Kartu fitur di bagian bawah kiri --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white/10 rounded-xl p-4">
                <span class="text-2xl">🎫</span>
                <p class="text-white text-sm font-medium mt-2">Loket & Tiket</p>
                <p class="text-green-200 text-xs mt-1">Tiket masuk kendaraan</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4">
                <span class="text-2xl">🚣</span>
                <p class="text-white text-sm font-medium mt-2">Wahana Tubing</p>
                <p class="text-green-200 text-xs mt-1">Mini & dewasa</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4">
                <span class="text-2xl">🏊</span>
                <p class="text-white text-sm font-medium mt-2">Area Kolam</p>
                <p class="text-green-200 text-xs mt-1">Kolam & pelampung</p>
            </div>
            <div class="bg-white/10 rounded-xl p-4">
                <span class="text-2xl">🍽️</span>
                <p class="text-white text-sm font-medium mt-2">Kuliner</p>
                <p class="text-green-200 text-xs mt-1">Kasir & kelola menu</p>
            </div>
        </div>
    </div>

    {{-- Kanan: Form Login --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">

            {{-- Logo mobile (hanya muncul di layar kecil) --}}
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-10 h-10 bg-green-700 rounded-lg flex items-center justify-center">
                    <span class="text-white text-xl">🌿</span>
                </div>
                <span class="font-bold text-gray-800 text-xl">Desa Wisata</span>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-1">Masuk ke Sistem</h1>
            <p class="text-gray-500 text-sm mb-8">Masukkan email dan password untuk melanjutkan.</p>

            {{-- Pesan error dari session --}}
            @if ($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           autocomplete="email" autofocus required
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent @error('email') border-red-400 @enderror"
                           placeholder="nama@email.com">
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                               autocomplete="current-password" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-600 focus:border-transparent @error('password') border-red-400 @enderror"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                </div>

                {{-- Tombol login --}}
                <button type="submit"
                        class="w-full bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg text-sm font-medium transition">
                    Masuk
                </button>

            </form>

            {{-- Kembali ke landing --}}
            <p class="mt-6 text-center text-sm text-gray-400">
                <a href="{{ route('welcome') }}" class="text-green-600 hover:underline">
                    ← Kembali ke halaman utama
                </a>
            </p>

        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" /><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />';
            }
        }
    </script>

</body>
</html>