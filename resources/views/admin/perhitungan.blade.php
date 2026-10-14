@extends('layouts.admin')

@section('content')
    <!-- Script MathJax bawaan dari Dev untuk merender rumus matematika -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <h3 class="mb-4 fw-bold">Detail Perhitungan TOPSIS</h3>

    <p class="text-muted mb-4">
        Metode <strong>TOPSIS</strong> menentukan alternatif terbaik berdasarkan <strong>kedekatan</strong> 
        dengan solusi ideal positif dan <strong>kejauhan</strong> dari solusi ideal negatif. 
        Perhitungan dilakukan dalam <strong>6 tahap berurutan</strong>.
    </p>

    @php
        $contoh_a = $alternatif->first();
        $contoh_k = $kriteria->first();
    @endphp

    <!-- ================= HASIL AKHIR ================= -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-success text-white fw-bold">Hasil Akhir Perangkingan</div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th style="width:100px">Peringkat</th>
                        <th>Nama Alternatif</th>
                        <th style="width:180px">Nilai Preferensi (V)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hasil_akhir as $index => $hasil)
                    <tr class="{{ $index === 0 ? 'table-success fw-bold' : '' }} text-center">
                        <td>{{ $index + 1 }}</td>
                        <td class="text-start">{{ $hasil['nama'] }}</td>
                        <td>{{ $hasil['nilai_v'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= TAHAP 1 ================= -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">1. Matriks Keputusan (X)</div>
        <div class="card-body">
            <p class="mb-2"><strong>Tujuan tahap ini:</strong> menyusun tabel berisi nilai rata-rata jawaban kuesioner untuk setiap kombinasi (alternatif × kriteria).</p>
            <p class="mb-3">$$ x_{ij} = \frac{\sum_{r=1}^{R} \text{nilai}_r}{R} $$</p>

            @if($contoh_a && $contoh_k)
            <div class="alert alert-light border mb-3">
                <strong>💡 Contoh Data:</strong> Baris <strong>{{ $contoh_a->nama_alternatif }}</strong> pada kolom <strong>{{ $contoh_k->nama_kriteria }}</strong> memiliki nilai rata-rata kuesioner sebesar <strong>{{ $matriks_x[$contoh_a->id][$contoh_k->id] ?? 0 }}</strong>.
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Alternatif</th>
                            @foreach ($kriteria as $k)
                                <th class="text-center">{{ $k->nama_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alternatif as $a)
                        <tr>
                            <td>{{ $a->nama_alternatif }}</td>
                            @foreach ($kriteria as $k)
                                <td class="text-center">{{ $matriks_x[$a->id][$k->id] ?? 0 }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAHAP 2 ================= -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">2. Matriks Ternormalisasi (R)</div>
        <div class="card-body">
            <p class="mb-2"><strong>Tujuan tahap ini:</strong> menyetarakan skala setiap kriteria dengan membagi nilai dengan <strong>akar dari jumlah kuadrat kolomnya</strong>.</p>
            <p class="mb-3">$$ r_{ij} = \frac{x_{ij}}{\sqrt{\sum_{i=1}^{m} x_{ij}^{2}}} $$</p>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:140px">Pembagi</th>
                            @foreach ($kriteria as $k)
                                <th class="text-center">{{ $k->nama_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>√(Σ x²)</td>
                            @foreach ($kriteria as $k)
                                <td class="text-center">{{ $pembagi[$k->id] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

            <p class="mb-2"><strong>Hasil matriks R:</strong></p>
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Alternatif</th>
                            @foreach ($kriteria as $k)
                                <th class="text-center">{{ $k->nama_kriteria }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alternatif as $a)
                        <tr>
                            <td>{{ $a->nama_alternatif }}</td>
                            @foreach ($kriteria as $k)
                                <td class="text-center">{{ $matriks_r[$a->id][$k->id] ?? 0 }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAHAP 3 ================= -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">3. Matriks Ternormalisasi Terbobot (Y)</div>
        <div class="card-body">
            <p class="mb-2"><strong>Tujuan tahap ini:</strong> mengalikan matriks R dengan <strong>bobot</strong> tiap kriteria, agar kriteria yang lebih penting berpengaruh lebih besar.</p>
            <p class="mb-3">$$ y_{ij} = w_{j} \cdot r_{ij} $$</p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Alternatif</th>
                            @foreach ($kriteria as $k)
                                <th class="text-center">
                                    {{ $k->nama_kriteria }}<br>
                                    <small class="fw-normal">(w={{ $k->bobot }})</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alternatif as $a)
                        <tr>
                            <td>{{ $a->nama_alternatif }}</td>
                            @foreach ($kriteria as $k)
                                <td class="text-center">{{ $matriks_y[$a->id][$k->id] ?? 0 }}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAHAP 4 ================= -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">4. Solusi Ideal Positif (A+) dan Negatif (A−)</div>
        <div class="card-body">
            <p class="mb-2"><strong>Aturan pengambilan nilai:</strong></p>
            <ul>
                <li>Kriteria <strong>benefit</strong>: $$ A^{+} = \max_i(y_{ij}), \quad A^{-} = \min_i(y_{ij}) $$</li>
                <li>Kriteria <strong>cost</strong>: $$ A^{+} = \min_i(y_{ij}), \quad A^{-} = \max_i(y_{ij}) $$</li>
            </ul>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width:160px">Solusi Ideal</th>
                            @foreach ($kriteria as $k)
                                <th class="text-center">
                                    {{ $k->nama_kriteria }}<br>
                                    <span class="badge bg-secondary fw-normal">{{ $k->jenis }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold">A+ (Positif)</td>
                            @foreach ($kriteria as $k)
                                <td class="text-center">{{ $ideal_positif[$k->id] ?? 0 }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="fw-semibold">A− (Negatif)</td>
                            @foreach ($kriteria as $k)
                                <td class="text-center">{{ $ideal_negatif[$k->id] ?? 0 }}</td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAHAP 5 ================= -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">5. Jarak Terhadap Solusi Ideal (D+ dan D−)</div>
        <div class="card-body">
            <p class="mb-2"><strong>Tujuan tahap ini:</strong> mengukur <strong>seberapa jauh</strong> tiap alternatif dari solusi ideal positif (D+) dan negatif (D−).</p>
            <p class="mb-2">$$ D_{i}^{+} = \sqrt{\sum_{j=1}^{n} (y_{ij} - A_{j}^{+})^{2}} $$</p>
            <p class="mb-3">$$ D_{i}^{-} = \sqrt{\sum_{j=1}^{n} (y_{ij} - A_{j}^{-})^{2}} $$</p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Alternatif</th>
                            <th class="text-center">D+ (jarak ke A+)</th>
                            <th class="text-center">D− (jarak ke A−)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alternatif as $a)
                        <tr>
                            <td>{{ $a->nama_alternatif }}</td>
                            <td class="text-center">{{ $d_positif[$a->id] ?? 0 }}</td>
                            <td class="text-center">{{ $d_negatif[$a->id] ?? 0 }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ================= TAHAP 6 ================= -->
    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">6. Nilai Preferensi (V) dan Perangkingan</div>
        <div class="card-body">
            <p class="mb-2"><strong>Tujuan tahap ini:</strong> menghitung nilai akhir tiap alternatif (0 sampai 1). Nilai <strong>tertinggi</strong> = alternatif terbaik.</p>
            <p class="mb-3">$$ V_{i} = \frac{D_{i}^{-}}{D_{i}^{-} + D_{i}^{+}} $$</p>

            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Alternatif</th>
                            <th class="text-center">D−</th>
                            <th class="text-center">D+</th>
                            <th class="text-center">Total (D− + D+)</th>
                            <th class="text-center">V</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alternatif as $a)
                            @php
                                $d_neg = $d_negatif[$a->id] ?? 0;
                                $d_pos = $d_positif[$a->id] ?? 0;
                                $total_d = $d_neg + $d_pos;
                                $v = $total_d > 0 ? round($d_neg / $total_d, 4) : 0;
                            @endphp
                        <tr>
                            <td>{{ $a->nama_alternatif }}</td>
                            <td class="text-center">{{ $d_neg }}</td>
                            <td class="text-center">{{ $d_pos }}</td>
                            <td class="text-center">{{ round($total_d, 4) }}</td>
                            <td class="text-center fw-bold text-success">{{ $v }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection