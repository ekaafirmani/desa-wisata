<?php

namespace Tests\Feature\Admin;

use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengeluaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_only_sees_expenses_from_the_current_month(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'aktif' => true,
        ]);

        $current = Pengeluaran::create([
            'nama_pengeluaran' => 'Listrik bulan ini',
            'nominal' => 150000,
            'tanggal' => now()->toDateString(),
        ]);

        $previous = Pengeluaran::create([
            'nama_pengeluaran' => 'Listrik bulan lalu',
            'nominal' => 100000,
            'tanggal' => now()->subMonth()->toDateString(),
        ]);

        $next = Pengeluaran::create([
            'nama_pengeluaran' => 'Listrik bulan depan',
            'nominal' => 200000,
            'tanggal' => now()->addMonth()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.pengeluaran'));

        $response->assertOk();
        $response->assertSee($current->nama_pengeluaran);
        $response->assertSee('Rp 150.000');
        $response->assertDontSee($previous->nama_pengeluaran);
        $response->assertDontSee($next->nama_pengeluaran);
    }

    public function test_admin_can_create_an_expense(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'aktif' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.pengeluaran.simpan'), [
            'nama_pengeluaran' => 'Perawatan alat',
            'nominal' => 75000,
            'tanggal' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('admin.pengeluaran'));
        $this->assertDatabaseHas('pengeluaran', [
            'nama_pengeluaran' => 'Perawatan alat',
            'nominal' => 75000,
            'tanggal' => now()->toDateString(),
        ]);
    }

    public function test_admin_can_update_an_expense(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'aktif' => true,
        ]);
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Bahan bakar',
            'nominal' => 50000,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.pengeluaran.update', $pengeluaran), [
            'nama_pengeluaran' => 'Bahan bakar update',
            'nominal' => 65000,
            'tanggal' => now()->toDateString(),
        ]);

        $response->assertRedirect(route('admin.pengeluaran'));
        $this->assertDatabaseHas('pengeluaran', [
            'id' => $pengeluaran->id,
            'nama_pengeluaran' => 'Bahan bakar update',
            'nominal' => 65000,
        ]);
    }

    public function test_admin_can_delete_an_expense(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'aktif' => true,
        ]);
        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran' => 'Sewa kendaraan',
            'nominal' => 120000,
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.pengeluaran.hapus', $pengeluaran));

        $response->assertRedirect(route('admin.pengeluaran'));
        $this->assertDatabaseMissing('pengeluaran', [
            'id' => $pengeluaran->id,
        ]);
    }

    public function test_expense_form_requires_valid_data(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'aktif' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.pengeluaran.simpan'), []);

        $response->assertSessionHasErrors([
            'nama_pengeluaran',
            'nominal',
            'tanggal',
        ]);
    }
}
