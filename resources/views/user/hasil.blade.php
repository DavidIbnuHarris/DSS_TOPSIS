<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Keputusan - User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        
        <!-- Baris Navigasi Tambahan (Tombol Logout & Kembali) -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            @if(Auth::check() && Auth::user()->role === 'admin')
                <a href="{{ url('/admin') }}" class="btn btn-outline-secondary btn-sm">&laquo; Kembali ke Admin</a>
            @else
                <div></div>
            @endif
            
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">Logout</button>
            </form>
        </div>

        <div class="text-center mb-4">
            <h2 class="text-success fw-bold">Terima Kasih!</h2>
            <p class="text-muted">Data kuesioner berhasil diproses menggunakan algoritma TOPSIS.</p>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white fw-bold">
                Hasil Perangkingan Rekomendasi
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Peringkat</th>
                            <th>Alternatif</th>
                            <th>Nilai Preferensi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($hasil_akhir) && count($hasil_akhir) > 0)
                            @foreach($hasil_akhir as $index => $hasil)
                                <tr class="{{ $index === 0 ? 'table-success fw-bold' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $hasil['nama'] }}</td>
                                    <td>{{ $hasil['nilai_v'] }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-muted">Belum ada data perhitungan.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                @if(isset($hasil_akhir) && count($hasil_akhir) > 0)
                    <div class="alert alert-info mt-3 border-0" style="background-color: #d1ecf1; color: #0c5460;">
                        <strong>Kesimpulan:</strong> Berdasarkan perhitungan agregat dari seluruh kuesioner, sistem merekomendasikan <strong>{{ $hasil_akhir[0]['nama'] }}</strong> sebagai pilihan terbaik.
                    </div>
                @endif
            </div>
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="btn btn-outline-success">Isi Kuesioner Baru</a>
        </div>
    </div>
</body>
</html>