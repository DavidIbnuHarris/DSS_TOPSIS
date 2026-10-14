<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function index()
    {
        $kriteria = \App\Models\Kriteria::all();
        $alternatif = \App\Models\Alternatif::all();
        $skala = \App\Models\Skala::orderBy('nilai', 'asc')->get(); 
        
        // Hitung total untuk Dashboard Cards
        $total_alternatif = $alternatif->count();
        $total_kriteria = $kriteria->count();
        // Menghitung jumlah responden unik (user berbeda yang sudah mengisi)
        $total_responden = \Illuminate\Support\Facades\DB::table('hasil_kuesioners')
                            ->distinct('user_id')
                            ->count('user_id');
        
        return view('admin.index', compact('kriteria', 'alternatif', 'skala', 'total_alternatif', 'total_kriteria', 'total_responden'));
    }

    public function storeAlternatif(Request $request)
    {
        $request->validate(['nama_alternatif' => 'required']);

        \App\Models\Alternatif::create([
            'nama_alternatif' => $request->nama_alternatif
        ]);
        
        return redirect('/admin');
    }

    public function storeKriteria(Request $request)
    {
        $request->validate([
            'nama_kriteria' => 'required',
            'jenis' => 'required',
            'bobot' => 'required|numeric'
        ]);

        \App\Models\Kriteria::create([
            'nama_kriteria' => $request->nama_kriteria,
            'jenis' => $request->jenis,
            'bobot' => $request->bobot
        ]);
        
        return redirect('/admin');
    }

    public function storeSkala(Request $request)
    {
        $request->validate([
            'nilai' => 'required|numeric',
            'keterangan' => 'required'
        ]);

        \App\Models\Skala::create([
            'nilai' => $request->nilai,
            'keterangan' => $request->keterangan
        ]);
        
        return redirect('/admin');
    }
}