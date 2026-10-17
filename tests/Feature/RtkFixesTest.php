<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lokasi;
use App\Models\PengajuanKerusakan;
use App\Models\PeminjamanFasilitas;
use App\Models\Fasilitas;
use App\Models\PeminjamanFasilitasItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RtkFixesTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    public function test_laporan_kerusakan_relationship_search()
    {
        $rtkUser = User::role('rtk')->first();
        if (!$rtkUser) {
            $rtkUser = User::factory()->create(['email' => 'rtk@ucic.ac.id', 'nama' => 'RTK Admin', 'status_akun' => 'active']);
            $rtkUser->assignRole('rtk');
        }

        $pelapor = User::factory()->create(['nama' => 'TestPelaporSearchKeyword', 'email' => 'pelapor_test@example.com']);
        $pelapor->assignRole('pelapor');

        $lokasi = Lokasi::first() ?? Lokasi::create(['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'nama_ruangan' => 'R1', 'is_active' => true]);
        $fasilitas = Fasilitas::create([
            'nama_fasilitas' => 'TestFacilitySearchKeyword',
            'deskripsi' => 'Desc',
            'status_kondisi' => 'baik',
            'lokasi_id' => $lokasi->id,
            'kategori' => 'alat',
            'total_stok' => 10,
            'stok_tersedia' => 10
        ]);

        $laporan = PengajuanKerusakan::create([
            'pelapor_id' => $pelapor->id,
            'fasilitas_id' => $fasilitas->id,
            'lokasi_id' => $lokasi->id,
            'jenis_kerusakan' => 'Kerusakan Ringan',
            'lokasi_detail' => 'Detail',
            'deskripsi' => 'Deskripsi',
            'tingkat_urgensi' => 'rendah',
            'status_laporan' => 'pending'
        ]);

        $response = $this->actingAs($rtkUser)
            ->getJson(route('rtk.laporan-kerusakan.index', [
                'draw' => 1,
                'columns' => [
                    ['data' => 'id', 'name' => 'id', 'searchable' => 'true', 'orderable' => 'true'],
                    ['data' => 'pelapor', 'name' => 'pelapor', 'searchable' => 'true', 'orderable' => 'false'],
                    ['data' => 'fasilitas', 'name' => 'fasilitas', 'searchable' => 'true', 'orderable' => 'false'],
                ],
                'search' => ['value' => 'TestPelaporSearchKeyword']
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals($laporan->id, $data[0]['id']);

        $responseFasilitas = $this->actingAs($rtkUser)
            ->getJson(route('rtk.laporan-kerusakan.index', [
                'draw' => 1,
                'columns' => [
                    ['data' => 'id', 'name' => 'id', 'searchable' => 'true', 'orderable' => 'true'],
                    ['data' => 'pelapor', 'name' => 'pelapor', 'searchable' => 'true', 'orderable' => 'false'],
                    ['data' => 'fasilitas', 'name' => 'fasilitas', 'searchable' => 'true', 'orderable' => 'false'],
                ],
                'search' => ['value' => 'TestFacilitySearchKeyword']
            ]));

        $responseFasilitas->assertStatus(200);
        $dataFasilitas = $responseFasilitas->json('data');
        $this->assertNotEmpty($dataFasilitas);
        $this->assertEquals($laporan->id, $dataFasilitas[0]['id']);
    }

    public function test_peminjaman_relationship_search()
    {
        $rtkUser = User::role('rtk')->first();
        if (!$rtkUser) {
            $rtkUser = User::factory()->create(['email' => 'rtk@ucic.ac.id', 'nama' => 'RTK Admin', 'status_akun' => 'active']);
            $rtkUser->assignRole('rtk');
        }

        $peminjam = User::factory()->create(['nama' => 'TestPeminjamSearchKeyword', 'email' => 'peminjam_test@example.com']);
        $peminjam->assignRole('pelapor');

        $lokasi = Lokasi::first() ?? Lokasi::create(['kampus' => 'Kampus 1', 'lantai' => 'Lantai 1', 'nama_ruangan' => 'R1', 'is_active' => true]);
        $fasilitas = Fasilitas::create([
            'nama_fasilitas' => 'TestFacilityLoanSearchKeyword',
            'deskripsi' => 'Desc',
            'status_kondisi' => 'baik',
            'lokasi_id' => $lokasi->id,
            'kategori' => 'alat',
            'total_stok' => 10,
            'stok_tersedia' => 10
        ]);

        $peminjaman = PeminjamanFasilitas::create([
            'peminjam_id' => $peminjam->id,
            'waktu_mulai' => now()->addDay(),
            'waktu_selesai' => now()->addDays(2),
            'keperluan' => 'Keperluan Test',
            'status_pinjam' => 'pending'
        ]);

        PeminjamanFasilitasItem::create([
            'peminjaman_id' => $peminjaman->id,
            'fasilitas_id' => $fasilitas->id,
            'jumlah_dipinjam' => 2,
            'status_item' => 'pending'
        ]);

        $response = $this->actingAs($rtkUser)
            ->getJson(route('rtk.peminjaman.index', [
                'draw' => 1,
                'columns' => [
                    ['data' => 'id', 'name' => 'id', 'searchable' => 'true', 'orderable' => 'true'],
                    ['data' => 'peminjam', 'name' => 'peminjam', 'searchable' => 'true', 'orderable' => 'false'],
                    ['data' => 'fasilitas', 'name' => 'fasilitas', 'searchable' => 'true', 'orderable' => 'false'],
                ],
                'search' => ['value' => 'TestPeminjamSearchKeyword']
            ]));

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotEmpty($data);
        $this->assertEquals($peminjaman->id, $data[0]['id']);

        $responseFasilitas = $this->actingAs($rtkUser)
            ->getJson(route('rtk.peminjaman.index', [
                'draw' => 1,
                'columns' => [
                    ['data' => 'id', 'name' => 'id', 'searchable' => 'true', 'orderable' => 'true'],
                    ['data' => 'peminjam', 'name' => 'peminjam', 'searchable' => 'true', 'orderable' => 'false'],
                    ['data' => 'fasilitas', 'name' => 'fasilitas', 'searchable' => 'true', 'orderable' => 'false'],
                ],
                'search' => ['value' => 'TestFacilityLoanSearchKeyword']
            ]));

        $responseFasilitas->assertStatus(200);
        $dataFasilitas = $responseFasilitas->json('data');
        $this->assertNotEmpty($dataFasilitas);
        $this->assertEquals($peminjaman->id, $dataFasilitas[0]['id']);
    }

    public function test_lokasi_active_status_toggle()
    {
        $rtkUser = User::role('rtk')->first();
        if (!$rtkUser) {
            $rtkUser = User::factory()->create(['email' => 'rtk@ucic.ac.id', 'nama' => 'RTK Admin', 'status_akun' => 'active']);
            $rtkUser->assignRole('rtk');
        }

        $lokasi = Lokasi::create([
            'kampus' => 'Kampus 1',
            'lantai' => 'Lantai 1',
            'kode_ruangan' => 'R-999',
            'nama_ruangan' => 'Ruang Testing',
            'is_active' => true
        ]);

        $this->assertTrue($lokasi->is_active);

        $response = $this->actingAs($rtkUser)
            ->put(route('rtk.lokasi.update', $lokasi), [
                'kampus' => 'Kampus 1',
                'lantai' => 'Lantai 1',
                'kode_ruangan' => 'R-999',
                'nama_ruangan' => 'Ruang Testing Updated',
            ]);

        $response->assertRedirect(route('rtk.lokasi.index'));
        
        $lokasi->refresh();
        $this->assertFalse($lokasi->is_active);
    }
}
