<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alternatif;
use App\Models\Kriteria;
use App\Models\Skala;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth; // Tambahkan ini untuk deteksi user login

class TopsisController extends Controller
{
    // 1. Tampilkan Halaman Kuesioner
    public function kuesioner()
    {
        $kriteria = Kriteria::all();
        $alternatif = Alternatif::all();
        $skala = Skala::orderBy('nilai', 'asc')->get(); 
        
        return view('user.kuesioner', compact('kriteria', 'alternatif', 'skala')); 
    }

    // 2. Simpan Jawaban Kuesioner
    public function simpanKuesioner(Request $request)
    {
        $request->validate([
            'jawaban' => 'required|array',
        ]);

        $user_id = Auth::id(); // Ambil ID User yang sedang login

        if ($request->has('jawaban')) {
            foreach ($request->jawaban as $alternatif_id => $kriteria_data) {
                foreach ($kriteria_data as $kriteria_id => $nilai) {
                    
                    // Gunakan updateOrInsert agar jika user merevisi jawaban, datanya di-update, bukan diduplikat
                    DB::table('hasil_kuesioners')->updateOrInsert(
                        [
                            'user_id' => $user_id, // Pastikan ada kolom user_id di database Anda
                            'alternatif_id' => $alternatif_id,
                            'kriteria_id' => $kriteria_id,
                        ],
                        [
                            'nilai' => $nilai,
                        ]
                    );
                }
            }
        }
        
        return redirect('/hasil');
    }

    // 3. Halaman Hasil untuk User
    public function hasil()
    {
        $kriteria = Kriteria::all();
        $alternatif = Alternatif::all();

        if ($kriteria->isEmpty() || $alternatif->isEmpty()) {
            return redirect('/')->with('error', 'Data masih kosong!');
        }

        // Ambil rata-rata jawaban yang baru saja disimpan
        $matriks_x = [];
        $q_hasil = DB::table('hasil_kuesioners')
            ->select('alternatif_id', 'kriteria_id', DB::raw('AVG(nilai) as rata2'))
            ->groupBy('alternatif_id', 'kriteria_id')
            ->get();

        foreach ($alternatif as $a) {
            foreach ($kriteria as $k) {
                $matriks_x[$a->id][$k->id] = 0;
            }
        }
        foreach ($q_hasil as $row) {
            $matriks_x[$row->alternatif_id][$row->kriteria_id] = round($row->rata2, 3);
        }

        // Bungkus data untuk dikirim ke Python
        $payload_matriks_x = new \stdClass();
        foreach ($matriks_x as $a_id => $k_vals) {
            $payload_matriks_x->{$a_id} = (object) $k_vals;
        }

        $payload = [
            'kriteria' => $kriteria->toArray(),
            'alternatif' => $alternatif->toArray(),
            'matriks_x' => $payload_matriks_x
        ];

        // Minta Python menghitung hasilnya
        try {
            $response = Http::post('http://127.0.0.1:8001/hitung-topsis', $payload);
            $hasil_python = $response->json();
        } catch (\Exception $e) {
            dd('Server Python (Uvicorn) belum menyala! Error: ' . $e->getMessage());
        }

        // Lempar data ranking ke halaman user/hasil.blade.php
        return view('user.hasil', $hasil_python);
    }

    // 4. Halaman Perhitungan untuk Admin
    public function hitungTopsis()
    {
        $kriteria = Kriteria::all();
        $alternatif = Alternatif::all();

        if ($kriteria->isEmpty() || $alternatif->isEmpty()) {
            return redirect('/admin')->with('error', 'Data Kriteria atau Alternatif masih kosong!');
        }

        // TAHAP 1: Menyiapkan Matriks X
        $matriks_x = [];
        $q_hasil = DB::table('hasil_kuesioners')
            ->select('alternatif_id', 'kriteria_id', DB::raw('AVG(nilai) as rata2'))
            ->groupBy('alternatif_id', 'kriteria_id')
            ->get();

        foreach ($alternatif as $a) {
            foreach ($kriteria as $k) {
                $matriks_x[$a->id][$k->id] = 0;
            }
        }
        foreach ($q_hasil as $row) {
            $matriks_x[$row->alternatif_id][$row->kriteria_id] = round($row->rata2, 3);
        }

        $payload_matriks_x = new \stdClass();
        foreach ($matriks_x as $a_id => $k_vals) {
            $payload_matriks_x->{$a_id} = (object) $k_vals;
        }

        $payload = [
            'kriteria' => $kriteria->toArray(),
            'alternatif' => $alternatif->toArray(),
            'matriks_x' => $payload_matriks_x
        ];

        try {
            $response = Http::post('http://127.0.0.1:8001/hitung-topsis', $payload);
            
            if (!$response->successful()) {
                dd([
                    'PESAN' => 'Error dari sistem Python (FastAPI)!',
                    'STATUS_CODE' => $response->status(),
                    'DETAIL_ERROR' => $response->json() ?? $response->body()
                ]);
            }

            $hasil_python = $response->json();
            
            if (!is_array($hasil_python)) {
                dd("ERROR: Python tidak mengembalikan format data JSON yang benar.", $response->body());
            }
            
        } catch (\Exception $e) {
            dd([
                'PESAN' => 'Koneksi Terputus! Server Python (Uvicorn) sepertinya belum menyala.',
                'TIPS' => 'Pastikan kamu sudah mengetik "uvicorn api_topsis:app --port 8001" di terminal.',
                'DETAIL_SISTEM' => $e->getMessage()
            ]);
        }

        // Kalau sukses semua, baru digabungkan untuk view
        $data_ke_view = array_merge([
            'kriteria' => $kriteria,
            'alternatif' => $alternatif,
            'matriks_x' => $matriks_x 
        ], $hasil_python);

        return view('admin.perhitungan', $data_ke_view);
    }
}