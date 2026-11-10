@extends('layouts.pengguna')

@section('title', 'Buat Manifes Setoran Baru')
@section('heading', 'Buat Manifes Setoran Limbah Medis & B3')
@section('subheading', 'Pilih fasilitas berizin, tentukan metode penyerahan, dan masukkan perkiraan berat tiap klasifikasi limbah.')

@section('content')

    <form method="POST" action="{{ route('pengguna.setoran.store') }}" id="form-manifes" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        @csrf

        {{-- KOLOM KIRI (8 Kolom): PILIH FASILITAS, METODE, & RINCIAN LIMBAH --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Langkah 1: Pilih Fasilitas Penampungan & Metode Penyerahan --}}
            <section class="bg-white border border-stone-200 rounded-xl p-5 space-y-5">
                <div class="flex items-center gap-2.5 pb-3 border-b border-stone-200">
                    <span class="w-6 h-6 rounded-full bg-[#133A26] text-[#F2C230] font-judul font-bold text-xs flex items-center justify-center">1</span>
                    <h2 class="font-judul font-bold text-base text-[#1F2421]">Tujuan Fasilitas Berizin &amp; Metode Penyerahan</h2>
                </div>

                {{-- Pilih TPS / Fasilitas --}}
                <div>
                    <label for="tps_id" class="block text-xs font-semibold text-stone-700 mb-1.5">
                        Fasilitas Penampungan / TPS Tujuan <span class="text-red-600">*</span>
                    </label>
                    <select id="tps_id" name="tps_id" required onchange="perbaruiKalkulasiManifes()"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 bg-white text-sm font-semibold text-[#1F2421]">
                        @foreach ($daftarTps as $tps)
                            @php
                                $sisaKg = max(0, (float) $tps->kapasitas_total_kg - (float) $tps->kapasitas_terpakai_kg);
                            @endphp
                            <option value="{{ $tps->tps_id }}"
                                    data-izin-medis="{{ $tps->izin_limbah_medis ? '1' : '0' }}"
                                    data-nama="{{ $tps->nama_tps }}"
                                    @selected(old('tps_id') == $tps->tps_id)>
                                {{ $tps->nama_tps }} — {{ $tps->izin_limbah_medis ? '[BERIZIN LIMBAH MEDIS & B3]' : '[NON-MEDIS / B3 & DAUR ULANG]' }} (Sisa Kuota: {{ number_format($sisaKg, 0, ',', '.') }} kg)
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-stone-500 mt-1">
                        Khusus penyetoran <strong>Limbah Medis</strong> wajib memilih fasilitas dengan status <strong>[BERIZIN LIMBAH MEDIS &amp; B3]</strong>.
                    </p>
                </div>

                {{-- Pilih Metode --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @php $metodeLama = old('metode', 'dijemput'); @endphp
                    <label class="p-3.5 rounded-xl border-2 cursor-pointer transition flex items-start gap-3 {{ $metodeLama === 'dijemput' ? 'border-[#1D6B45] bg-emerald-50/40' : 'border-stone-200 bg-white' }}" id="label-metode-dijemput">
                        <input type="radio" name="metode" value="dijemput" @checked($metodeLama === 'dijemput') onchange="ubahMetodeManifes()" class="mt-1 accent-[#1D6B45]">
                        <div class="text-xs">
                            <div class="font-bold text-sm text-[#1F2421]">Dijemput Kurir Khusus</div>
                            <p class="text-stone-600 mt-0.5">Kurir mengambil kemasan tersegel dari titik koordinat alamat Anda.</p>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl border-2 cursor-pointer transition flex items-start gap-3 {{ $metodeLama === 'antar_sendiri' ? 'border-[#1D6B45] bg-emerald-50/40' : 'border-stone-200 bg-white' }}" id="label-metode-antar">
                        <input type="radio" name="metode" value="antar_sendiri" @checked($metodeLama === 'antar_sendiri') onchange="ubahMetodeManifes()" class="mt-1 accent-[#1D6B45]">
                        <div class="text-xs">
                            <div class="font-bold text-sm text-[#1F2421]">Antar Sendiri ke Fasilitas</div>
                            <p class="text-stone-600 mt-0.5">Bawa langsung kemasan ke meja timbang fasilitas pada jam operasional.</p>
                        </div>
                    </label>
                </div>

                {{-- Pilih Alamat (Hanya tampil jika metode = dijemput) --}}
                <div id="wadah-alamat-jemput" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="alamat_id" class="block text-xs font-semibold text-stone-700">
                            Alamat Titik Penjemputan <span class="text-red-600">*</span>
                        </label>
                        <a href="{{ route('pengguna.alamat') }}" class="text-xs font-semibold text-[#1D6B45] hover:underline">
                            + Tambah Alamat Baru
                        </a>
                    </div>

                    @if ($daftarAlamat->isEmpty())
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-300 text-xs text-amber-950 flex items-center justify-between gap-3">
                            <span>Anda belum memiliki alamat penjemputan terdaftar. Tambahkan alamat terlebih dahulu atau pilih metode Antar Sendiri.</span>
                            <a href="{{ route('pengguna.alamat') }}" class="px-3 py-1.5 rounded-lg bg-[#1D6B45] text-white font-semibold shrink-0">
                                Tambah Alamat
                            </a>
                        </div>
                    @else
                        <select id="alamat_id" name="alamat_id" class="w-full px-3.5 py-2.5 rounded-xl border border-stone-300 bg-white text-sm text-[#1F2421]">
                            @foreach ($daftarAlamat as $al)
                                <option value="{{ $al->alamat_id }}" @selected(old('alamat_id') == $al->alamat_id || $al->is_default)>
                                    {{ $al->label }} {{ $al->is_default ? '(Utama)' : '' }} — {{ $al->alamat_lengkap }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Jadwal & Catatan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="jadwal_waktu" class="block text-xs font-semibold text-stone-700 mb-1">
                            Rencana Waktu Jemput / Tiba (Opsional)
                        </label>
                        <input id="jadwal_waktu" name="jadwal_waktu" type="datetime-local"
                               value="{{ old('jadwal_waktu', now()->addHours(2)->format('Y-m-d\TH:i')) }}"
                               class="w-full px-3 py-2 rounded-xl border border-stone-300 bg-white text-sm angka-tabular">
                    </div>

                    <div>
                        <label for="catatan_pengguna" class="block text-xs font-semibold text-stone-700 mb-1">
                            Catatan Kemasan / Patokan Lokasi (Opsional)
                        </label>
                        <input id="catatan_pengguna" name="catatan_pengguna" type="text" maxlength="255"
                               value="{{ old('catatan_pengguna') }}"
                               placeholder="Mis: 1 Safety Box tersegel + 2 kardus B3"
                               class="w-full px-3 py-2 rounded-xl border border-stone-300 bg-white text-sm">
                    </div>
                </div>
            </section>

            {{-- Langkah 2: Pilih Klasifikasi Limbah & Berat Estimasi (kg) --}}
            <section class="bg-white border border-stone-200 rounded-xl p-5 space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-stone-200">
                    <div class="flex items-center gap-2.5">
                        <span class="w-6 h-6 rounded-full bg-[#133A26] text-[#F2C230] font-judul font-bold text-xs flex items-center justify-center">2</span>
                        <div>
                            <h2 class="font-judul font-bold text-base text-[#1F2421]">Rincian Klasifikasi Limbah &amp; Sampah Terpilah</h2>
                            <p class="text-xs text-stone-500">Isi berat perkiraan (kg) hanya pada jenis limbah yang ingin Anda setorkan.</p>
                        </div>
                    </div>
                    <span id="info-opsi-medis" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#FCE8E6] text-[#B3261E]">
                        Termasuk Opsi Limbah Medis
                    </span>
                </div>

                <div class="divide-y divide-stone-200">
                    @foreach ($jenisSampah as $j)
                        @php
                            $nilaiLama = old("berat_estimasi.{$j->jenis_id}", 0);
                            $badgeKat = match ($j->kategori) {
                                'medis'      => ['bg' => 'bg-[#FCE8E6] text-[#B3261E] border-[#F2B8B5]', 'label' => 'MEDIS KHUSUS'],
                                'b3'         => ['bg' => 'bg-amber-100 text-amber-900 border-amber-300', 'label' => 'LIMBAH B3'],
                                'elektronik' => ['bg' => 'bg-sky-100 text-sky-900 border-sky-300', 'label' => 'E-WASTE'],
                                'anorganik'  => ['bg' => 'bg-emerald-100 text-emerald-900 border-emerald-300', 'label' => 'ANORGANIK'],
                                default      => ['bg' => 'bg-stone-100 text-stone-800 border-stone-300', 'label' => 'ORGANIK'],
                            };
                        @endphp
                        <div data-baris-kategori="{{ $j->kategori }}"
                             class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="min-w-0 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-sm text-[#1F2421]">{{ $j->nama }}</span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded border {{ $badgeKat['bg'] }}">
                                        {{ $badgeKat['label'] }}
                                    </span>
                                    @if ($j->poin_per_kg > 0)
                                        <span class="text-xs font-bold text-[#1D6B45] angka-tabular">+{{ $j->poin_per_kg }} poin/kg</span>
                                    @else
                                        <span class="text-xs font-semibold text-[#B3261E]">Pemusnahan Berizin (0 poin)</span>
                                    @endif
                                </div>
                                <p class="text-xs text-stone-500">{{ $j->deskripsi }}</p>
                            </div>

                            <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                <button type="button"
                                        onclick="ubahBeratForm({{ $j->jenis_id }}, -0.5)"
                                        aria-label="Kurangi {{ $j->nama }}"
                                        class="w-8 h-8 rounded-lg border border-stone-300 bg-stone-100 hover:bg-stone-200 font-bold text-sm flex items-center justify-center cursor-pointer">
                                    −
                                </button>
                                <input type="number"
                                       id="berat-jenis-{{ $j->jenis_id }}"
                                       name="berat_estimasi[{{ $j->jenis_id }}]"
                                       step="0.1"
                                       min="0"
                                       max="500"
                                       value="{{ $nilaiLama }}"
                                       data-nama="{{ $j->nama }}"
                                       data-kategori="{{ $j->kategori }}"
                                       data-poin="{{ $j->poin_per_kg }}"
                                       oninput="perbaruiKalkulasiManifes()"
                                       class="w-20 px-2.5 py-1.5 rounded-lg border border-stone-300 bg-white text-sm font-bold text-right angka-tabular text-[#1F2421]">
                                <span class="text-xs font-semibold text-stone-600">kg</span>
                                <button type="button"
                                        onclick="ubahBeratForm({{ $j->jenis_id }}, 0.5)"
                                        aria-label="Tambah {{ $j->nama }}"
                                        class="w-8 h-8 rounded-lg bg-[#1D6B45] hover:bg-[#13502F] text-white font-bold text-sm flex items-center justify-center cursor-pointer">
                                    +
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        {{-- KOLOM KANAN (4 Kolom): RINGKASAN NOTA MANIFES & VALIDASI IZIN MEDIS --}}
        <div class="lg:col-span-4 lg:sticky lg:top-6 space-y-4">
            <div class="bg-[#133A26] text-white rounded-2xl p-5 shadow-md space-y-4">
                <div class="flex items-center justify-between border-b border-white/10 pb-3">
                    <div>
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#F2C230] block">Pratinjau Dokumen</span>
                        <h3 class="font-judul text-base font-bold">Ringkasan Manifes Setoran</h3>
                    </div>
                    <span id="badge-status-medis" class="px-2 py-0.5 rounded bg-[#F2C230] text-[#1F2421] font-judul font-extrabold text-[10px]">
                        SIAP KIRIM
                    </span>
                </div>

                {{-- Daftar Rincian Terpilih --}}
                <div class="bg-white text-[#1F2421] rounded-xl p-4 space-y-3">
                    <div class="text-xs text-stone-500 border-b border-dashed border-stone-300 pb-2">
                        Tujuan: <strong id="ringkasan-nama-tps" class="text-[#1F2421]">-</strong>
                    </div>

                    <dl id="ringkasan-item-list" class="space-y-1.5 text-xs angka-tabular min-h-[48px]">
                        <div class="text-stone-400 italic">Belum ada jenis limbah yang diisi beratnya.</div>
                    </dl>

                    <div class="pt-2.5 border-t border-dashed border-stone-300 space-y-1 text-xs angka-tabular">
                        <div id="baris-ringkasan-medis" class="flex justify-between text-[#B3261E] font-semibold">
                            <span>Sub-total Limbah Medis:</span>
                            <span id="ringkasan-berat-medis">0,00 kg</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-[#1F2421]">
                            <span>Total Berat Estimasi:</span>
                            <span id="ringkasan-total-berat">0,00 kg</span>
                        </div>
                        <div class="flex justify-between text-sm font-extrabold text-[#1D6B45]">
                            <span>Estimasi Poin Insentif B3:</span>
                            <span id="ringkasan-total-poin">+0 poin</span>
                        </div>
                    </div>
                </div>

                <button type="submit" id="tombol-submit-manifes"
                        class="w-full py-3 px-4 rounded-xl bg-[#F2C230] hover:bg-[#e0b020] text-[#1F2421] font-judul font-extrabold text-sm text-center cursor-pointer transition">
                    Terbitkan Tiket Manifes Setoran
                </button>
            </div>
        </div>
    </form>

    <script>
        function ubahMetodeManifes() {
            const metode = document.querySelector('input[name="metode"]:checked')?.value || 'dijemput';
            const wadahAlamat = document.getElementById('wadah-alamat-jemput');
            const lblJemput = document.getElementById('label-metode-dijemput');
            const lblAntar = document.getElementById('label-metode-antar');

            if (wadahAlamat) {
                wadahAlamat.style.display = metode === 'dijemput' ? 'block' : 'none';
            }
            if (lblJemput && lblAntar) {
                lblJemput.className = 'p-3.5 rounded-xl border-2 cursor-pointer transition flex items-start gap-3 ' + (metode === 'dijemput' ? 'border-[#1D6B45] bg-emerald-50/40' : 'border-stone-200 bg-white');
                lblAntar.className = 'p-3.5 rounded-xl border-2 cursor-pointer transition flex items-start gap-3 ' + (metode === 'antar_sendiri' ? 'border-[#1D6B45] bg-emerald-50/40' : 'border-stone-200 bg-white');
            }
        }

        function ubahBeratForm(jenisId, delta) {
            const input = document.getElementById('berat-jenis-' + jenisId);
            if (!input || input.disabled) return;
            const skrg = parseFloat(input.value) || 0;
            const baru = Math.max(0, Math.round((skrg + delta) * 10) / 10);
            input.value = baru > 0 ? baru.toFixed(1) : 0;
            perbaruiKalkulasiManifes();
        }

        function perbaruiKalkulasiManifes() {
            const selectTps = document.getElementById('tps_id');
            const optTps = selectTps ? selectTps.options[selectTps.selectedIndex] : null;
            const izinMedisTps = optTps ? optTps.getAttribute('data-izin-medis') === '1' : false;
            const namaTps = optTps ? optTps.getAttribute('data-nama') : '-';

            const elNamaTps = document.getElementById('ringkasan-nama-tps');
            if (elNamaTps) elNamaTps.textContent = namaTps;

            // Sembunyikan & nonaktifkan seluruh opsi Limbah Medis jika TPS tidak memiliki izin limbah medis
            const barisMedis = document.querySelectorAll('[data-baris-kategori="medis"]');
            barisMedis.forEach((baris) => {
                const inputMedis = baris.querySelector('input[type="number"]');
                if (!izinMedisTps) {
                    baris.classList.add('hidden');
                    if (inputMedis) {
                        inputMedis.value = 0;
                        inputMedis.disabled = true;
                    }
                } else {
                    baris.classList.remove('hidden');
                    if (inputMedis) {
                        inputMedis.disabled = false;
                    }
                }
            });

            // Perbarui label status opsi medis & sub-total medis
            const infoOpsiMedis = document.getElementById('info-opsi-medis');
            const barisRingkasanMedis = document.getElementById('baris-ringkasan-medis');
            if (infoOpsiMedis) {
                if (izinMedisTps) {
                    infoOpsiMedis.textContent = 'Fasilitas Berizin Medis · Opsi Medis Tersedia';
                    infoOpsiMedis.className = 'px-2.5 py-1 rounded-lg text-xs font-semibold bg-[#FCE8E6] text-[#B3261E]';
                } else {
                    infoOpsiMedis.textContent = 'Tanpa Izin Medis · Hanya Limbah B3 & Non-Medis';
                    infoOpsiMedis.className = 'px-2.5 py-1 rounded-lg text-xs font-semibold bg-stone-100 text-stone-600';
                }
            }
            if (barisRingkasanMedis) {
                barisRingkasanMedis.style.display = izinMedisTps ? 'flex' : 'none';
            }

            const inputs = document.querySelectorAll('[id^="berat-jenis-"]');
            let totalBerat = 0;
            let beratMedis = 0;
            let totalPoinMentah = 0;
            let htmlList = '';

            inputs.forEach((inp) => {
                if (inp.disabled) return;
                const berat = Math.max(0, parseFloat(inp.value) || 0);
                if (berat > 0) {
                    const nama = inp.getAttribute('data-nama');
                    const kat = inp.getAttribute('data-kategori');
                    const poin = parseFloat(inp.getAttribute('data-poin')) || 0;

                    totalBerat += berat;
                    if (kat === 'medis') beratMedis += berat;
                    totalPoinMentah += (berat * poin);

                    htmlList += `
                        <div class="flex items-center justify-between text-stone-700">
                            <dt class="truncate pr-2 font-medium">${nama}</dt>
                            <dd class="font-bold shrink-0">${berat.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})} kg</dd>
                        </div>
                    `;
                }
            });

            const wadahList = document.getElementById('ringkasan-item-list');
            if (wadahList) {
                wadahList.innerHTML = htmlList || '<div class="text-stone-400 italic">Belum ada jenis limbah yang diisi beratnya.</div>';
            }

            document.getElementById('ringkasan-berat-medis').textContent = beratMedis.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' kg';
            document.getElementById('ringkasan-total-berat').textContent = totalBerat.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' kg';
            document.getElementById('ringkasan-total-poin').textContent = '+' + Math.floor(totalPoinMentah).toLocaleString('id-ID') + ' poin';
        }

        document.addEventListener('DOMContentLoaded', () => {
            ubahMetodeManifes();
            perbaruiKalkulasiManifes();
        });
    </script>

@endsection

