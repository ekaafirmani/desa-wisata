<?php

namespace Database\Seeders;

use App\Models\Gasebo;
use App\Models\LapakUmkm;
use App\Models\MasterStok;
use App\Models\MenuKuliner;
use App\Models\PaketTubing;
use App\Models\PaketWisata;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DummyDataSeeder extends Seeder
{
    private Carbon $startDate;

    private Carbon $endDate;

    /** @var array<string, User> */
    private array $users = [];

    /** @var array<string, PaketTubing> */
    private array $tubingPackages = [];

    /** @var array<int, PaketWisata> */
    private array $paketWisata = [];

    /** @var array<int, MenuKuliner> */
    private array $menus = [];

    /** @var array<int, LapakUmkm> */
    private array $lapaks = [];

    /** @var array<int, Gasebo> */
    private array $gasebos = [];

    /** @var array<string, array<string, int>> */
    private array $monthlyCounts = [];

    public function run(): void
    {
        $this->startDate = Carbon::parse('2026-04-01')->startOfDay();
        $this->endDate = now()->copy();

        if ($this->startDate->greaterThan($this->endDate)) {
            $this->command?->warn('Tanggal sekarang masih sebelum April 2026.');

            return;
        }

        if ($this->hasExistingTransactions()) {
            $this->command?->warn('Data transaksi pada periode tersebut sudah ada. Seeder dihentikan agar tidak ada duplikasi.');

            return;
        }

        DB::transaction(function (): void {
            $this->prepareReferenceData();

            $month = $this->startDate->copy()->startOfMonth();
            $lastMonth = $this->endDate->copy()->startOfMonth();

            while ($month->lessThanOrEqualTo($lastMonth)) {
                $this->seedMonth($month);
                $month->addMonthNoOverflow();
            }
        });

        $this->displaySummary();
    }

    private function hasExistingTransactions(): bool
    {
        $tables = [
            'tiket_masuk',
            'tiket_kolam',
            'sewa_pelampung',
            'transaksi_tubing',
            'transaksi_kuliner',
            'pembayaran_umkm',
            'pakan_ikan',
            'ikan_hias_penjualans',
            'sewa_gazebo',
            'transaksi_paket_wisata',
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && DB::table($table)
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->exists()) {
                return true;
            }
        }

        return false;
    }

    private function prepareReferenceData(): void
    {
        $admin = $this->ensureUser('admin', 'Administrator Dummy', 'dummy-admin@desawisata.test');
        $this->users['admin'] = $admin;

        $this->users['loket'] = $this->ensureUser(
            'loket',
            'Petugas Loket Dummy',
            'dummy-loket@desawisata.test',
            $admin->id
        );
        $this->users['tubing_mini'] = $this->ensureUser(
            'tubing_mini',
            'Petugas Tubing Mini Dummy',
            'dummy-tubing-mini@desawisata.test',
            $admin->id
        );
        $this->users['tubing_dewasa'] = $this->ensureUser(
            'tubing_dewasa',
            'Petugas Tubing Dewasa Dummy',
            'dummy-tubing-dewasa@desawisata.test',
            $admin->id
        );
        $this->users['kolam'] = $this->ensureUser(
            'kolam',
            'Petugas Kolam Dummy',
            'dummy-kolam@desawisata.test',
            $admin->id
        );
        $this->users['kuliner'] = $this->ensureUser(
            'kuliner',
            'Petugas Kuliner Dummy',
            'dummy-kuliner@desawisata.test',
            $admin->id
        );
        $this->users['paket_wisata'] = $this->ensureUser(
            'paket_wisata',
            'Petugas Paket Wisata Dummy',
            'dummy-paket-wisata@desawisata.test',
            $admin->id
        );

        $this->preparePaketTubing();
        $this->preparePaketWisata();
        $this->prepareMenuKuliner();
        $this->prepareLapakUmkm();
        $this->prepareGasebo();
        $this->prepareMasterStok();
    }

    private function ensureUser(string $role, string $name, string $email, ?int $createdBy = null): User
    {
        $user = User::where('role', $role)->where('aktif', true)->first();

        if ($user) {
            return $user;
        }

        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password123'),
                'role' => $role,
                'aktif' => true,
                'created_by' => $createdBy,
            ]
        );
    }

    private function preparePaketTubing(): void
    {
        $definitions = [
            'mini' => [
                'nama_paket' => 'Paket Tubing Mini Dummy',
                'fasilitas' => 'Perahu mini,Reject,life jacket, dan pemandu.',
                'harga' => 35000,
            ],
            'dewasa' => [
                'nama_paket' => 'Paket Tubing Dewasa Dummy',
                'fasilitas' => 'Perahu dewasa, life jacket, dan pemandu.',
                'harga' => 50000,
            ],
        ];

        foreach ($definitions as $jenis => $definition) {
            $package = PaketTubing::where('jenis', $jenis)->where('aktif', true)->first();

            if (!$package) {
                $package = PaketTubing::create([
                    ...$definition,
                    'jenis' => $jenis,
                    'aktif' => true,
                ]);
            }

            $this->tubingPackages[$jenis] = $package;
        }
    }

    private function preparePaketWisata(): void
    {
        $definitions = [
            [
                'nama_paket' => 'Paket Jelajah Desa Dummy',
                'deskripsi' => 'Jelajah atraksi utama desa.',
                'fasilitas' => 'Tiket masuk, pemandu, dan makan siang.',
                'harga_per_orang' => 75000,
                'minimal_orang' => 10,
            ],
            [
                'nama_paket' => 'Paket Family Tour Dummy',
                'deskripsi' => 'Paket wisata untuk keluarga.',
                'fasilitas' => 'Tiket masuk, pemandu, dan snack.',
                'harga_per_orang' => 100000,
                'minimal_orang' => 5,
            ],
        ];

        foreach ($definitions as $definition) {
            $package = PaketWisata::where('nama_paket', $definition['nama_paket'])
                ->where('aktif', true)
                ->first();

            if (!$package) {
                $package = PaketWisata::create([
                    ...$definition,
                    'aktif' => true,
                ]);
            }

            $this->paketWisata[] = $package;
        }
    }

    private function prepareMenuKuliner(): void
    {
        $definitions = [
            ['nama_menu' => 'Nasi Goreng Dummy', 'harga' => 20000, 'kategori' => 'makanan'],
            ['nama_menu' => 'Mie Goreng Dummy', 'harga' => 18000, 'kategori' => 'makanan'],
            ['nama_menu' => 'Es Teh Manis Dummy', 'harga' => 5000, 'kategori' => 'minuman'],
            ['nama_menu' => 'Kopi Susu Dummy', 'harga' => 12000, 'kategori' => 'minuman'],
            ['nama_menu' => 'Kentang Goreng Dummy', 'harga' => 15000, 'kategori' => 'snack'],
        ];

        foreach ($definitions as $definition) {
            $menu = MenuKuliner::where('nama_menu', $definition['nama_menu'])->first();

            if (!$menu) {
                $menu = MenuKuliner::create([
                    ...$definition,
                    'tersedia' => true,
                ]);
            }

            $this->menus[] = $menu;
        }
    }

    private function prepareLapakUmkm(): void
    {
        $definitions = [
            ['nama_pedagang' => 'Pedagang Dummy 1', 'nama_usaha' => 'Sembako Dummy', 'jenis_usaha' => 'Sembako', 'tarif_bulanan' => 150000],
            ['nama_pedagang' => 'Pedagang Dummy 2', 'nama_usaha' => 'Warung Masakan Dummy', 'jenis_usaha' => 'Makanan', 'tarif_bulanan' => 200000],
            ['nama_pedagang' => 'Pedagang Dummy 3', 'nama_usaha' => 'Kerajinan Tangan Dummy', 'jenis_usaha' => 'Kerajinan', 'tarif_bulanan' => 175000],
            ['nama_pedagang' => 'Pedagang Dummy 4', 'nama_usaha' => 'Batik Desa Dummy', 'jenis_usaha' => 'Fashion', 'tarif_bulanan' => 250000],
            ['nama_pedagang' => 'Pedagang Dummy 5', 'nama_usaha' => 'Souvenir Desa Dummy', 'jenis_usaha' => 'Souvenir', 'tarif_bulanan' => 125000],
            ['nama_pedagang' => 'Pedagang Dummy 6', 'nama_usaha' => 'Jus Segar Dummy', 'jenis_usaha' => 'Minuman', 'tarif_bulanan' => 100000],
        ];

        foreach ($definitions as $definition) {
            $lapak = LapakUmkm::where('nama_usaha', $definition['nama_usaha'])->first();

            if (!$lapak) {
                $lapak = LapakUmkm::create([
                    ...$definition,
                    'no_telepon' => '0812' . random_int(10000000, 99999999),
                    'status' => 'aktif',
                    'tanggal_mulai' => '2026-01-01',
                ]);
            }

            $this->lapaks[] = $lapak;
        }
    }

    private function prepareGasebo(): void
    {
        $definitions = [
            ['nama_gasebo' => 'Gazebo Dummy Kecil 1', 'jenis' => 'kecil', 'harga_per_jam' => 25000, 'durasi_minimal' => 1],
            ['nama_gasebo' => 'Gazebo Dummy Kecil 2', 'jenis' => 'kecil', 'harga_per_jam' => 25000, 'durasi_minimal' => 1],
            ['nama_gasebo' => 'Gazebo Dummy Besar 1', 'jenis' => 'besar', 'harga_per_jam' => 40000, 'durasi_minimal' => 3],
            ['nama_gasebo' => 'Gazebo Dummy Besar 2', 'jenis' => 'besar', 'harga_per_jam' => 40000, 'durasi_minimal' => 3],
        ];

        foreach ($definitions as $definition) {
            $gasebo = Gasebo::where('nama_gasebo', $definition['nama_gasebo'])->first();

            if (!$gasebo) {
                $gasebo = Gasebo::create([
                    ...$definition,
                    'status' => 'tersedia',
                    'aktif' => true,
                ]);
            }

            $this->gasebos[] = $gasebo;
        }
    }

    private function prepareMasterStok(): void
    {
        MasterStok::firstOrCreate(
            ['jenis' => 'pelampung'],
            [
                'total_stok' => 100,
                'tersedia' => 100,
                'harga_satuan' => 10000,
            ]
        );

        MasterStok::firstOrCreate(
            ['jenis' => 'pakan_ikan'],
            [
                'total_stok' => 1000,
                'tersedia' => 1000,
                'harga_satuan' => 5000,
            ]
        );
    }

    private function seedMonth(Carbon $month): void
    {
        $this->seedParkir($month);
        $this->seedKolam($month);
        $this->seedTubing($month, 'mini');
        $this->seedTubing($month, 'dewasa');
        $this->seedKuliner($month);
        $this->seedUmkm($month);
        $this->seedPakanIkan($month);
        $this->seedIkanHias($month);
        $this->seedGasebo($month);
        $this->seedPaketWisata($month);
        $this->seedPengeluaran($month);
    }

    private function seedParkir(Carbon $month): void
    {
        $prices = [
            'motor' => 2000,
            'mobil' => 5000,
            'bus' => 10000,
        ];
        $rows = [];

        $count = $this->randomCount($month, 'Parkir');

        for ($i = 0; $i < $count; $i++) {
            $jenis = $this->choice(array_keys($prices));
            $harga = $prices[$jenis];
            $jumlah = match ($jenis) {
                'motor' => random_int(1, 3),
                'mobil' => random_int(1, 2),
                'bus' => 1,
            };
            $moment = $this->randomMoment($month);

            $rows[] = array_merge([
                'user_id' => $this->users['loket']->id,
                'jenis_kendaraan' => $jenis,
                'jumlah' => $jumlah,
                'harga_satuan' => $harga,
                'total_bayar' => $harga * $jumlah,
            ], $this->timestamps($moment));
        }

        $this->insertRows('tiket_masuk', $rows);
    }

    private function seedKolam(Carbon $month): void
    {
        $total = $this->randomCount($month, 'Kolam Renang');
        $tiketCount = random_int(1, $total - 1);
        $pelampungCount = $total - $tiketCount;
        $this->recordCount($month, 'Kolam - Tiket', $tiketCount);
        $this->recordCount($month, 'Kolam - Pelampung', $pelampungCount);

        $tiketRows = [];
        for ($i = 0; $i < $tiketCount; $i++) {
            $jumlah = random_int(1, 10);
            $harga = $this->choice([10000, 15000, 20000]);
            $moment = $this->randomMoment($month);

            $tiketRows[] = array_merge([
                'user_id' => $this->users['kolam']->id,
                'jumlah_orang' => $jumlah,
                'harga_satuan' => $harga,
                'total_bayar' => $jumlah * $harga,
            ], $this->timestamps($moment));
        }
        $this->insertRows('tiket_kolam', $tiketRows);

        $pelampungRows = [];
        for ($i = 0; $i < $pelampungCount; $i++) {
            $jumlah = random_int(1, 5);
            $harga = $this->choice([5000, 7500, 10000]);
            $moment = $this->randomMoment($month);
            $waktuKembali = $moment->copy()->addHours(random_int(1, 6));

            if ($waktuKembali->greaterThan(now())) {
                $waktuKembali = now()->copy();
            }

            $pelampungRows[] = array_merge([
                'user_id' => $this->users['kolam']->id,
                'jumlah' => $jumlah,
                'catatan' => $this->choice(['Sewa pelampung reguler', 'Sewa pelampung kelompok', 'Sewa pelampung hari santai']),
                'harga_satuan' => $harga,
                'total_bayar' => $jumlah * $harga,
                'waktu_kembali' => $waktuKembali->toDateTimeString(),
                'status' => 'kembali',
            ], $this->timestamps($moment));
        }
        $this->insertRows('sewa_pelampung', $pelampungRows);
    }

    private function seedTubing(Carbon $month, string $jenis): void
    {
        $category = $jenis === 'mini' ? 'Tubing Mini' : 'Tubing Dewasa';
        $package = $this->tubingPackages[$jenis];
        $rows = [];
        $user = $this->users[$jenis === 'mini' ? 'tubing_mini' : 'tubing_dewasa'];
        $maxPeserta = $jenis === 'mini' ? 8 : 6;

        $count = $this->randomCount($month, $category);

        for ($i = 0; $i < $count; $i++) {
            $jumlahPeserta = random_int(1, $maxPeserta);
            $moment = $this->randomMoment($month);

            $rows[] = array_merge([
                'user_id' => $user->id,
                'paket_id' => $package->id,
                'jumlah_peserta' => $jumlahPeserta,
                'total_bayar' => $jumlahPeserta * (int) $package->harga,
            ], $this->timestamps($moment));
        }

        $this->insertRows('transaksi_tubing', $rows);
    }

    private function seedKuliner(Carbon $month): void
    {
        $detailRows = [];

        $count = $this->randomCount($month, 'Warung Pokdarwis');

        for ($i = 0; $i < $count; $i++) {
            $moment = $this->randomMoment($month);
            $detailIndexes = (array) array_rand($this->menus, random_int(1, min(3, count($this->menus))));
            $total = 0;

            $transaksiId = DB::table('transaksi_kuliner')->insertGetId([
                'user_id' => $this->users['kuliner']->id,
                'total_bayar' => 0,
                'created_at' => $moment->toDateTimeString(),
                'updated_at' => $moment->toDateTimeString(),
            ]);

            foreach ($detailIndexes as $menuIndex) {
                $menu = $this->menus[$menuIndex];
                $jumlah = random_int(1, 3);
                $subtotal = $jumlah * (int) $menu->harga;
                $total += $subtotal;

                $detailRows[] = [
                    'transaksi_id' => $transaksiId,
                    'menu_id' => $menu->id,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                    'created_at' => $moment->toDateTimeString(),
                    'updated_at' => $moment->toDateTimeString(),
                ];
            }

            DB::table('transaksi_kuliner')
                ->where('id', $transaksiId)
                ->update(['total_bayar' => $total]);
        }

        $this->insertRows('detail_kuliner', $detailRows);
    }

    private function seedUmkm(Carbon $month): void
    {
        $rows = [];

        $count = $this->randomCount($month, 'UMKM');

        for ($i = 0; $i < $count; $i++) {
            $lapak = $this->choice($this->lapaks);
            $moment = $this->randomMoment($month);

            $rows[] = array_merge([
                'lapak_id' => $lapak->id,
                'user_id' => $this->users['admin']->id,
                'bulan' => $month->month,
                'tahun' => $month->year,
                'jumlah_bayar' => $lapak->tarif_bulanan,
                'tanggal_bayar' => $moment->toDateString(),
                'status' => 'lunas',
                'catatan' => $this->choice(['Pembayaran sewa bulanan', 'Pembayaran rutin', 'Pembayaran rutin bulanan']),
            ], $this->timestamps($moment));
        }

        $this->insertRows('pembayaran_umkm', $rows);
    }

    private function seedPakanIkan(Carbon $month): void
    {
        $rows = [];

        $count = $this->randomCount($month, 'Pakan Ikan');

        for ($i = 0; $i < $count; $i++) {
            $jumlah = random_int(1, 10);
            $harga = $this->choice([5000, 7500, 10000]);
            $moment = $this->randomMoment($month);

            $rows[] = array_merge([
                'user_id' => $this->users['kuliner']->id,
                'titik_jual' => $this->choice(['loket', 'kolam', 'kuliner']),
                'jumlah_porsi' => $jumlah,
                'harga_satuan' => $harga,
                'total_bayar' => $jumlah * $harga,
            ], $this->timestamps($moment));
        }

        $this->insertRows('pakan_ikan', $rows);
    }

    private function seedIkanHias(Carbon $month): void
    {
        $rows = [];
        $users = [$this->users['kolam'], $this->users['loket'], $this->users['admin']];

        $count = $this->randomCount($month, 'Ikan Hias');

        for ($i = 0; $i < $count; $i++) {
            $jumlah = random_int(1, 5);
            $harga = $this->choice([5000, 10000, 15000]);
            $moment = $this->randomMoment($month);

            $rows[] = array_merge([
                'user_id' => $this->choice($users)->id,
                'jumlah_ikan' => $jumlah,
                'harga_satuan' => $harga,
                'total_bayar' => $jumlah * $harga,
            ], $this->timestamps($moment));
        }

        $this->insertRows('ikan_hias_penjualans', $rows);
    }

    private function seedGasebo(Carbon $month): void
    {
        $rows = [];
        $newSchema = Schema::hasColumn('sewa_gazebo', 'gasebo_id');

        $count = $this->randomCount($month, 'Gasebo');

        for ($i = 0; $i < $count; $i++) {
            $gasebo = $this->choice($this->gasebos);
            $durasi = random_int(max(1, (int) $gasebo->durasi_minimal), max(2, (int) $gasebo->durasi_minimal + 3));
            $harga = (int) $gasebo->harga_per_jam;
            $moment = $this->randomMoment($month);
            $waktuSelesai = $moment->copy()->addHours($durasi);

            if ($waktuSelesai->greaterThan(now())) {
                $waktuSelesai = now()->copy();
            }

            $row = $newSchema
                ? [
                    'user_id' => $this->users['admin']->id,
                    'gasebo_id' => $gasebo->id,
                    'nama_penyewa' => 'Penyewa Gasebo Dummy',
                    'durasi_jam' => $durasi,
                    'harga_per_jam' => $harga,
                    'total_bayar' => $durasi * $harga,
                    'waktu_mulai' => $moment->toDateTimeString(),
                    'waktu_selesai' => $waktuSelesai->toDateTimeString(),
                    'status' => 'selesai',
                    'catatan' => 'Sewa gasebo dummy',
                ]
                : [
                    'user_id' => $this->users['admin']->id,
                    'titik_jual' => 'admin',
                    'jenis_gazebo' => $gasebo->jenis,
                    'jumlah' => 1,
                    'catatan' => 'Sewa gasebo dummy',
                    'harga_satuan' => $harga,
                    'total_bayar' => $durasi * $harga,
                    'status' => $this->choice(['disewa', 'kembali']),
                    'waktu_kembali' => $waktuSelesai->toDateTimeString(),
                ];

            $rows[] = array_merge($row, $this->timestamps($moment));
        }

        $this->insertRows('sewa_gazebo', $rows);
    }

    private function seedPaketWisata(Carbon $month): void
    {
        $rows = [];

        $count = $this->randomCount($month, 'Paket Wisata');

        for ($i = 0; $i < $count; $i++) {
            $package = $this->choice($this->paketWisata);
            $jumlahOrang = random_int((int) $package->minimal_orang, (int) $package->minimal_orang + 5);
            $moment = $this->randomMoment($month);

            $rows[] = array_merge([
                'user_id' => $this->users['paket_wisata']->id,
                'paket_id' => $package->id,
                'jumlah_orang' => $jumlahOrang,
                'harga_per_orang' => $package->harga_per_orang,
                'total_bayar' => $jumlahOrang * (int) $package->harga_per_orang,
                'nama_pemesan' => 'Pemesan Dummy',
                'no_telepon' => '0813' . random_int(10000000, 99999999),
                'catatan' => 'Pemesanan paket wisata dummy',
            ], $this->timestamps($moment));
        }

        $this->insertRows('transaksi_paket_wisata', $rows);
    }

    private function seedPengeluaran(Carbon $month): void
    {
        $names = [
            'Listrik',
            'Air',
            'Internet',
            'Perawatan Alat',
            'Transportasi',
            'Perlengkapan',
            'Kebersihan',
        ];
        $rows = [];

        $count = $this->randomCount($month, 'Pengeluaran');

        for ($i = 0; $i < $count; $i++) {
            $moment = $this->randomMoment($month);

            $rows[] = array_merge([
                'nama_pengeluaran' => $this->choice($names) . ' Dummy',
                'nominal' => random_int(50000, 2000000),
                'tanggal' => $moment->toDateString(),
            ], $this->timestamps($moment));
        }

        $this->insertRows('pengeluaran', $rows);
    }

    private function randomCount(Carbon $month, string $category): int
    {
        $count = random_int(50, 100);
        $this->recordCount($month, $category, $count);

        return $count;
    }

    private function recordCount(Carbon $month, string $category, int $count): void
    {
        $monthKey = $month->format('Y-m');
        $this->monthlyCounts[$monthKey][$category] =
            ($this->monthlyCounts[$monthKey][$category] ?? 0) + $count;
    }

    private function randomMoment(Carbon $month): Carbon
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->isSameMonth($this->endDate)
            ? $this->endDate->copy()
            : $month->copy()->endOfMonth();

        $startTimestamp = $start->getTimestamp();
        $endTimestamp = max($startTimestamp, $end->getTimestamp());

        return Carbon::createFromTimestamp(random_int($startTimestamp, $endTimestamp));
    }

    private function timestamps(Carbon $moment): array
    {
        return [
            'created_at' => $moment->toDateTimeString(),
            'updated_at' => $moment->toDateTimeString(),
        ];
    }

    private function insertRows(string $table, array $rows): void
    {
        if ($rows !== []) {
            DB::table($table)->insert($rows);
        }
    }

    private function choice(array $values): mixed
    {
        return $values[array_rand($values)];
    }

    private function displaySummary(): void
    {
        if (!$this->command) {
            return;
        }

        $this->command->info('Dummy data April 2026 sampai ' . $this->endDate->format('d-m-Y') . ' berhasil dibuat.');

        foreach ($this->monthlyCounts as $month => $categories) {
            $this->command->line($month . ': ' . json_encode($categories, JSON_UNESCAPED_UNICODE));
        }
    }
}
