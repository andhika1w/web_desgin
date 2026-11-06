<?php

namespace App\Http\Controllers\Pengelola;

use App\Http\Controllers\Controller;
use App\Models\JenisSampah;
use App\Models\Notifikasi;
use App\Models\Setoran;
use App\Models\Tps;
use App\Models\TransaksiPoin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PengelolaTpsController extends Controller
{
    /**
     * Ambil 1 TPS milik akun pengelola yang sedang login (1 akun pengelola = 1 TPS).
     *
     * @return array{0: \Illuminate\Database\Eloquent\Collection<int, Tps>, 1: ?Tps}
     */
    private function tpsAktif(Request $request): array
    {
        $tps = Tps::where('pengelola_id', $request->user()->user_id)
            ->orderBy('tps_id')
            ->first();

        return [collect($tps ? [$tps] : []), $tps];
    }

    private function setAppUserId(Request $request): void
    {
        if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement('SET @app_user_id = ?', [$request->user()->user_id]);
        }
    }

    /**
     * 1. Meja Timbang & Setoran Masuk (/pengelola)
     */
    public function index(Request $request): View
    {
        [$daftarTps, $tps] = $this->tpsAktif($request);

        if (! $tps) {
            return view('pengelola.dashboard', [
                'daftarTps' => $daftarTps,
                'tps' => null,
                'setoranMasuk' => collect(),
                'kapasitasJenis' => collect(),
                'ringkasan' => [],
                'notifikasiBelumBaca' => collect(),
            ]);
        }

        // Setoran yang sedang menuju atau sudah tiba di TPS (siap ditimbang)
        $setoranMasuk = Setoran::with([
            'pengguna',
            'detail.jenisSampah',
            'penjemputan.kurirUser',
            'penjemputan.alamat',
        ])
            ->where('tps_id', $tps->tps_id)
            ->whereIn('status', ['tiba_di_tps', 'menuju_tps', 'diangkut', 'kurir_ditugaskan', 'menunggu_kurir'])
            ->orderByRaw("FIELD(status, 'tiba_di_tps', 'diangkut', 'menuju_tps', 'kurir_ditugaskan', 'menunggu_kurir')")
            ->orderBy('created_at', 'asc')
            ->get();

        $kapasitasJenis = $tps->jenisSampah()
            ->orderByRaw("FIELD(kategori, 'organik', 'anorganik', 'elektronik', 'b3', 'medis')")
            ->get();

        $selesaiHariIni = Setoran::where('tps_id', $tps->tps_id)
            ->where('status', 'selesai')
            ->whereDate('selesai_at', today());

        $ringkasan = [
            'siap_timbang' => $setoranMasuk->where('status', 'tiba_di_tps')->count(),
            'sedang_menuju' => $setoranMasuk->whereIn('status', ['menuju_tps', 'diangkut', 'kurir_ditugaskan'])->count(),
            'selesai_hari_ini' => (clone $selesaiHariIni)->count(),
            'berat_hari_ini_kg' => (float) (clone $selesaiHariIni)->sum('total_berat_aktual_kg'),
            'total_setoran_selesai' => Setoran::where('tps_id', $tps->tps_id)->where('status', 'selesai')->count(),
        ];

        $notifikasiBelumBaca = Notifikasi::where('penerima_id', $request->user()->user_id)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('pengelola.dashboard', compact(
            'daftarTps',
            'tps',
            'setoranMasuk',
            'kapasitasJenis',
            'ringkasan',
            'notifikasiBelumBaca'
        ));
    }

    /**
     * Tandai setoran sudah tiba di TPS (khususnya untuk antar_sendiri yang baru sampai di pos).
     */
    public function konfirmasiTiba(Request $request, Setoran $setoran): RedirectResponse
    {
        [, $tps] = $this->tpsAktif($request);
        abort_unless($tps && $setoran->tps_id === $tps->tps_id, 403);

        if (! in_array($setoran->status, ['menuju_tps', 'diangkut'], true)) {
            return back()->with('galat', 'Status setoran tidak dapat diubah ke tiba di TPS.');
        }

        $this->setAppUserId($request);

        $setoran->update(['status' => 'tiba_di_tps']);

        if ($setoran->penjemputan && $setoran->penjemputan->status !== 'tiba_di_tps') {
            $setoran->penjemputan->update([
                'status' => 'tiba_di_tps',
                'tiba_at' => now(),
            ]);
        }

        return back()->with('sukses', "Setoran {$setoran->kode_setoran} telah dikonfirmasi tiba di TPS. Silakan isi hasil timbangan.");
    }

    /**
     * Simpan hasil penimbangan berat_aktual_kg & selesaikan setoran (memanggil sp_selesaikan_setoran).
     */
    public function timbangSetoran(Request $request, Setoran $setoran): RedirectResponse
    {
        [, $tps] = $this->tpsAktif($request);
        abort_unless($tps && $setoran->tps_id === $tps->tps_id, 403);

        $setoran->load('detail.jenisSampah');

        $rules = [];
        foreach ($setoran->detail as $d) {
            $rules["berat_aktual.{$d->detail_id}"] = ['required', 'numeric', 'min:0.01', 'max:99999'];
        }

        $validated = $request->validate($rules, [
            'required' => 'Berat aktual wajib diisi untuk semua jenis sampah.',
            'min' => 'Berat aktual minimal 0,01 kg.',
        ]);

        $beratInput = $validated['berat_aktual'] ?? [];

        try {
            DB::transaction(function () use ($request, $setoran, $beratInput) {
                $this->setAppUserId($request);

                // Jika pengelola langsung menimbang setoran antar_sendiri yang statusnya masih menuju_tps
                if (in_array($setoran->status, ['menuju_tps', 'diangkut'], true)) {
                    $setoran->update(['status' => 'tiba_di_tps']);
                }

                // 1. Simpan berat_aktual_kg tiap item detail
                foreach ($setoran->detail as $detail) {
                    $berat = (float) ($beratInput[$detail->detail_id] ?? $detail->berat_estimasi_kg);
                    $detail->update(['berat_aktual_kg' => $berat]);
                }

                // 2. Panggil stored procedure sp_selesaikan_setoran pada MySQL
                if (in_array(DB::connection()->getDriverName(), ['mysql', 'mariadb'], true)) {
                    DB::statement('CALL sp_selesaikan_setoran(?)', [$setoran->setoran_id]);
                } else {
                    // Fallback jika bukan MySQL
                    $totalBerat = 0;
                    $totalPoin = 0;
                    foreach ($setoran->detail()->with('jenisSampah')->get() as $d) {
                        $totalBerat += (float) $d->berat_aktual_kg;
                        $totalPoin += (int) floor($d->berat_aktual_kg * $d->jenisSampah->poin_per_kg);

                        DB::table('tps_jenis_sampah')
                            ->where('tps_id', $setoran->tps_id)
                            ->where('jenis_id', $d->jenis_id)
                            ->increment('terpakai_kg', $d->berat_aktual_kg);
                    }

                    $setoran->update([
                        'status' => 'selesai',
                        'total_berat_aktual_kg' => $totalBerat,
                        'total_poin' => $totalPoin,
                        'selesai_at' => now(),
                    ]);

                    if ($totalPoin > 0) {
                        TransaksiPoin::create([
                            'pengguna_id' => $setoran->pengguna_id,
                            'setoran_id' => $setoran->setoran_id,
                            'tipe' => 'masuk',
                            'jumlah' => $totalPoin,
                            'keterangan' => 'Poin setoran '.$setoran->kode_setoran,
                        ]);
                    }
                }
            });
        } catch (\Throwable $e) {
            return back()->with('galat', 'Gagal menyelesaikan setoran: '.$e->getMessage());
        }

        $setoran->refresh();

        return back()->with(
            'sukses',
            "Setoran {$setoran->kode_setoran} selesai ditimbang ({$setoran->total_berat_aktual_kg} kg). Pengguna menerima +{$setoran->total_poin} poin."
        );
    }

    /**
     * Tolak setoran di TPS dengan alasan_penolakan.
     */
    public function tolakSetoran(Request $request, Setoran $setoran): RedirectResponse
    {
        [, $tps] = $this->tpsAktif($request);
        abort_unless($tps && $setoran->tps_id === $tps->tps_id, 403);

        $data = $request->validate([
            'alasan_penolakan' => ['required', 'string', 'max:255'],
        ]);

        if (in_array($setoran->status, ['selesai', 'ditolak_tps', 'dibatalkan'], true)) {
            return back()->with('galat', 'Setoran ini sudah selesai atau dibatalkan.');
        }

        DB::transaction(function () use ($request, $setoran, $data) {
            $this->setAppUserId($request);

            $setoran->update([
                'status' => 'ditolak_tps',
                'alasan_penolakan' => $data['alasan_penolakan'],
            ]);

            if ($setoran->penjemputan && ! in_array($setoran->penjemputan->status, ['tiba_di_tps', 'dibatalkan'], true)) {
                $setoran->penjemputan->update(['status' => 'dibatalkan']);
            }

            Notifikasi::create([
                'penerima_id' => $setoran->pengguna_id,
                'setoran_id' => $setoran->setoran_id,
                'tipe' => 'setoran_ditolak',
                'judul' => 'Setoran ditolak TPS',
                'pesan' => "Setoran {$setoran->kode_setoran} ditolak oleh TPS: {$data['alasan_penolakan']}",
            ]);
        });

        return back()->with('sukses', "Setoran {$setoran->kode_setoran} telah ditolak.");
    }

    /**
     * 2. Kelola Kapasitas per Jenis Sampah (/pengelola/kapasitas)
     */
    public function kapasitas(Request $request): View
    {
        [$daftarTps, $tps] = $this->tpsAktif($request);

        $semuaJenis = JenisSampah::orderByRaw("FIELD(kategori, 'organik', 'anorganik', 'elektronik', 'b3', 'medis')")
            ->orderBy('nama')
            ->get();

        $pivotMap = $tps
            ? $tps->jenisSampah()->get()->keyBy('jenis_id')
            : collect();

        return view('pengelola.kapasitas', compact('daftarTps', 'tps', 'semuaJenis', 'pivotMap'));
    }

    /**
     * Simpan pengaturan kapasitas_maks_kg, diterima, atau terpakai_kg per jenis sampah.
     */
    public function updateKapasitasJenis(Request $request, JenisSampah $jenisSampah): RedirectResponse
    {
        [, $tps] = $this->tpsAktif($request);
        abort_unless($tps, 404);

        if ($jenisSampah->kategori === 'medis' && ! $tps->izin_limbah_medis) {
            return back()->with('galat', 'TPS Anda belum memiliki izin limbah medis. Ajukan izin medis terlebih dahulu.');
        }

        $data = $request->validate([
            'kapasitas_maks_kg' => ['required', 'numeric', 'min:0', 'max:999999'],
            'terpakai_kg' => ['required', 'numeric', 'min:0', 'max:999999'],
            'diterima' => ['nullable', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        $tps->jenisSampah()->syncWithoutDetaching([
            $jenisSampah->jenis_id => [
                'kapasitas_maks_kg' => $data['kapasitas_maks_kg'],
                'terpakai_kg' => $data['terpakai_kg'],
                'diterima' => $request->boolean('diterima'),
                'catatan' => $data['catatan'] ?? null,
            ],
        ]);

        // Sinkronkan juga kapasitas_total_kg jika diperlukan
        $totalTerpakai = (float) DB::table('tps_jenis_sampah')
            ->where('tps_id', $tps->tps_id)
            ->sum('terpakai_kg');

        $tps->update(['kapasitas_terpakai_kg' => $totalTerpakai]);

        return back()->with('sukses', "Kapasitas petak '{$jenisSampah->nama}' berhasil diperbarui.");
    }

    /**
     * Kosongkan muatan (terpakai_kg = 0) saat sampah diangkut dari TPS ke TPA.
     */
    public function kosongkanPetak(Request $request, JenisSampah $jenisSampah): RedirectResponse
    {
        [, $tps] = $this->tpsAktif($request);
        abort_unless($tps, 404);

        DB::table('tps_jenis_sampah')
            ->where('tps_id', $tps->tps_id)
            ->where('jenis_id', $jenisSampah->jenis_id)
            ->update([
                'terpakai_kg' => 0,
                'updated_at' => now(),
            ]);

        $totalTerpakai = (float) DB::table('tps_jenis_sampah')
            ->where('tps_id', $tps->tps_id)
            ->sum('terpakai_kg');

        $tps->update(['kapasitas_terpakai_kg' => $totalTerpakai]);

        return back()->with('sukses', "Muatan '{$jenisSampah->nama}' telah dikosongkan (0 kg).");
    }

    /**
     * 3. Riwayat Setoran & Ulasan Warga (/pengelola/riwayat)
     */
    public function riwayat(Request $request): View
    {
        [$daftarTps, $tps] = $this->tpsAktif($request);

        $riwayatSetoran = collect();
        $rataRatingTps = null;

        if ($tps) {
            $query = Setoran::with(['pengguna', 'detail.jenisSampah', 'ulasan', 'penjemputan.kurirUser'])
                ->where('tps_id', $tps->tps_id)
                ->orderByDesc('created_at');

            if ($request->filled('status')) {
                $query->where('status', $request->string('status'));
            }

            $riwayatSetoran = $query->paginate(15)->withQueryString();

            $rataRatingTps = DB::table('ulasan')
                ->join('setoran', 'setoran.setoran_id', '=', 'ulasan.setoran_id')
                ->where('setoran.tps_id', $tps->tps_id)
                ->whereNotNull('ulasan.rating_tps')
                ->avg('ulasan.rating_tps');
        }

        return view('pengelola.riwayat', compact('daftarTps', 'tps', 'riwayatSetoran', 'rataRatingTps'));
    }

    /**
     * 4. Profil & Operasional TPS (/pengelola/profil-tps)
     */
    public function profilTps(Request $request): View
    {
        [$daftarTps, $tps] = $this->tpsAktif($request);

        return view('pengelola.profil-tps', compact('daftarTps', 'tps'));
    }

    public function updateProfilTps(Request $request): RedirectResponse
    {
        [, $tps] = $this->tpsAktif($request);
        abort_unless($tps, 404);

        $data = $request->validate([
            'nama_tps' => ['required', 'string', 'max:120'],
            'alamat' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'kapasitas_total_kg' => ['required', 'numeric', 'min:10'],
            'jam_buka' => ['nullable', 'date_format:H:i'],
            'jam_tutup' => ['nullable', 'date_format:H:i'],
            'no_izin' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['buka', 'tutup', 'penuh'])],
        ]);

        $tps->update($data);

        return back()->with('sukses', "Data operasional {$tps->nama_tps} berhasil disimpan.");
    }

    public function storeTpsBaru(Request $request): RedirectResponse
    {
        if (Tps::where('pengelola_id', $request->user()->user_id)->exists()) {
            return back()->with('galat', '1 akun pengelola hanya dapat mendaftarkan 1 lokasi TPS.');
        }

        $data = $request->validate([
            'nama_tps' => ['required', 'string', 'max:120'],
            'alamat' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'kapasitas_total_kg' => ['required', 'numeric', 'min:10'],
            'jam_buka' => ['nullable', 'date_format:H:i'],
            'jam_tutup' => ['nullable', 'date_format:H:i'],
            'no_izin' => ['nullable', 'string', 'max:100'],
            'izin_limbah_medis' => ['nullable', 'boolean'],
        ]);

        $tps = Tps::create([
            'pengelola_id' => $request->user()->user_id,
            'nama_tps' => $data['nama_tps'],
            'alamat' => $data['alamat'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'kapasitas_total_kg' => $data['kapasitas_total_kg'],
            'jam_buka' => $data['jam_buka'] ?? '07:00',
            'jam_tutup' => $data['jam_tutup'] ?? '17:00',
            'no_izin' => $data['no_izin'] ?? null,
            'izin_limbah_medis' => $request->boolean('izin_limbah_medis'),
            'status' => 'buka',
            'is_verified' => false, // Menunggu verifikasi admin
        ]);

        $jenisNonMedis = JenisSampah::where('kategori', '!=', 'medis')->get();
        $kapPerJenis = round($tps->kapasitas_total_kg / max(1, $jenisNonMedis->count()), 2);
        foreach ($jenisNonMedis as $j) {
            $tps->jenisSampah()->attach($j->jenis_id, [
                'kapasitas_maks_kg' => $kapPerJenis,
                'terpakai_kg' => 0,
                'diterima' => true,
            ]);
        }

        $request->session()->put('pengelola_tps_id', $tps->tps_id);

        return redirect()->route('pengelola.dashboard')->with(
            'sukses',
            "Lokasi {$tps->nama_tps} berhasil diajukan dan menunggu verifikasi admin."
        );
    }

    public function bacaNotifikasi(Request $request): RedirectResponse
    {
        Notifikasi::where('penerima_id', $request->user()->user_id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back();
    }
}

