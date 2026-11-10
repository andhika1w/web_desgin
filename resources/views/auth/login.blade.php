@extends('layouts.akun')

@section('title', 'Masuk')

@section('body')
<main class="akun">
    <section class="akun__form">
        @include('auth.partials.merek')

        <div class="akun__isi">
            <h1>Masuk ke akun</h1>
            <p class="pengantar">Pakai email atau nomor HP yang Anda daftarkan.</p>

            @if (session('status'))
                <p class="pesan pesan--sukses" role="status">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                <div class="isian">
                    <label for="login">Email atau nomor HP</label>
                    <input id="login" name="login" type="text" value="{{ old('login') }}"
                           autocomplete="username" autofocus required
                           placeholder="nama@email.com atau 0812…"
                           @error('login') aria-invalid="true" aria-describedby="login-galat" @enderror>
                    @error('login')
                        <p class="galat" id="login-galat">{{ $message }}</p>
                    @enderror
                </div>

                <div class="isian">
                    <label for="password">Kata sandi</label>
                    <input id="password" name="password" type="password"
                           autocomplete="current-password" required
                           @error('password') aria-invalid="true" aria-describedby="password-galat" @enderror>
                    @error('password')
                        <p class="galat" id="password-galat">{{ $message }}</p>
                    @enderror
                </div>

                <div class="baris-antara">
                    <label class="centang">
                        <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="tombol">Masuk</button>
            </form>

            <p class="alih">
                Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai penghasil/pengguna, pengelola fasilitas berizin, atau kurir B3</a>
            </p>
        </div>
    </section>

    @include('auth.partials.nota', [
        'judul' => 'Setiap manifes limbah medis & B3 ditimbang resmi di fasilitas berizin.',
        'keterangan' => 'Riwayat manifes pemusnahan medis, status penjemputan kurir, dan poin insentif B3 dapat dipantau setelah masuk. <strong>Manifes di atas hanya contoh.</strong>',
    ])
</main>
@endsection
