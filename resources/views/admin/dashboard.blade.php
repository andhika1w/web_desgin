@extends('layouts.admin')

@section('title', 'Ikhtisar & Verifikasi Izin Medis/B3')
@section('heading', 'Pengawasan Limbah Medis, B3 & Verifikasi Izin')
@section('subheading', 'Verifikasi izin fasilitas penampungan medis/B3 & kurir khusus, pantau daya tampung, dan awasi manifes setoran.')

@section('content')

    {{-- Pita Status Operasional (Data Nyata Database) --}}
    <section aria-label="Ringkasan Operasional" class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-y sm:divide-y-0 sm:divide-x divide-stone-200">
            <div class="p-4">
                <div class="text-xs text-stone-500">Akun Menunggu</div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-bold angka-tabular {{ $ringkasan['akun_pending'] > 0 ? 'text-amber-700' : 'text-[#1F2421]' }}">
                        {{ $ringkasan['akun_pending'] }}
                    </span>
                    <span class="text-xs text-stone-500">pendaftar</span>
                </div>
            </div>

            <div class="p-4">
                <div class="text-xs text-stone-500">TPS Belum Verifikasi</div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-bold angka-tabular {{ $ringkasan['tps_belum_verifikasi'] > 0 ? 'text-amber-700' : 'text-[#1F2421]' }}">
                        {{ $ringkasan['tps_belum_verifikasi'] }}
                    </span>
                    <span class="text-xs text-stone-500">lokasi</span>
                </div>
            </div>

            <div class="p-4">
                <div class="text-xs text-stone-500">TPS Buka & Aktif</div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-bold angka-tabular text-[#1D6B45]">{{ $ringkasan['tps_aktif'] }}</span>
                    <span class="text-xs text-stone-500">beroperasi</span>
                </div>
            </div>

            <div class="p-4">
                <div class="text-xs text-stone-500">Kurir Online/Tugas</div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-bold angka-tabular text-[#1F2421]">{{ $ringkasan['kurir_online'] }}</span>
                    <span class="text-xs text-stone-500">kurir</span>
                </div>
            </div>

            <div class="p-4">
                <div class="text-xs text-stone-500">Setoran Berjalan</div>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-2xl font-bold angka-tabular text-[#1F2421]">{{ $ringkasan['setoran_berjalan'] }}</span>
                    <span class="text-xs text-stone-500">transaksi</span>
                </div>
            </div>

            <div class="p-4">
                <div class="text-xs text-stone-500">Total Ditimbang</div>
                <div class="mt-1 flex items-baseline gap-1.5">
                    <span class="text-2xl font-bold angka-tabular text-[#1F2421]">
                        {{ number_format($ringkasan['total_berat_selesai_kg'], 1, ',', '.') }}
                    </span>
                    <span class="text-xs font-semibold text-stone-500">kg</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ANTREAN VERIFIKASI (Aksi Utama Admin) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- 1. Antrean Verifikasi Akun Pengelola TPS & Kurir --}}
        <section class="bg-white border border-stone-200 rounded-xl flex flex-col">
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-[#1F2421]">Verifikasi Pendaftaran Akun</h2>
                    <p class="text-xs text-stone-500">Pengelola TPS & kurir membutuhkan persetujuan sebelum bisa masuk.</p>
                </div>
                <a href="{{ route('admin.akun', ['status' => 'pending']) }}" class="text-xs font-semibold text-[#1D6B45] hover:underline shrink-0">
                    Lihat semua ({{ $ringkasan['akun_pending'] }})
                </a>
            </div>

            @if ($akunPending->isEmpty())
                <div class="p-6 text-sm text-stone-500">
                    Tidak ada antrean pendaftaran akun yang menunggu verifikasi saat ini.
                </div>
            @else
                <div class="divide-y divide-stone-200">
                    @foreach ($akunPending as $u)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-sm text-[#1F2421]">{{ $u->nama }}</span>
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded {{ $u->role === 'kurir' ? 'bg-sky-100 text-sky-900' : 'bg-amber-100 text-amber-900' }}">
                                        {{ $u->role === 'kurir' ? 'Kurir' : 'Pengelola TPS' }}
                                    </span>
                                </div>
                                <div class="text-xs text-stone-600 mt-0.5 angka-tabular">
                                    {{ $u->email }} · {{ $u->no_hp }}
                                </div>
                                @if ($u->kurirProfil)
                                    <div class="text-xs text-stone-500 mt-1 angka-tabular">
                                        Kendaraan: <strong class="text-stone-700">{{ $u->kurirProfil->jenis_kendaraan }}</strong>
                                        ({{ $u->kurirProfil->plat_nomor }})
                                        @if ($u->kurirProfil->kapasitas_angkut_kg)
                                            · maks {{ number_format($u->kurirProfil->kapasitas_angkut_kg, 0, ',', '.') }} kg
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 shrink-0">
                                <form method="POST" action="{{ route('admin.akun.status', $u) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="aktif">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#1D6B45] hover:bg-[#13502F] text-white text-xs font-semibold cursor-pointer">
                                        Setujui
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('admin.akun.status', $u) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="ditolak">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg border border-red-300 text-red-700 hover:bg-red-50 text-xs font-semibold cursor-pointer">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- 2. Antrean Verifikasi TPS --}}
        <section class="bg-white border border-stone-200 rounded-xl flex flex-col">
            <div class="px-5 py-4 border-b border-stone-200 flex items-center justify-between gap-2">
                <div>
                    <h2 class="text-base font-bold text-[#1F2421]">TPS Menunggu Verifikasi Izin</h2>
                    <p class="text-xs text-stone-500">Hanya TPS terverifikasi yang masuk rekomendasi setoran warga.</p>
                </div>
                <a href="{{ route('admin.tps') }}" class="text-xs font-semibold text-[#1D6B45] hover:underline shrink-0">
                    Kelola TPS
                </a>
            </div>

            @if ($tpsBelumVerifikasi->isEmpty())
                <div class="p-6 text-sm text-stone-500">
                    Semua lokasi TPS yang terdaftar sudah diverifikasi.
                </div>
            @else
                <div class="divide-y divide-stone-200">
                    @foreach ($tpsBelumVerifikasi as $t)
                        <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-sm text-[#1F2421]">{{ $t->nama_tps }}</span>
                                    @if ($t->izin_limbah_medis)
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-900">
                                            Pengajuan Izin Medis
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-stone-600 mt-0.5">{{ $t->alamat }}</div>
                                <div class="text-xs text-stone-500 mt-1 angka-tabular">
                                    Pengelola: <strong class="text-stone-700">{{ $t->pengelola?->nama ?? '-' }}</strong>
                                    · Kapasitas {{ number_format($t->kapasitas_total_kg, 0, ',', '.') }} kg
                                    @if ($t->no_izin)
                                        · Izin: {{ $t->no_izin }}
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('admin.tps.verifikasi', $t) }}" class="shrink-0">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="is_verified" value="1">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#1D6B45] hover:bg-[#13502F] text-white text-xs font-semibold cursor-pointer">
                                    Verifikasi TPS
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    {{-- PAPAN KAPASITAS TPS (Tanda Khas: Lembar Kendali Kapasitas Timbangan TPS) --}}
    <section class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-stone-200 flex flex-wrap items-center justify-between gap-2">
            <div>
                <h2 class="text-base font-bold text-[#1F2421]">Kapasitas & Status TPS</h2>
                <p class="text-xs text-stone-500">Kapasitas terpakai disinkronkan otomatis dari timbangan jenis sampah di tiap TPS.</p>
            </div>
            <a href="{{ route('admin.tps') }}" class="text-xs font-semibold text-[#1D6B45] hover:underline">
                Tambah / Atur TPS
            </a>
        </div>

        @if ($daftarTps->isEmpty())
            <div class="p-6 text-sm text-stone-500">
                Belum ada TPS terdaftar. Tambahkan TPS baru melalui menu <a href="{{ route('admin.tps') }}" class="font-semibold text-[#1D6B45] underline">Data & Kapasitas TPS</a>.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-stone-200 bg-stone-50 text-xs font-semibold text-stone-600">
                            <th class="py-3 px-4">Nama TPS & Alamat</th>
                            <th class="py-3 px-4">Pengelola</th>
                            <th class="py-3 px-4">Izin & Verifikasi</th>
                            <th class="py-3 px-4 w-64">Keterisian Kapasitas (kg)</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @foreach ($daftarTps as $tps)
                            @php
                                $persen = $tps->kapasitas_total_kg > 0
                                    ? min(100, round(($tps->kapasitas_terpakai_kg / $tps->kapasitas_total_kg) * 100, 1))
                                    : 0;
                                $warnaBar = $persen >= 90 ? 'bg-red-600' : ($persen >= 70 ? 'bg-amber-500' : 'bg-[#1D6B45]');
                            @endphp
                            <tr class="hover:bg-stone-50/70">
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-[#1F2421]">{{ $tps->nama_tps }}</div>
                                    <div class="text-xs text-stone-500">{{ $tps->alamat }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-xs text-stone-700">
                                    <div class="font-medium">{{ $tps->pengelola?->nama ?? '-' }}</div>
                                    <div class="text-stone-500 angka-tabular">{{ $tps->pengelola?->no_hp }}</div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="flex flex-wrap gap-1.5">
                                        @if ($tps->is_verified)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-emerald-100 text-emerald-900">Terverifikasi</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-900">Belum Verifikasi</span>
                                        @endif

                                        @if ($tps->izin_limbah_medis)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-900">Izin Limbah Medis</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 angka-tabular">
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="font-semibold text-[#1F2421]">
                                            {{ number_format($tps->kapasitas_terpakai_kg, 1, ',', '.') }} / {{ number_format($tps->kapasitas_total_kg, 0, ',', '.') }} kg
                                        </span>
                                        <span class="text-stone-500">{{ $persen }}%</span>
                                    </div>
                                    <div class="w-full h-2 rounded-full bg-stone-200 overflow-hidden">
                                        <div class="h-full {{ $warnaBar }}" style="width: {{ $persen }}%"></div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @php
                                        $badgeStatus = match ($tps->status) {
                                            'buka' => 'bg-emerald-50 text-emerald-800 border-emerald-300',
                                            'penuh' => 'bg-red-50 text-red-800 border-red-300',
                                            default => 'bg-stone-100 text-stone-700 border-stone-300',
                                        };
                                    @endphp
                                    <span class="inline-block px-2.5 py-0.5 text-xs font-bold uppercase rounded border {{ $badgeStatus }}">
                                        {{ $tps->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- SETORAN TERBARU & LOG AKTIVITAS ADMIN --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Setoran Terbaru (2 kolom) --}}
        <section class="lg:col-span-2 bg-white border border-stone-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-200">
                <h2 class="text-base font-bold text-[#1F2421]">Setoran Sampah Terbaru</h2>
                <p class="text-xs text-stone-500">Pantauan transaksi penjemputan kurir maupun antar sendiri ke TPS.</p>
            </div>

            @if ($setoranTerbaru->isEmpty())
                <div class="p-6 text-sm text-stone-500">
                    Belum ada transaksi setoran yang tercatat.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-stone-200 bg-stone-50 text-xs font-semibold text-stone-600">
                                <th class="py-2.5 px-4">Kode & Waktu</th>
                                <th class="py-2.5 px-4">Pengguna & TPS</th>
                                <th class="py-2.5 px-4">Metode</th>
                                <th class="py-2.5 px-4">Status</th>
                                <th class="py-2.5 px-4 text-right">Berat / Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-200">
                            @foreach ($setoranTerbaru as $s)
                                <tr class="hover:bg-stone-50/70">
                                    <td class="py-3 px-4 angka-tabular">
                                        <div class="font-semibold text-xs text-[#1F2421]">{{ $s->kode_setoran }}</div>
                                        <div class="text-xs text-stone-500">{{ $s->created_at?->format('d/m/Y H:i') }}</div>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-xs text-[#1F2421]">{{ $s->pengguna?->nama ?? '-' }}</div>
                                        <div class="text-xs text-stone-500">→ {{ $s->tps?->nama_tps ?? '-' }}</div>
                                    </td>
                                    <td class="py-3 px-4 text-xs">
                                        @if ($s->metode === 'dijemput')
                                            <span class="font-medium text-sky-800">Dijemput Kurir</span>
                                            @if ($s->penjemputan?->kurirUser)
                                                <div class="text-stone-500">{{ $s->penjemputan->kurirUser->nama }}</div>
                                            @endif
                                        @else
                                            <span class="font-medium text-stone-700">Antar Sendiri</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <span class="inline-block px-2 py-0.5 text-xs font-semibold rounded bg-stone-100 text-stone-800">
                                            {{ str_replace('_', ' ', $s->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right angka-tabular text-xs">
                                        @if ($s->total_berat_aktual_kg !== null)
                                            <div class="font-semibold text-[#1F2421]">{{ number_format($s->total_berat_aktual_kg, 2, ',', '.') }} kg</div>
                                            <div class="text-[#1D6B45] font-semibold">+{{ $s->total_poin }} poin</div>
                                        @else
                                            <span class="text-stone-400">Belum ditimbang</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        {{-- Log Aktivitas Admin --}}
        <section class="bg-white border border-stone-200 rounded-xl flex flex-col">
            <div class="px-5 py-4 border-b border-stone-200">
                <h2 class="text-base font-bold text-[#1F2421]">Log Aktivitas Admin</h2>
                <p class="text-xs text-stone-500">Rekam jejak verifikasi & perubahan oleh admin.</p>
            </div>

            @if ($logAdmin->isEmpty())
                <div class="p-6 text-sm text-stone-500">
                    Belum ada aktivitas admin yang tercatat.
                </div>
            @else
                <ul class="divide-y divide-stone-200 text-xs">
                    @foreach ($logAdmin as $log)
                        <li class="p-3.5 space-y-1">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-semibold text-[#1F2421]">{{ str_replace('_', ' ', ucfirst($log->aksi)) }}</span>
                                <span class="text-stone-400 angka-tabular">{{ $log->created_at?->format('d/m H:i') }}</span>
                            </div>
                            <div class="text-stone-600">
                                Oleh <strong>{{ $log->admin?->nama ?? 'Admin' }}</strong>
                                @if (!empty($log->detail['nama']))
                                    · {{ $log->detail['nama'] }}
                                @elseif (!empty($log->detail['nama_tps']))
                                    · {{ $log->detail['nama_tps'] }}
                                @elseif (!empty($log->detail['judul']))
                                    · {{ $log->detail['judul'] }}
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>

@endsection

