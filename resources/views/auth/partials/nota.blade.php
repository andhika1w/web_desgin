{{-- Contoh nota timbang setoran. Data contoh, bukan data nyata. --}}
<aside class="akun__nota" aria-labelledby="nota-judul">
    <div class="nota-wrap">
        <p class="nota-wrap__judul" id="nota-judul">{{ $judul }}</p>

        <div class="nota" role="img" aria-label="Contoh nota setoran: plastik 2,40 kg, kertas dan kardus 3,10 kg, logam 0,80 kg. Total 6,30 kg, 118 poin.">
            <span class="stempel" aria-hidden="true">SELESAI</span>

            <div class="nota__kepala" aria-hidden="true">
                <div class="nota__tps">TPS Kenanga (contoh)</div>
                <div class="nota__kode">STR-20261105-0001 · antar sendiri</div>
            </div>

            <div aria-hidden="true">
                <div class="nota__baris">
                    <span>Plastik</span><span class="penuntun"></span><span class="nota__berat">2,40 kg</span>
                </div>
                <div class="nota__baris">
                    <span>Kertas dan kardus</span><span class="penuntun"></span><span class="nota__berat">3,10 kg</span>
                </div>
                <div class="nota__baris">
                    <span>Logam</span><span class="penuntun"></span><span class="nota__berat">0,80 kg</span>
                </div>

                <div class="nota__baris nota__total">
                    <span>Ditimbang</span><span class="penuntun"></span><span class="nota__berat">6,30 kg</span>
                </div>
                <div class="nota__baris">
                    <span>Poin masuk</span><span class="penuntun"></span><span class="nota__berat">+118</span>
                </div>
            </div>
        </div>

        <p class="nota-wrap__ket">{!! $keterangan !!}</p>
    </div>
</aside>

