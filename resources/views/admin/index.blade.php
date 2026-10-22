@extends('layouts.admin')

@section('content')
<h3 class="mb-4 fw-normal text-dark">Selamat Datang, Admin!</h3>

<!-- 3 Kartu Dashboard Ringkasan -->
<div class="row mb-4">
    <!-- Card Alternatif -->
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-1" style="border: 2px solid #0d6efd !important;">
            <div class="card-body d-flex justify-content-between align-items-center p-4">
                <div>
                    <h6 class="fw-bold mb-1" style="color: #0d6efd;">TOTAL ALTERNATIF</h6>
                    <h4 class="mb-0 fw-bold text-dark">{{ $total_alternatif ?? count($alternatif) }} Data</h4>
                </div>
                <i class="bi bi-box fs-1 text-secondary opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Card Kriteria -->
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-1" style="border: 2px solid #198754 !important;">
            <div class="card-body d-flex justify-content-between align-items-center p-4">
                <div>
                    <h6 class="fw-bold mb-1" style="color: #198754;">TOTAL KRITERIA</h6>
                    <h4 class="mb-0 fw-bold text-dark">{{ $total_kriteria ?? count($kriteria) }} Data</h4>
                </div>
                <i class="bi bi-list-check fs-1 text-secondary opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Card Responden -->
    <div class="col-md-4 mb-3">
        <div class="card border-0 shadow-sm rounded-1" style="border: 2px solid #0dcaf0 !important;">
            <div class="card-body d-flex justify-content-between align-items-center p-4">
                <div>
                    <h6 class="fw-bold mb-1" style="color: #0dcaf0;">TOTAL RESPONDEN</h6>
                    <h4 class="mb-0 fw-bold text-dark">{{ $total_responden ?? 0 }} Orang</h4>
                </div>
                <i class="bi bi-people fs-1 text-secondary opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<h2 class="mb-4">Kelola Master Data</h2>

<div class="row">
    
    <!-- 1. Bagian Alternatif -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Data Alternatif</h5>
            </div>
            <div class="card-body">
                <form action="/admin/alternatif" method="POST" class="row g-3 mb-4">
                    @csrf
                    <div class="col-md-10">
                        <input type="text" name="nama_alternatif" class="form-control" placeholder="Nama Alternatif (Contoh: Universitas Pahlawan)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">Simpan</button>
                    </div>
                </form>

                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Alternatif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($alternatif as $index => $a)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $a->nama_alternatif }}</td>
                            <td>
    <form action="/admin/alternatif/{{ $a->id }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus alternatif ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
    </form>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada data alternatif. Silakan tambah di atas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. Bagian Kriteria -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Data Kriteria</h5>
            </div>
            <div class="card-body">
                <form action="/admin/kriteria" method="POST" class="row g-3 mb-4">
                    @csrf
                    <div class="col-md-4">
                        <input type="text" name="nama_kriteria" class="form-control" placeholder="Nama Kriteria (Contoh: Harga)" required>
                    </div>
                    <div class="col-md-3">
                        <select name="jenis" class="form-select" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="benefit">Benefit (Keuntungan)</option>
                            <option value="cost">Cost (Biaya)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" step="0.01" name="bobot" class="form-control" placeholder="Bobot (Contoh: 0.3)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Simpan</button>
                    </div>
                </form>

                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama Kriteria</th>
                            <th>Jenis</th>
                            <th>Bobot</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kriteria as $index => $k)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $k->nama_kriteria }}</td>
                            <td>{{ $k->jenis }}</td>
                            <td>{{ $k->bobot }}</td>
                            <td>
    <form action="/admin/kriteria/{{ $k->id }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kriteria ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
    </form>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada data kriteria. Silakan tambah di atas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 3. Bagian Skala Likert -->
    <div class="col-md-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark fw-bold">
                <h5 class="mb-0">Kelola Skala Likert Kuesioner</h5>
            </div>
            <div class="card-body">
                <form action="/admin/skala" method="POST" class="row g-3 mb-4">
                    @csrf
                    <div class="col-md-3">
                        <input type="number" name="nilai" class="form-control" placeholder="Nilai (Contoh: 1)" required>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="keterangan" class="form-control" placeholder="Keterangan (Contoh: Sangat Buruk)" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-warning w-100 fw-bold">Simpan</button>
                    </div>
                </form>

                <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Nilai Skala</th>
                            <th>Keterangan Penilaian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($skala as $s)
                        <tr>
                            <td>{{ $s->nilai }}</td>
                            <td>{{ $s->keterangan }}</td>
                            <td>
    <form action="/admin/skala/{{ $s->id }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus skala ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
    </form>
</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada data skala likert. Silakan tambah di atas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection