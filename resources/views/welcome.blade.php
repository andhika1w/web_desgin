<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengelolaan Sampah Medis &amp; Limbah B3 — Penanganan Berizin, Manifes Timbangan &amp; Poin Lingkungan</title>
    <meta name="description" content="Sistem pengelolaan sampah medis (infeksius, benda tajam, farmasi, patologis) dan limbah B3 (Bahan Berbahaya dan Beracun) terintegrasi dengan fasilitas penampungan berizin resmi.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@600;700;800&family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    @endif

    <style>
        :root {
            --aspal: #1F2421;
            --beton: #F3F5F2;
            --hijau-tua: #133A26;
            --hijau-klinis: #1D6B45;
            --kuning-hazard: #F2C230;
            --merah-medis: #B3261E;
        }
        body {
            font-family: 'Public Sans', system-ui, -apple-system, sans-serif;
            background-color: #F3F5F2;
            color: #1F2421;
        }
        h1, h2, h3, h4, .font-display {
            font-family: 'Archivo', system-ui, -apple-system, sans-serif;
            letter-spacing: -0.02em;
        }
        .angka-tabular {
            font-variant-numeric: tabular-nums;
        }
        .nota-gerigi {
            background-image: radial-gradient(circle at 8px 0, transparent 6px, #F3F5F2 6.5px);
            background-size: 16px 10px;
            background-repeat: repeat-x;
        }
        @media (prefers-reduced-motion: reduce) {
            *, ::before, ::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-[#1D6B45] selection:text-white">

    {{-- Pita Status Pengawasan Limbah Medis & B3 --}}
    <div class="bg-[#133A26] text-[#E6EFE9] text-xs border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-[#F2C230]"></span>
                <span>Pengawasan Terpadu Limbah Medis (Infeksius/Sharps/Farmasi) &amp; Bahan Berbahaya Beracun (B3)</span>
            </div>
            <div class="flex items-center gap-4 angka-tabular text-[11px] sm:text-xs text-[#C8DBD0]">
                <span><strong>{{ $ringkasan['total_tps_medis'] ?? 1 }}</strong> Fasilitas Berizin Medis</span>
                <span aria-hidden="true">·</span>
                <span><strong>{{ $ringkasan['total_jenis_khusus'] ?? 7 }}</strong> Klasifikasi Medis, B3 &amp; E-Waste</span>
                <span aria-hidden="true" class="hidden sm:inline">·</span>
                <span class="hidden sm:inline">Tertangani: <strong>{{ number_format((float) ($ringkasan['total_berat_selesai_kg'] ?? 0), 2, ',', '.') }} kg</strong></span>
            </div>
        </div>
    </div>

    {{-- Navigasi Utama --}}
    <header class="sticky top-0 z-40 bg-[#F3F5F2]/95 backdrop-blur-md border-b border-[#D6DDD7]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-[#1F2421] font-display font-bold text-lg focus:outline-none focus:ring-2 focus:ring-[#1D6B45] rounded-lg py-1">
                <span class="w-9 h-9 rounded-xl bg-[#133A26] text-[#F2C230] flex items-center justify-center shadow-xs">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/>
                    </svg>
                </span>
                <div class="leading-tight">
                    <span class="block tracking-tight">Medis &amp; Limbah B3</span>
                    <span class="block font-sans font-normal text-[11px] text-[#535E57]">Sistem Pengelolaan &amp; Manifes Berizin</span>
                </div>
            </a>

            <nav aria-label="Navigasi bagian halaman" class="hidden md:flex items-center gap-6 text-sm font-medium text-[#37413B]">
                <a href="#standar-kemasan" class="hover:text-[#1D6B45] transition-colors py-1">SOP Kemasan</a>
                <a href="#katalog-limbah" class="hover:text-[#1D6B45] transition-colors py-1">Klasifikasi Medis &amp; B3</a>
                <a href="#fasilitas-berizin" class="hover:text-[#1D6B45] transition-colors py-1">Fasilitas Berizin</a>
                <a href="#data-nasional" class="hover:text-[#1D6B45] transition-colors py-1">Data Nasional</a>
                @if($kontenEdukasi->isNotEmpty())
                    <a href="#edukasi" class="hover:text-[#1D6B45] transition-colors py-1">Protokol K3</a>
                @endif
            </nav>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('login') }}" class="inline-flex items-center px-3.5 py-2 rounded-xl text-sm font-semibold text-[#1F2421] hover:bg-[#E4EAE5] transition-colors cursor-pointer">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-[#1D6B45] hover:bg-[#155235] text-white text-sm font-semibold shadow-xs transition-colors cursor-pointer">
                    <span>Daftar Akun</span>
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        {{-- HERO UTAMA PENGELOLAAN SAMPAH MEDIS & B3 --}}
        <section class="relative overflow-hidden border-b border-[#D6DDD7] bg-gradient-to-b from-[#EBF0EC] to-[#F3F5F2] py-10 sm:py-16 lg:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="space-y-6">
                    <div class="inline-flex flex-wrap items-center gap-2 px-3 py-1.5 rounded-lg bg-[#FCE8E6] border border-[#F2B8B5] text-[#B3261E] text-xs font-bold">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                        </svg>
                        <span>Protokol Penanganan Khusus · Wajib Fasilitas Berizin Limbah Medis &amp; B3</span>
                    </div>

                    <h1 class="font-display text-3xl sm:text-4xl lg:text-[2.7rem] font-extrabold text-[#1F2421] leading-[1.12] max-w-4xl">
                        Kelola sampah medis &amp; limbah B3 secara aman, terlacak, dan tersertifikasi.
                    </h1>

                    <p class="text-base sm:text-lg text-[#454F49] leading-relaxed max-w-3xl">
                        Jangan buang jarum suntik, masker infeksius, obat kedaluwarsa, baterai litium, atau lampu merkuri ke tempat sampah biasa. Salurkan ke <strong>Fasilitas Penampungan Berizin</strong> dengan penjemputan kurir khusus atau pengantaran terjadwal, lengkap dengan manifes timbangan digital dan pelacakan status pemusnahan berizin.
                    </p>

                    {{-- Tombol Aksi Utama --}}
                    <div class="flex flex-wrap items-center gap-3 pt-1">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#1D6B45] hover:bg-[#155235] text-white font-semibold text-sm sm:text-base shadow-sm transition-colors cursor-pointer">
                            <span>Masuk ke Dashboard</span>
                            <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl bg-white hover:bg-[#E4EAE5] text-[#1F2421] border border-[#C6D1C9] font-semibold text-sm sm:text-base transition-colors cursor-pointer">
                            <span>Daftar Akun Baru</span>
                        </a>
                    </div>

                    {{-- 3 Pilar Ekosistem Pengelolaan Medis & B3 --}}
                    <div class="pt-5 border-t border-[#D6DDD7] grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white/85 rounded-xl p-4 border border-[#D6DDD7]">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#B3261E]">Penghasil / Warga</span>
                                <span class="text-[11px] font-semibold text-[#535E57]">Klinik &amp; Rumah</span>
                            </div>
                            <p class="text-xs sm:text-sm text-[#454F49] leading-relaxed">Segel limbah infeksius, benda tajam, dan farmasi terpisah. Pantau status angkut hingga selesai.</p>
                        </div>
                        <div class="bg-white/85 rounded-xl p-4 border border-[#D6DDD7]">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#133A26]">Fasilitas Berizin</span>
                                <span class="text-[11px] font-semibold text-[#535E57]">1 Akun · 1 Unit</span>
                            </div>
                            <p class="text-xs sm:text-sm text-[#454F49] leading-relaxed">Verifikasi izin limbah medis (<code class="text-[11px]">izin_limbah_medis</code>), timbang berat aktual, dan jaga kuota penyimpanan aman.</p>
                        </div>
                        <div class="bg-white/85 rounded-xl p-4 border border-[#D6DDD7]">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold uppercase tracking-wider text-[#854D0E]">Kurir Khusus B3</span>
                                <span class="text-[11px] font-semibold text-[#535E57]">Bukti Foto</span>
                            </div>
                            <p class="text-xs sm:text-sm text-[#454F49] leading-relaxed">Jemput kemasan tertutup dari titik lokasi, unggah bukti pengangkutan, dan serahkan ke fasilitas tujuan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- SEKSI STANDAR KEMASAN & KODE WARNA KESELAMATAN MEDIS & B3 --}}
        <section id="standar-kemasan" class="py-12 sm:py-14 bg-[#1F2421] text-white border-b border-[#D6DDD7]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#F2C230] block mb-1">Protokol K3 &amp; Pemilahan Sumber</span>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-white">
                            Standar Wadah &amp; Kode Warna Sebelum Penyerahan
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm text-[#B7C0BA] max-w-md">
                        Petugas kurir maupun fasilitas berizin berhak menolak setoran apabila kemasan bocor atau benda tajam tidak berada di dalam wadah tahan tusuk.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="rounded-2xl bg-[#2A302C] border border-white/10 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-lg bg-[#F2C230] text-[#1F2421] font-display font-extrabold text-xs uppercase">
                                    Kantong Kuning
                                </span>
                                <span class="text-xs text-[#C8DBD0] font-mono">Biohazard</span>
                            </div>
                            <h3 class="font-display font-bold text-base text-white mb-1.5">Medis Infeksius</h3>
                            <p class="text-xs text-[#B7C0BA] leading-relaxed">
                                Masker bekas pakai, sarung tangan medis, perban, kapas terkontaminasi darah atau cairan tubuh. Diikat rapat maksimal 3/4 kapasitas kantong.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/10 text-[11px] text-[#F2C230] font-semibold">
                            Wajib Izin Limbah Medis
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#2A302C] border border-[#F2B8B5]/30 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-lg bg-[#B3261E] text-white font-display font-extrabold text-xs uppercase">
                                    Safety Box
                                </span>
                                <span class="text-xs text-[#F2B8B5] font-mono">Tahan Tusuk</span>
                            </div>
                            <h3 class="font-display font-bold text-base text-white mb-1.5">Medis Benda Tajam (Sharps)</h3>
                            <p class="text-xs text-[#B7C0BA] leading-relaxed">
                                Jarum suntik insulin, lancet cek darah, pisau bedah, dan pecahan ampul. Dilarang mematahkan atau menutup ulang jarum (*no recapping*).
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/10 text-[11px] text-[#F2B8B5] font-semibold">
                            Wadah Keras Anti-Bocor &amp; Anti-Tusuk
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#2A302C] border border-white/10 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-lg bg-[#D97706] text-white font-display font-extrabold text-xs uppercase">
                                    Kemasan Cokelat
                                </span>
                                <span class="text-xs text-[#C8DBD0] font-mono">Farmasi / Kimia</span>
                            </div>
                            <h3 class="font-display font-bold text-base text-white mb-1.5">Medis Farmasi &amp; Obat</h3>
                            <p class="text-xs text-[#B7C0BA] leading-relaxed">
                                Obat kedaluwarsa, sisa sirup dalam botol tertutup, blister tablet rusak, dan vaksin/reagen yang sudah tidak terpakai.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/10 text-[11px] text-[#FDE68A] font-semibold">
                            Pisahkan dari Sampah Domestik
                        </div>
                    </div>

                    <div class="rounded-2xl bg-[#2A302C] border border-white/10 p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 rounded-lg bg-[#1D6B45] text-white font-display font-extrabold text-xs uppercase">
                                    Kontainer B3 &amp; E-Waste
                                </span>
                                <span class="text-xs text-[#C8DBD0] font-mono">+Poin Insentif</span>
                            </div>
                            <h3 class="font-display font-bold text-base text-white mb-1.5">Limbah B3 &amp; Elektronik</h3>
                            <p class="text-xs text-[#B7C0BA] leading-relaxed">
                                Baterai bekas, aki kecil, lampu TL/neon bermerkuri (bungkus kardus agar tidak pecah), serta perangkat elektronik rusak.
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-white/10 text-[11px] text-[#86EFAC] font-semibold">
                            Mendapat Poin Setoran per Kg
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- SEKSI KATALOG KLASIFIKASI LIMBAH MEDIS, B3 & DAUR ULANG --}}
        <section id="katalog-limbah" class="py-14 sm:py-16 border-b border-[#D6DDD7] bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#B3261E] block mb-1">Database Master Klasifikasi Limbah</span>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-[#1F2421]">
                            Daftar Jenis Limbah Medis, B3 &amp; Tarif Insentif Poin
                        </h2>
                        <p class="text-sm sm:text-base text-[#535E57] mt-1 max-w-2xl">
                            Limbah kategori <strong>Medis</strong> difokuskan pada pemusnahan berizin yang aman bagi kesehatan masyarakat (0 poin), sedangkan <strong>Limbah B3, Elektronik, dan Anorganik</strong> memberikan poin insentif per kilogram.
                        </p>
                    </div>

                    {{-- Filter Kategori (Medis & B3 di urutan utama) --}}
                    <div role="tablist" aria-label="Filter kategori limbah" class="flex flex-wrap gap-1.5">
                        @php
                            $kategoriList = [
                                'semua' => 'Semua Klasifikasi Medis (' . $jenisSampah->count() . ')',
                                'medis' => 'Limbah Medis Berizin (' . $jenisSampah->where('kategori', 'medis')->count() . ')',
                            ];
                        @endphp
                        @foreach($kategoriList as $kodeKat => $labelKat)
                            <button type="button"
                                onclick="filterKategoriSampah('{{ $kodeKat }}')"
                                data-filter-btn="{{ $kodeKat }}"
                                class="px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors cursor-pointer {{ $kodeKat === 'semua' ? 'bg-[#133A26] text-white' : 'bg-[#F3F5F2] text-[#37413B] hover:bg-[#E4EAE5]' }}">
                                {{ $labelKat }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Grid Kartu Jenis Limbah --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($jenisSampah as $jenis)
                        @php
                            $badgeMap = [
                                'medis'      => ['bg' => 'bg-[#FCE8E6]', 'text' => 'text-[#B3261E]', 'label' => 'Limbah Medis Infeksius/Khusus'],
                                'b3'         => ['bg' => 'bg-[#FEF3C7]', 'text' => 'text-[#854D0E]', 'label' => 'Bahan Berbahaya & Beracun (B3)'],
                                'elektronik' => ['bg' => 'bg-[#E8F1FA]', 'text' => 'text-[#164C78]', 'label' => 'Limbah Elektronik (E-Waste)'],
                                'anorganik'  => ['bg' => 'bg-[#E6F4EC]', 'text' => 'text-[#135433]', 'label' => 'Daur Ulang Anorganik'],
                                'organik'    => ['bg' => 'bg-[#F1F5E6]', 'text' => 'text-[#3F5E14]', 'label' => 'Organik Terpilah'],
                            ];
                            $katInfo = $badgeMap[$jenis->kategori] ?? $badgeMap['anorganik'];
                        @endphp
                        <article
                            data-kategori-card="{{ $jenis->kategori }}"
                            class="rounded-2xl border {{ $jenis->kategori === 'medis' ? 'border-[#F2B8B5] bg-[#FFFBFB]' : ($jenis->kategori === 'b3' ? 'border-[#FDE68A] bg-[#FFFDF5]' : 'border-[#D6DDD7] bg-[#F9FAF9]') }} p-5 flex flex-col justify-between gap-4 transition-all hover:border-[#1D6B45]">
                            <div>
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $katInfo['bg'] }} {{ $katInfo['text'] }}">
                                        {{ $katInfo['label'] }}
                                    </span>
                                    <div class="text-right angka-tabular shrink-0">
                                        @if($jenis->poin_per_kg > 0)
                                            <span class="inline-flex items-baseline gap-1 px-2.5 py-1 rounded-lg bg-[#133A26] text-white font-display font-bold text-sm">
                                                +{{ $jenis->poin_per_kg }} <span class="text-[11px] font-normal text-[#C8DBD0]">poin/kg</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-[#B3261E] text-white font-semibold text-[11px]">
                                                Pemusnahan Aman
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <h3 class="font-display font-bold text-base text-[#1F2421] mb-1">
                                    {{ $jenis->nama }}
                                </h3>
                                <p class="text-xs sm:text-sm text-[#535E57] leading-relaxed">
                                    {{ $jenis->deskripsi ?: 'Dikemas terpisah sesuai protokol penanganan limbah.' }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-[#E2E8E4] flex items-center justify-between gap-2 text-xs">
                                @if($jenis->kategori === 'medis')
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-[#B3261E]">
                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Wajib Fasilitas Berizin Limbah Medis</span>
                                    </span>
                                @elseif($jenis->perlu_penanganan_khusus)
                                    <span class="inline-flex items-center gap-1.5 font-semibold text-[#854D0E]">
                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Penanganan Khusus B3 (Kemasan Kedap)</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[#1D6B45] font-medium">
                                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>Diterima di Seluruh Fasilitas Terverifikasi</span>
                                    </span>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- SEKSI PANTAUAN FASILITAS PENAMPUNGAN BERIZIN LIMBAH MEDIS & B3 --}}
        <section id="fasilitas-berizin" class="py-14 sm:py-16 border-b border-[#D6DDD7] bg-[#F3F5F2]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-[#1D6B45] block mb-1">Rekomendasi &amp; Kuota Penyimpanan</span>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-[#1F2421]">
                            Fasilitas Penampungan Berizin Limbah Medis &amp; B3
                        </h2>
                        <p class="text-sm sm:text-base text-[#535E57] mt-1 max-w-2xl">
                            Prosedur <code class="text-xs bg-white px-1.5 py-0.5 rounded border border-[#D6DDD7]">sp_rekomendasi_tps</code> memprioritaskan fasilitas terdekat yang memiliki izin limbah medis aktif dan ruang penyimpanan yang mencukupi.
                        </p>
                    </div>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-[#1D6B45] hover:text-[#133A26] cursor-pointer">
                        <span>Pengelola fasilitas berizin? Daftarkan unit Anda</span>
                        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.638L10.23 5.29a.75.75 0 1 1 1.04-1.08l5.5 5.25a.75.75 0 0 1 0 1.08l-5.5 5.25a.75.75 0 1 1-1.04-1.08l4.158-3.96H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    @forelse($daftarTps as $tps)
                        @php
                            $totalKg = max(1, (float) $tps->kapasitas_total_kg);
                            $terpakaiKg = (float) $tps->kapasitas_terpakai_kg;
                            $sisaKg = max(0, $totalKg - $terpakaiKg);
                            $persen = min(100, round(($terpakaiKg / $totalKg) * 100, 1));
                            $warnaBar = $persen >= 90 ? 'bg-[#B3261E]' : ($persen >= 75 ? 'bg-[#D97706]' : 'bg-[#1D6B45]');
                        @endphp
                        <div class="bg-white rounded-2xl border {{ $tps->izin_limbah_medis ? 'border-[#1D6B45]' : 'border-[#D6DDD7]' }} p-6 shadow-2xs flex flex-col justify-between gap-5">
                            <div>
                                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($tps->status === 'buka')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#E6F4EC] text-[#135433] text-xs font-bold uppercase">
                                                <span class="w-2 h-2 rounded-full bg-[#1D6B45]"></span>
                                                Menerima Manifes
                                            </span>
                                        @elseif($tps->status === 'penuh')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#FCE8E6] text-[#B3261E] text-xs font-bold uppercase">
                                                Kuota Penuh
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#E4EAE5] text-[#454F49] text-xs font-bold uppercase">
                                                Tutup
                                            </span>
                                        @endif

                                        @if($tps->izin_limbah_medis)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#FCE8E6] text-[#B3261E] border border-[#F2B8B5] text-xs font-bold">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd"/>
                                                </svg>
                                                <span>Berizin Resmi Limbah Medis &amp; B3</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#F3F5F2] text-[#535E57] text-xs font-medium">
                                                Non-Medis (B3 Rumah Tangga &amp; Daur Ulang)
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-[#535E57] font-mono">
                                        No. Izin: {{ $tps->no_izin ?: 'TERVERIFIKASI' }}
                                    </span>
                                </div>

                                <h3 class="font-display text-xl font-bold text-[#1F2421]">
                                    {{ $tps->nama_tps }}
                                </h3>
                                <p class="text-sm text-[#535E57] mt-1">
                                    {{ $tps->alamat }}
                                </p>
                            </div>

                            {{-- Indikator Daya Tampung Penyimpanan --}}
                            <div class="bg-[#F9FAF9] rounded-xl p-4 border border-[#E2E8E4] space-y-2.5">
                                <div class="flex items-center justify-between text-xs sm:text-sm angka-tabular">
                                    <span class="font-semibold text-[#37413B]">Terisi: {{ number_format($terpakaiKg, 2, ',', '.') }} kg ({{ $persen }}%)</span>
                                    <span class="font-bold text-[#1D6B45]">Kapasitas Tersedia: {{ number_format($sisaKg, 2, ',', '.') }} kg</span>
                                </div>
                                <div class="w-full h-2.5 bg-[#E2E8E4] rounded-full overflow-hidden" role="progressbar" aria-valuenow="{{ $persen }}" aria-valuemin="0" aria-valuemax="100" aria-label="Kapasitas {{ $tps->nama_tps }}">
                                    <div class="h-full {{ $warnaBar }} rounded-full transition-all" style="width: {{ max(3, $persen) }}%"></div>
                                </div>
                                <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-xs text-[#535E57] angka-tabular">
                                    <span>Jam Penerimaan: <strong>{{ substr((string) $tps->jam_buka, 0, 5) }} – {{ substr((string) $tps->jam_tutup, 0, 5) }} WIB</strong></span>
                                    <span>Total Kuota Aman: <strong>{{ number_format($totalKg, 0, ',', '.') }} kg</strong></span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 bg-white rounded-2xl border border-[#D6DDD7] p-8 text-center text-sm text-[#535E57]">
                            Belum ada fasilitas berizin yang terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- SEKSI DATA NASIONAL PENGELOLAAN LIMBAH MEDIS (EDUKASI) --}}
        {{-- Angka bisa diganti dari controller: kirim variabel $dataNasional dengan kunci yang sama seperti default di bawah. --}}
        @php
            $dn = $dataNasional ?? [
                'tahun'              => 2025,
                'total_fasyankes'    => 146751,
                'lapor_speed'        => 10958,
                'belum_lapor'        => 135793,
                'potensi_ton'        => 190656.61,
                'tercatat_ton'       => 60457.45,
                'belum_terlapor_ton' => 130199.16,
            ];

            $pctLapor      = round($dn['lapor_speed'] / $dn['total_fasyankes'] * 100, 2);
            $pctBelumLapor = round($dn['belum_lapor'] / $dn['total_fasyankes'] * 100, 2);
            $pctTercatat   = round($dn['tercatat_ton'] / $dn['potensi_ton'] * 100, 2);
            $pctBelumTon   = round($dn['belum_terlapor_ton'] / $dn['potensi_ton'] * 100, 2);

            $fmt   = fn ($n, $d = 0) => number_format((float) $n, $d, ',', '.');
            $lebar = fn ($n) => number_format((float) $n, 2, '.', '');

            // Komposisi limbah fasyankes menurut pengelompokan Kemenkes (data 2017)
            $komposisi = [
                ['label' => 'Limbah domestik (non-medis)', 'persen' => 80, 'warna' => 'bg-[#8A968E]', 'hex' => '#8A968E'],
                ['label' => 'Infeksius & patologis',       'persen' => 15, 'warna' => 'bg-[#B3261E]', 'hex' => '#B3261E'],
                ['label' => 'Kimia & farmasi',             'persen' => 3,  'warna' => 'bg-[#D97706]', 'hex' => '#D97706'],
                ['label' => 'Termometer & tabung',         'persen' => 1,  'warna' => 'bg-[#0284C7]', 'hex' => '#0284C7'],
                ['label' => 'Benda tajam',                 'persen' => 1,  'warna' => 'bg-[#7A1712]', 'hex' => '#7A1712'],
            ];

            // Kapasitas pengolahan vs tidak terkelola (Kemenkes / UGM 2019)
            $kapasitasOlahan = [
                ['label' => '10 jasa pengolah berizin',      'ton' => 170, 'persen' => round(170 / 304 * 100, 1), 'hex' => '#1D6B45', 'warna' => 'bg-[#1D6B45]'],
                ['label' => '87 RS dengan insinerator sendiri', 'ton' => 60,  'persen' => round(60 / 304 * 100, 1),  'hex' => '#D97706', 'warna' => 'bg-[#D97706]'],
                ['label' => 'Tidak terkelola',               'ton' => 74,  'persen' => round(74 / 304 * 100, 1),  'hex' => '#B3261E', 'warna' => 'bg-[#B3261E]'],
            ];

            // Helper pembuat irisan (slice) SVG Pie Chart
            $buatIrisanPie = function (array $items, int $cx = 100, int $cy = 100, int $r = 92): array {
                $total = array_sum(array_column($items, 'persen')) ?: 100;
                $sudutAwal = -90.0;
                $hasil = [];

                foreach ($items as $item) {
                    $p = (float) ($item['persen'] ?? 0);
                    $sudutSlice = ($p / $total) * 360.0;
                    $sudutAkhir = $sudutAwal + $sudutSlice;

                    $rad1 = deg2rad($sudutAwal);
                    $rad2 = deg2rad($sudutAkhir);

                    $x1 = round($cx + $r * cos($rad1), 3);
                    $y1 = round($cy + $r * sin($rad1), 3);
                    $x2 = round($cx + $r * cos($rad2), 3);
                    $y2 = round($cy + $r * sin($rad2), 3);

                    $largeArc = $sudutSlice > 180.0 ? 1 : 0;

                    $hasil[] = $item + [
                        'path' => "M {$cx} {$cy} L {$x1} {$y1} A {$r} {$r} 0 {$largeArc} 1 {$x2} {$y2} Z",
                    ];

                    $sudutAwal = $sudutAkhir;
                }

                return $hasil;
            };

            $pieFasyankes = $buatIrisanPie([
                ['label' => 'Sudah melapor (' . $fmt($pctLapor, 2) . '%)', 'persen' => $pctLapor, 'hex' => '#F2C230'],
                ['label' => 'Belum melapor (' . $fmt($pctBelumLapor, 2) . '%)', 'persen' => $pctBelumLapor, 'hex' => '#B3261E'],
            ]);

            $pieTonase = $buatIrisanPie([
                ['label' => 'Tercatat di SPEED (' . $fmt($pctTercatat, 2) . '%)', 'persen' => $pctTercatat, 'hex' => '#F2C230'],
                ['label' => 'Belum terlapor / belum jelas (' . $fmt($pctBelumTon, 2) . '%)', 'persen' => $pctBelumTon, 'hex' => '#B3261E'],
            ]);

            $pieKapasitas = $buatIrisanPie($kapasitasOlahan);
            $pieKomposisi = $buatIrisanPie($komposisi);
        @endphp

        <section id="data-nasional" class="py-14 sm:py-16 border-b border-[#D6DDD7] bg-[#EBF0EC]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="mb-8 max-w-3xl">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#B3261E] block mb-1">Data Nasional · Edukasi</span>
                    <h2 class="font-display text-2xl sm:text-3xl font-bold text-[#1F2421]">
                        Sebagian besar limbah medis Indonesia belum terpantau
                    </h2>
                    <p class="text-sm sm:text-base text-[#454F49] mt-2 leading-relaxed">
                        Pemerintah mewajibkan fasilitas pelayanan kesehatan (fasyankes) melaporkan pengelolaan limbah B3 medis secara digital. Data tahun {{ $dn['tahun'] }} dari KLH/BPLH menunjukkan jarak yang besar antara potensi limbah yang dihasilkan dan yang benar-benar tercatat.
                    </p>
                </div>

                {{-- Data utama tahun 2025: pelaporan fasyankes & tonase --}}
                <div class="bg-[#133A26] text-white rounded-2xl p-5 sm:p-7 border border-[#1D6B45]/40 shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-6">
                        <h3 class="font-display text-lg font-bold">Pelaporan limbah B3 medis lewat SPEED, {{ $dn['tahun'] }}</h3>
                        <span class="px-2.5 py-1 rounded-md bg-[#F2C230] text-[#1F2421] font-display font-bold text-xs">KLH/BPLH · Agustus 2026</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-10">
                        {{-- Blok 1: jumlah fasyankes (Pie Chart) --}}
                        <div class="bg-[#0E2B1C]/60 rounded-xl p-4 sm:p-5 border border-white/10 flex flex-col sm:flex-row items-center gap-5">
                            <div class="relative w-32 h-32 sm:w-36 sm:h-36 shrink-0">
                                <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-sm" role="img"
                                     aria-label="Grafik Pie: {{ $fmt($pctLapor, 2) }} persen fasyankes sudah melapor, {{ $fmt($pctBelumLapor, 2) }} persen belum melapor">
                                    @foreach($pieFasyankes as $irisan)
                                        <path d="{{ $irisan['path'] }}"
                                              fill="{{ $irisan['hex'] }}"
                                              stroke="#133A26"
                                              stroke-width="3"
                                              stroke-linejoin="round"
                                              class="transition-opacity hover:opacity-90">
                                            <title>{{ $irisan['label'] }}</title>
                                        </path>
                                    @endforeach
                                </svg>
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <p class="text-xs sm:text-sm text-[#C8DBD0] mb-0.5">Fasyankes yang sudah melapor</p>
                                <p class="font-display text-2xl sm:text-3xl font-extrabold angka-tabular">
                                    {{ $fmt($dn['lapor_speed']) }}
                                    <span class="text-sm font-semibold text-[#C8DBD0] block sm:inline">dari {{ $fmt($dn['total_fasyankes']) }} unit</span>
                                </p>

                                <dl class="mt-3.5 space-y-2 text-xs angka-tabular border-t border-white/10 pt-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <dt class="flex items-center gap-2 text-[#C8DBD0]">
                                            <span class="w-3 h-3 rounded-sm bg-[#F2C230] shrink-0"></span>
                                            <span>Sudah melapor</span>
                                        </dt>
                                        <dd class="font-bold text-sm text-white">{{ $fmt($pctLapor, 2) }}%</dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <dt class="flex items-center gap-2 text-[#C8DBD0]">
                                            <span class="w-3 h-3 rounded-sm bg-[#B3261E] shrink-0"></span>
                                            <span>Belum melapor</span>
                                        </dt>
                                        <dd class="font-bold text-sm text-white">{{ $fmt($pctBelumLapor, 2) }}% <span class="text-xs font-normal text-[#C8DBD0]">({{ $fmt($dn['belum_lapor']) }})</span></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        {{-- Blok 2: tonase (Pie Chart) --}}
                        <div class="bg-[#0E2B1C]/60 rounded-xl p-4 sm:p-5 border border-white/10 flex flex-col sm:flex-row items-center gap-5">
                            <div class="relative w-32 h-32 sm:w-36 sm:h-36 shrink-0">
                                <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-sm" role="img"
                                     aria-label="Grafik Pie: {{ $fmt($pctTercatat, 2) }} persen tonase tercatat di SPEED, {{ $fmt($pctBelumTon, 2) }} persen belum terlapor">
                                    @foreach($pieTonase as $irisan)
                                        <path d="{{ $irisan['path'] }}"
                                              fill="{{ $irisan['hex'] }}"
                                              stroke="#133A26"
                                              stroke-width="3"
                                              stroke-linejoin="round"
                                              class="transition-opacity hover:opacity-90">
                                            <title>{{ $irisan['label'] }}</title>
                                        </path>
                                    @endforeach
                                </svg>
                            </div>

                            <div class="flex-1 text-center sm:text-left">
                                <p class="text-xs sm:text-sm text-[#C8DBD0] mb-0.5">Potensi timbulan limbah B3 medis</p>
                                <p class="font-display text-2xl sm:text-3xl font-extrabold angka-tabular">
                                    {{ $fmt($dn['potensi_ton'], 2) }}
                                    <span class="text-sm font-semibold text-[#C8DBD0]">ton</span>
                                </p>

                                <dl class="mt-3.5 space-y-2 text-xs angka-tabular border-t border-white/10 pt-3">
                                    <div class="flex items-center justify-between gap-2">
                                        <dt class="flex items-center gap-2 text-[#C8DBD0]">
                                            <span class="w-3 h-3 rounded-sm bg-[#F2C230] shrink-0"></span>
                                            <span>Tercatat di SPEED</span>
                                        </dt>
                                        <dd class="font-bold text-sm text-white">{{ $fmt($pctTercatat, 2) }}% <span class="text-xs font-normal text-[#C8DBD0]">({{ $fmt($dn['tercatat_ton'], 2) }} t)</span></dd>
                                    </div>
                                    <div class="flex items-center justify-between gap-2">
                                        <dt class="flex items-center gap-2 text-[#C8DBD0]">
                                            <span class="w-3 h-3 rounded-sm bg-[#B3261E] shrink-0"></span>
                                            <span>Belum terlapor</span>
                                        </dt>
                                        <dd class="font-bold text-sm text-white">{{ $fmt($pctBelumTon, 2) }}% <span class="text-xs font-normal text-[#C8DBD0]">({{ $fmt($dn['belum_terlapor_ton'], 2) }} t)</span></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <p class="mt-6 pt-4 border-t border-white/10 text-xs text-[#C8DBD0] leading-relaxed max-w-3xl">
                        SPEED adalah sistem pelaporan dan evaluasi digital pengelolaan limbah B3 milik KLH/BPLH. Limbah yang belum terlapor belum tentu dibuang sembarangan, tetapi tidak dapat dipastikan bahwa limbah itu diangkut dan dimusnahkan oleh pihak berizin.
                    </p>
                </div>

                {{-- Konteks sebelumnya --}}
                <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <article class="bg-white rounded-2xl border border-[#D6DDD7] p-5 flex flex-col gap-3">
                        <h3 class="font-display font-bold text-base text-[#1F2421]">Kapasitas pengolahan belum menutup timbulan</h3>
                        <p class="text-sm text-[#454F49] leading-relaxed">
                            Paparan Kemenkes tahun 2019 memperkirakan limbah medis dari sekitar 2.820 rumah sakit dan 9.884 puskesmas mencapai kurang lebih <strong class="angka-tabular">290 ton per hari</strong>, belum termasuk klinik, unit transfusi darah, dan apotek.
                        </p>

                        <div class="pt-3 border-t border-[#E2E8E4] flex flex-col sm:flex-row items-center gap-4">
                            <div class="w-28 h-28 shrink-0">
                                <svg viewBox="0 0 200 200" class="w-full h-full" role="img" aria-label="Grafik Pie kapasitas pengolahan limbah medis per hari">
                                    @foreach($pieKapasitas as $irisan)
                                        <path d="{{ $irisan['path'] }}"
                                              fill="{{ $irisan['hex'] }}"
                                              stroke="#FFFFFF"
                                              stroke-width="2.5"
                                              stroke-linejoin="round"
                                              class="transition-opacity hover:opacity-90">
                                            <title>{{ $irisan['label'] }}: ±{{ $irisan['ton'] }} ton/hari ({{ $irisan['persen'] }}%)</title>
                                        </path>
                                    @endforeach
                                </svg>
                            </div>

                            <dl class="w-full text-xs sm:text-sm angka-tabular space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <dt class="flex items-center gap-2 text-[#535E57]">
                                        <span class="w-2.5 h-2.5 rounded-sm bg-[#1D6B45] shrink-0"></span>
                                        <span>10 jasa pengolah berizin</span>
                                    </dt>
                                    <dd class="font-semibold text-[#1F2421] shrink-0">±170 ton/hari</dd>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <dt class="flex items-center gap-2 text-[#535E57]">
                                        <span class="w-2.5 h-2.5 rounded-sm bg-[#D97706] shrink-0"></span>
                                        <span>87 RS berinsinerator</span>
                                    </dt>
                                    <dd class="font-semibold text-[#1F2421] shrink-0">±60 ton/hari</dd>
                                </div>
                                <div class="flex items-center justify-between gap-2 text-[#B3261E]">
                                    <dt class="flex items-center gap-2 font-semibold">
                                        <span class="w-2.5 h-2.5 rounded-sm bg-[#B3261E] shrink-0"></span>
                                        <span>Tidak terkelola</span>
                                    </dt>
                                    <dd class="font-bold shrink-0">±74 ton/hari</dd>
                                </div>
                            </dl>
                        </div>

                        <p class="text-xs text-[#707C74] mt-auto">Sumber: Humas UGM (2019), paparan Kemenkes.</p>
                    </article>

                    <article class="bg-white rounded-2xl border border-[#D6DDD7] p-5 flex flex-col gap-3">
                        <h3 class="font-display font-bold text-base text-[#1F2421]">Pandemi menambah beban</h3>
                        <p class="text-sm text-[#454F49] leading-relaxed">
                            KLHK mencatat rekapitulasi limbah medis selama pandemi Covid-19 sebesar <strong class="angka-tabular">7.502,79 ton</strong> sejak awal pandemi hingga 9 Februari 2021, berdasarkan laporan yang masuk.
                        </p>
                        <p class="text-sm text-[#454F49] leading-relaxed">
                            Pada waktu yang sama, <strong class="angka-tabular">124 rumah sakit</strong> tercatat berizin memusnahkan limbah medis: 119 dengan insinerator dan 5 dengan autoklaf. Selama masa darurat, insinerator yang belum berizin sempat diizinkan beroperasi dengan syarat suhu minimal 800 °C dan pengawasan KLHK.
                        </p>
                        <p class="text-xs text-[#707C74] mt-auto">Sumber: KLHK, dikutip Republika (16 Februari 2021); Kementerian LHK, dikutip Pajak.com.</p>
                    </article>

                    <article class="bg-white rounded-2xl border border-[#D6DDD7] p-5 flex flex-col gap-3">
                        <h3 class="font-display font-bold text-base text-[#1F2421]">Hanya sebagian kecil limbah fasyankes yang berbahaya</h3>
                        <p class="text-sm text-[#454F49] leading-relaxed">
                            Menurut pengelompokan Kemenkes (data 2017), sebagian besar sampah fasyankes sebenarnya limbah domestik. Pemilahan di sumber menjaga limbah infeksius tidak bercampur dengan sampah biasa.
                        </p>

                        <div class="pt-3 border-t border-[#E2E8E4] flex flex-col sm:flex-row items-center gap-4">
                            <div class="w-28 h-28 shrink-0">
                                <svg viewBox="0 0 200 200" class="w-full h-full" role="img" aria-label="Grafik Pie komposisi limbah fasyankes">
                                    @foreach($pieKomposisi as $irisan)
                                        <path d="{{ $irisan['path'] }}"
                                              fill="{{ $irisan['hex'] }}"
                                              stroke="#FFFFFF"
                                              stroke-width="2"
                                              stroke-linejoin="round"
                                              class="transition-opacity hover:opacity-90">
                                            <title>{{ $irisan['label'] }}: {{ $irisan['persen'] }}%</title>
                                        </path>
                                    @endforeach
                                </svg>
                            </div>

                            <ul class="w-full space-y-1.5 angka-tabular text-xs" aria-label="Komposisi limbah fasyankes">
                                @foreach($komposisi as $k)
                                    <li class="flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-2 text-[#37413B]">
                                            <span class="w-2.5 h-2.5 rounded-sm {{ $k['warna'] }} shrink-0"></span>
                                            <span>{{ $k['label'] }}</span>
                                        </span>
                                        <span class="font-bold text-[#1F2421] shrink-0">{{ $k['persen'] }}%</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <p class="text-xs text-[#707C74] mt-auto">Sumber: Kemenkes (2017), dikutip Mongabay Indonesia (2020).</p>
                    </article>
                </div>

                {{-- Kewajiban & tindakan --}}
                <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="bg-white rounded-2xl border border-[#D6DDD7] p-5 sm:p-6">
                        <h3 class="font-display font-bold text-base text-[#1F2421] mb-3">Dasar aturan yang perlu diketahui</h3>
                        <ul class="space-y-3 text-sm text-[#454F49] leading-relaxed">
                            <li class="pl-3 border-l-4 border-[#1D6B45]"><strong>PP Nomor 22 Tahun 2021</strong> tentang Penyelenggaraan Perlindungan dan Pengelolaan Lingkungan Hidup menjadi dasar kewajiban pengelolaan limbah B3 dari fasyankes.</li>
                            <li class="pl-3 border-l-4 border-[#1D6B45]">Pelaporan dilakukan melalui <strong>SPEED</strong>, sistem digital KLH/BPLH untuk memantau dan mengevaluasi pengelolaan limbah B3.</li>
                            <li class="pl-3 border-l-4 border-[#1D6B45]">Pengawasan lingkungan hidup mengacu pada <strong>PermenLH Nomor 6 Tahun 2026</strong>.</li>
                        </ul>
                    </div>

                    <div class="bg-[#1F2421] text-white rounded-2xl p-5 sm:p-6">
                        <h3 class="font-display font-bold text-base mb-3">Apa artinya bagi penghasil limbah medis</h3>
                        <ul class="space-y-3 text-sm text-[#D5DBD7] leading-relaxed">
                            <li class="pl-3 border-l-4 border-[#F2C230]">Pisahkan limbah infeksius, benda tajam, dan farmasi dari sampah domestik sejak dari sumbernya.</li>
                            <li class="pl-3 border-l-4 border-[#F2C230]">Serahkan hanya ke fasilitas berizin. Manifes timbangan di platform ini membuat setiap kilogram limbah punya jejak yang bisa ditelusuri.</li>
                            <li class="pl-3 border-l-4 border-[#F2C230]">Fasyankes: pastikan unit Anda sudah melapor di SPEED agar limbah Anda ikut terhitung dalam data nasional.</li>
                        </ul>
                    </div>
                </div>

                {{-- Catatan metodologi & sumber --}}
                <div class="mt-8 text-xs text-[#535E57] leading-relaxed max-w-4xl">
                    <p class="mb-2">
                        <strong>Catatan:</strong> angka antar-tahun berasal dari sumber, cakupan, dan satuan yang berbeda (ton per hari dan ton per tahun), sehingga tidak dapat dibandingkan langsung. Data 2025 adalah potensi timbulan dan laporan yang masuk ke SPEED, bukan hasil pengukuran seluruh fasyankes.
                    </p>
                    <p class="flex flex-wrap gap-x-4 gap-y-1">
                        <span class="font-semibold">Sumber:</span>
                        <a class="underline hover:text-[#1D6B45]" href="https://www.kemenlh.go.id/news/detail/tingkatkan-kepatuhan-klhbplh-dorong-fasilitas-pelayanan-kesehatan-optimalisasi-pelaporan-limbah-b3" target="_blank" rel="noopener noreferrer">KLH/BPLH, 13 Agustus 2026</a>
                        <a class="underline hover:text-[#1D6B45]" href="https://ugm.ac.id/?p=9575" target="_blank" rel="noopener noreferrer">UGM, 2019</a>
                        <a class="underline hover:text-[#1D6B45]" href="https://news.republika.co.id/berita/qom2oi409/ribuan-ton-limbah-medis-dan-saran-daur-ulang-dari-lipi" target="_blank" rel="noopener noreferrer">Republika, 16 Februari 2021</a>
                        <a class="underline hover:text-[#1D6B45]" href="https://mongabay.co.id/2020/10/05/dampak-limbah-medis-saat-pandemi/" target="_blank" rel="noopener noreferrer">Mongabay Indonesia, 5 Oktober 2020</a>
                    </p>
                </div>
            </div>
        </section>

        {{-- SEKSI EDUKASI & PROTOKOL KESELAMATAN (DARI TABEL konten) --}}
        @if($kontenEdukasi->isNotEmpty())
            <section id="edukasi" class="py-14 sm:py-16 bg-white border-b border-[#D6DDD7]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="mb-8">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#B3261E] block mb-1">Protokol K3 &amp; Sosialisasi</span>
                        <h2 class="font-display text-2xl sm:text-3xl font-bold text-[#1F2421]">
                            Panduan Penanganan Limbah Medis &amp; B3
                        </h2>
                        <p class="text-xs sm:text-sm text-[#535E57] mt-1">
                            Klik pada kartu panduan atau pengumuman di bawah ini untuk membaca isi lengkap protokolnya.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($kontenEdukasi as $artikel)
                            @php
                                $tglTerbit = $artikel->published_at
                                    ? \Carbon\Carbon::parse($artikel->published_at)->translatedFormat('d M Y')
                                    : '';
                                $isiLengkap = $artikel->isi ?? $artikel->ringkasan ?? '';
                            @endphp
                            <article
                                role="button"
                                tabindex="0"
                                onclick="bukaModalEdukasi({{ $loop->index }})"
                                onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();bukaModalEdukasi({{ $loop->index }});}"
                                data-edukasi-idx="{{ $loop->index }}"
                                data-judul="{{ $artikel->judul }}"
                                data-jenis="{{ $artikel->jenis }}"
                                data-tanggal="{{ $tglTerbit }}"
                                data-ringkasan="{{ $artikel->ringkasan }}"
                                class="group bg-[#F9FAF9] hover:bg-white rounded-2xl border border-[#D6DDD7] hover:border-[#1D6B45] p-5 flex flex-col justify-between gap-4 cursor-pointer transition-all shadow-2xs hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#1D6B45]">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-3">
                                        <span class="px-2.5 py-1 rounded-lg bg-[#FCE8E6] text-[#B3261E] text-xs font-semibold uppercase">
                                            {{ $artikel->jenis }}
                                        </span>
                                        @if($tglTerbit)
                                            <time class="text-xs text-[#535E57] angka-tabular">
                                                {{ $tglTerbit }}
                                            </time>
                                        @endif
                                    </div>
                                    <h3 class="font-display font-bold text-base text-[#1F2421] group-hover:text-[#1D6B45] transition-colors mb-2">
                                        {{ $artikel->judul }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[#535E57] leading-relaxed line-clamp-3">
                                        {{ $artikel->ringkasan ?: \Illuminate\Support\Str::limit($isiLengkap, 140) }}
                                    </p>
                                </div>

                                <div class="pt-3 border-t border-[#E2E8E4] flex items-center justify-between text-xs font-semibold text-[#1D6B45]">
                                    <span>Baca isi panduan lengkap</span>
                                    <span class="transition-transform group-hover:translate-x-1" aria-hidden="true">→</span>
                                </div>

                                <div id="isi-edukasi-{{ $loop->index }}" class="hidden">{{ $isiLengkap }}</div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- MODAL POPUP ISI PANDUAN / EDUKASI --}}
            <div id="modal-edukasi"
                 role="dialog"
                 aria-modal="true"
                 aria-labelledby="modal-edukasi-judul"
                 onclick="if(event.target === this) tutupModalEdukasi()"
                 class="fixed inset-0 z-50 hidden items-center justify-center p-4 sm:p-6 bg-black/60 backdrop-blur-xs">
                <div class="bg-white rounded-2xl border border-[#D6DDD7] shadow-2xl max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden">
                    {{-- Header Modal --}}
                    <div class="px-5 sm:px-6 py-4 bg-[#133A26] text-white flex items-start justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2.5">
                                <span id="modal-edukasi-jenis" class="px-2.5 py-0.5 rounded bg-[#F2C230] text-[#1F2421] font-display font-bold text-[11px] uppercase">
                                    EDUKASI
                                </span>
                                <time id="modal-edukasi-tanggal" class="text-xs text-[#C8DBD0] angka-tabular"></time>
                            </div>
                            <h3 id="modal-edukasi-judul" class="font-display text-lg sm:text-xl font-bold text-white leading-snug">
                                Judul Panduan
                            </h3>
                        </div>
                        <button type="button"
                                onclick="tutupModalEdukasi()"
                                aria-label="Tutup popup panduan"
                                class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center font-bold text-lg shrink-0 cursor-pointer transition-colors">
                            ✕
                        </button>
                    </div>

                    {{-- Isi Lengkap Modal --}}
                    <div class="p-5 sm:p-6 overflow-y-auto space-y-4">
                        <div id="modal-edukasi-ringkasan-box" class="p-3.5 rounded-xl bg-[#F3F5F2] border-l-4 border-[#1D6B45] text-xs sm:text-sm font-medium text-[#37413B] leading-relaxed hidden">
                            <p id="modal-edukasi-ringkasan"></p>
                        </div>

                        <div id="modal-edukasi-isi" class="text-sm sm:text-base text-[#1F2421] leading-relaxed whitespace-pre-line"></div>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="px-5 sm:px-6 py-3.5 bg-[#F9FAF9] border-t border-[#E2E8E4] flex items-center justify-end">
                        <button type="button"
                                onclick="tutupModalEdukasi()"
                                class="px-5 py-2 rounded-xl bg-[#1D6B45] hover:bg-[#155235] text-white font-semibold text-xs sm:text-sm cursor-pointer transition-colors">
                            Tutup Panduan
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- SEKSI AJAKAN PENDAFTARAN --}}
        <section class="py-14 sm:py-16 bg-[#133A26] text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-7 space-y-3">
                        <span class="inline-block px-3 py-1 rounded-lg bg-[#F2C230] text-[#1F2421] font-display font-bold text-xs uppercase tracking-wider">
                            Kepatuhan &amp; Keselamatan Lingkungan
                        </span>
                        <h2 class="font-display text-2xl sm:text-3xl font-extrabold text-white">
                            Salurkan limbah medis &amp; B3 ke jalur resmi sekarang.
                        </h2>
                        <p class="text-sm sm:text-base text-[#C8DBD0] max-w-2xl leading-relaxed">
                            Daftar sebagai <strong>Penghasil / Warga</strong> untuk membuat manifes setoran, <strong>Pengelola Fasilitas</strong> (1 akun mengelola 1 fasilitas berizin), atau <strong>Kurir Penjemputan</strong>.
                        </p>
                    </div>
                    <div class="lg:col-span-5 flex flex-wrap lg:justify-end gap-3">
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-[#F2C230] hover:bg-[#E0B020] text-[#1F2421] font-display font-bold text-sm sm:text-base transition-colors cursor-pointer">
                            <span>Daftar Akun Baru</span>
                        </a>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-3.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold text-sm sm:text-base transition-colors cursor-pointer">
                            <span>Masuk ke Akun</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-[#1F2421] text-[#B7C0BA] py-10 text-xs sm:text-sm border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="w-7 h-7 rounded-lg bg-[#B3261E] text-white flex items-center justify-center font-display font-bold text-xs">
                    B3
                </span>
                <span class="font-display font-bold text-white">Pengelolaan Sampah Medis &amp; Limbah B3</span>
                <span>· Manifes Timbangan &amp; Fasilitas Berizin</span>
            </div>
            <div class="flex flex-wrap items-center gap-5">
                <a href="#standar-kemasan" class="hover:text-white transition-colors">SOP Kemasan</a>
                <a href="#katalog-limbah" class="hover:text-white transition-colors">Klasifikasi Medis &amp; B3</a>
                <a href="#data-nasional" class="hover:text-white transition-colors">Data Nasional</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a>
                <a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a>
            </div>
        </div>
    </footer>

    {{-- Script Filter Kategori Limbah & Popup Modal Edukasi --}}
    <script>
        function filterKategoriSampah(kategori) {
            const cards = document.querySelectorAll('[data-kategori-card]');
            const buttons = document.querySelectorAll('[data-filter-btn]');

            buttons.forEach((btn) => {
                const aktif = btn.getAttribute('data-filter-btn') === kategori;
                btn.className = aktif
                    ? 'px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors cursor-pointer bg-[#133A26] text-white'
                    : 'px-3.5 py-2 rounded-xl text-xs font-semibold transition-colors cursor-pointer bg-[#F3F5F2] text-[#37413B] hover:bg-[#E4EAE5]';
            });

            cards.forEach((card) => {
                const katCard = card.getAttribute('data-kategori-card');
                if (kategori === 'semua' || katCard === kategori) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        }

        function bukaModalEdukasi(idx) {
            const kartu = document.querySelector('[data-edukasi-idx="' + idx + '"]');
            const wadahIsi = document.getElementById('isi-edukasi-' + idx);
            const modal = document.getElementById('modal-edukasi');
            if (!kartu || !modal) return;

            const judul = kartu.getAttribute('data-judul') || '';
            const jenis = kartu.getAttribute('data-jenis') || 'edukasi';
            const tanggal = kartu.getAttribute('data-tanggal') || '';
            const ringkasan = kartu.getAttribute('data-ringkasan') || '';
            const isi = wadahIsi ? wadahIsi.textContent.trim() : '';

            document.getElementById('modal-edukasi-judul').textContent = judul;
            document.getElementById('modal-edukasi-jenis').textContent = jenis;
            document.getElementById('modal-edukasi-tanggal').textContent = tanggal;

            const boxRingkasan = document.getElementById('modal-edukasi-ringkasan-box');
            const elRingkasan = document.getElementById('modal-edukasi-ringkasan');
            if (ringkasan && ringkasan !== isi) {
                elRingkasan.textContent = ringkasan;
                boxRingkasan.classList.remove('hidden');
            } else {
                boxRingkasan.classList.add('hidden');
            }

            document.getElementById('modal-edukasi-isi').textContent = isi || ringkasan || 'Tidak ada rincian isi tambahan.';

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function tutupModalEdukasi() {
            const modal = document.getElementById('modal-edukasi');
            if (!modal) return;
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                tutupModalEdukasi();
            }
        });
    </script>
</body>
</html>