{{-- Contoh manifes timbang limbah medis & B3. Data contoh, bukan data nyata. --}}
<aside class="akun__nota" aria-labelledby="nota-judul">
    <div class="nota-wrap">
        <p class="nota-wrap__judul" id="nota-judul">{{ $judul }}</p>

        <div class="nota" role="img" aria-label="Contoh manifes setoran: medis infeksius 0,80 kg, medis benda tajam 0,40 kg, baterai bekas B3 1,50 kg, elektronik bekas 1,20 kg. Total 3,90 kg, 45 poin insentif B3.">
            <span class="stempel" aria-hidden="true">SELESAI</span>

            <div class="nota__kepala" aria-hidden="true">
                <div class="nota__tps">TPS Kenanga · Berizin Medis (contoh)</div>
                <div class="nota__kode">MNF-20261106-0001 · dijemput kurir B3</div>
            </div>

            <div aria-hidden="true">
                <div class="nota__baris">
                    <span>Medis - infeksius (Izin Medis)</span><span class="penuntun"></span><span class="nota__berat">0,80 kg</span>
                </div>
                <div class="nota__baris">
                    <span>Medis - benda tajam (Safety Box)</span><span class="penuntun"></span><span class="nota__berat">0,40 kg</span>
                </div>
                <div class="nota__baris">
                    <span>Baterai bekas (B3)</span><span class="penuntun"></span><span class="nota__berat">1,50 kg</span>
                </div>
                <div class="nota__baris">
                    <span>Elektronik bekas</span><span class="penuntun"></span><span class="nota__berat">1,20 kg</span>
                </div>

                <div class="nota__baris nota__total">
                    <span>Manifes ditimbang</span><span class="penuntun"></span><span class="nota__berat">3,90 kg</span>
                </div>
                <div class="nota__baris">
                    <span>Insentif poin B3</span><span class="penuntun"></span><span class="nota__berat">+45</span>
                </div>
            </div>
        </div>

        <p class="nota-wrap__ket">{!! $keterangan !!}</p>
    </div>
</aside>
