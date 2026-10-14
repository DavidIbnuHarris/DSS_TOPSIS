<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuesioner Pemilihan - User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.92);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .kriteria-block:last-child hr { display: none; }
    </style>
</head>
<body class="bg-light">

    <!-- Animasi Loading Bawaan Repo (Disesuaikan ke Warna Hijau) -->
    <div id="loading-overlay">
        <div class="spinner-border text-success" style="width: 4rem; height: 4rem;" role="status"></div>
        <h4 class="mt-3 text-success fw-bold">Memproses Penilaian Anda...</h4>
        <p class="text-muted">Sedang menghitung menggunakan metode TOPSIS</p>
    </div>

    <div class="container my-5">
        
        <!-- Baris Navigasi Tambahan (Tombol Logout & Kembali) -->
        <div class="d-flex justify-content-between align-items-center mb-4">
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

        <h2 class="text-center mb-4">Form Kuesioner Penilaian</h2>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ url('/simpan-kuesioner') }}" id="form-kuesioner" novalidate>
            @csrf
            <input type="hidden" name="simpan_kuesioner" value="1">

            @foreach ($alternatif as $a)
                <div class="card mb-4 border-success shadow-sm">
                    <div class="card-header bg-success text-white fw-bold">
                        Menilai: {{ $a->nama_alternatif }}
                    </div>
                    <div class="card-body">
                        @foreach ($kriteria as $index => $k)
                            <div class="kriteria-block mb-3">
                                <p class="mb-2 fw-semibold">
                                    {{ $index + 1 }}. {{ $k->nama_kriteria }}
                                </p>

                                <div class="d-flex flex-wrap gap-3">
                                    @foreach ($skala as $s)
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="radio"
                                                   id="alt{{ $a->id }}-krit{{ $k->id }}-val{{ $s->nilai }}"
                                                   name="jawaban[{{ $a->id }}][{{ $k->id }}]"
                                                   value="{{ $s->nilai }}"
                                                   required>
                                            <label class="form-check-label text-muted"
                                                   for="alt{{ $a->id }}-krit{{ $k->id }}-val{{ $s->nilai }}">
                                                {{ $s->keterangan }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @if (!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="text-center mb-5">
                <button type="submit" class="btn btn-success btn-lg px-5">
                    Kirim Kuesioner
                </button>
            </div>
        </form>
    </div>

    <!-- Script Validasi Form & Loading Bawaan Repo -->
    <script>
        const form      = document.getElementById('form-kuesioner');
        const overlay   = document.getElementById('loading-overlay');
        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');

                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus({ preventScroll: true });
                }
                return;
            }

            overlay.style.display = 'flex';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>