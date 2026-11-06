@extends('layouts.akun')

@section('title', 'Dashboard')

@php
    $user = auth()->user();
    $namaPeran = [
        'admin' => 'Admin',
        'pengguna' => 'Pengguna',
        'pengelola_tps' => 'Pengelola TPS',
        'kurir' => 'Kurir',
    ][$user->role] ?? $user->role;
@endphp

@section('body')
<main class="dasbor">
    <div class="dasbor__atas">
        @include('auth.partials.merek')

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="tombol tombol--garis">Keluar</button>
        </form>
    </div>

    <h1>Halo, {{ $user->nama }}</h1>
    <p class="pengantar">Anda masuk sebagai {{ $namaPeran }}. Halaman dashboard masih kosong dan siap diisi.</p>

    <dl>
        <dt>Email</dt>
        <dd>{{ $user->email }}</dd>
        <dt>Nomor HP</dt>
        <dd>{{ $user->no_hp }}</dd>
        <dt>Peran</dt>
        <dd>{{ $namaPeran }}</dd>
        <dt>Terakhir masuk</dt>
        <dd>{{ $user->last_login_at?->translatedFormat('d M Y, H:i') ?? 'Baru mendaftar' }}</dd>
    </dl>
</main>
@endsection

