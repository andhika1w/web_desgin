@extends('layouts.admin')

@section('title', 'Konten & Pengumuman')
@section('heading', 'Konten Edukasi & Pengumuman')
@section('subheading', 'Terbitkan panduan pemilahan sampah, artikel edukasi, dan pengumuman layanan TPS.')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Daftar Konten (2 Kolom) --}}
        <section class="lg:col-span-2 bg-white border border-stone-200 rounded-xl overflow-hidden">
            <div class="px-5 py-4 border-b border-stone-200">
                <h2 class="text-base font-bold text-[#1F2421]">Daftar Konten ({{ $daftarKonten->count() }})</h2>
            </div>

            @if ($daftarKonten->isEmpty())
                <div class="p-6 text-sm text-stone-500">
                    Belum ada artikel atau pengumuman. Tulis konten pertama Anda melalui formulir di samping.
                </div>
            @else
                <div class="divide-y divide-stone-200">
                    @foreach ($daftarKonten as $k)
                        <article class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="min-w-0 space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    @php
                                        $badgeStatus = match ($k->status) {
                                            'terbit' => 'bg-emerald-100 text-emerald-900',
                                            'arsip' => 'bg-stone-200 text-stone-700',
                                            default => 'bg-amber-100 text-amber-900',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs font-bold uppercase rounded {{ $badgeStatus }}">
                                        {{ $k->status }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded bg-stone-100 text-stone-700 uppercase">
                                        {{ $k->jenis }}
                                    </span>
                                    <span class="text-xs text-stone-400 angka-tabular">
                                        {{ $k->published_at ? 'Terbit '.$k->published_at->format('d/m/Y H:i') : 'Dibuat '.$k->created_at?->format('d/m/Y') }}
                                    </span>
                                </div>

                                <h3 class="text-sm font-bold text-[#1F2421]">{{ $k->judul }}</h3>
                                @if ($k->ringkasan)
                                    <p class="text-xs text-stone-600 line-clamp-2">{{ $k->ringkasan }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-1.5 shrink-0">
                                @if ($k->status !== 'terbit')
                                    <form method="POST" action="{{ route('admin.konten.status', $k->konten_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="terbit">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#1D6B45] hover:bg-[#13502F] text-white text-xs font-semibold cursor-pointer">
                                            Terbitkan
                                        </button>
                                    </form>
                                @endif

                                @if ($k->status === 'terbit')
                                    <form method="POST" action="{{ route('admin.konten.status', $k->konten_id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="arsip">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg border border-stone-300 text-stone-700 hover:bg-stone-100 text-xs font-semibold cursor-pointer">
                                            Arsipkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Form Buat Konten --}}
        <section class="bg-white border border-stone-200 rounded-xl p-5">
            <h2 class="text-base font-bold text-[#1F2421]">Tulis Konten Baru</h2>
            <p class="text-xs text-stone-500 mt-0.5 mb-4">Buat panduan pemilahan atau pengumuman operasional TPS.</p>

            <form method="POST" action="{{ route('admin.konten.store') }}" class="space-y-3.5 text-sm">
                @csrf

                <div>
                    <label for="judul" class="block text-xs font-semibold text-stone-700 mb-1">Judul</label>
                    <input id="judul" name="judul" type="text" value="{{ old('judul') }}" required maxlength="200"
                           placeholder="Cara Memilah Sampah B3 Rumah Tangga"
                           class="w-full px-3 py-2 rounded-lg border border-stone-300 bg-white text-sm">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="jenis" class="block text-xs font-semibold text-stone-700 mb-1">Jenis Konten</label>
                        <select id="jenis" name="jenis" required
                                class="w-full px-3 py-2 rounded-lg border border-stone-300 bg-white text-sm">
                            <option value="edukasi" @selected(old('jenis') === 'edukasi')>Edukasi</option>
                            <option value="pengumuman" @selected(old('jenis') === 'pengumuman')>Pengumuman</option>
                            <option value="artikel" @selected(old('jenis') === 'artikel')>Artikel</option>
                            <option value="banner" @selected(old('jenis') === 'banner')>Banner</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-stone-700 mb-1">Status Awal</label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 rounded-lg border border-stone-300 bg-white text-sm">
                            <option value="terbit" @selected(old('status', 'terbit') === 'terbit')>Langsung Terbit</option>
                            <option value="draft" @selected(old('status') === 'draft')>Simpan Draft</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="ringkasan" class="block text-xs font-semibold text-stone-700 mb-1">Ringkasan Singkat (Opsional)</label>
                    <input id="ringkasan" name="ringkasan" type="text" value="{{ old('ringkasan') }}" maxlength="300"
                           placeholder="Ditampilkan sebagai cuplikan di halaman depan"
                           class="w-full px-3 py-2 rounded-lg border border-stone-300 bg-white text-sm">
                </div>

                <div>
                    <label for="isi" class="block text-xs font-semibold text-stone-700 mb-1">Isi Lengkap</label>
                    <textarea id="isi" name="isi" rows="6" required
                              placeholder="Tulis isi panduan atau pengumuman di sini…"
                              class="w-full px-3 py-2 rounded-lg border border-stone-300 bg-white text-sm">{{ old('isi') }}</textarea>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-lg bg-[#1D6B45] hover:bg-[#13502F] text-white font-semibold text-sm cursor-pointer">
                    Simpan Konten
                </button>
            </form>
        </section>
    </div>

@endsection

