from fastapi import FastAPI
from pydantic import BaseModel
import math

app = FastAPI()

# 1. Definisi struktur payload JSON yang dikirim oleh Laravel
class TopsisData(BaseModel):
    kriteria: list
    alternatif: list
    matriks_x: dict

@app.post("/hitung-topsis")
def hitung(data: TopsisData):
    kriteria = data.kriteria
    alternatif = data.alternatif
    matriks_x = data.matriks_x

    # TAHAP 2: Pembagi & Matriks Ternormalisasi (R)
    pembagi = {}
    matriks_r = {}
    
    for k in kriteria:
        k_id = str(k['id'])
        # Menjumlahkan kuadrat semua nilai di satu kolom kriteria
        total_kuadrat = sum(float(matriks_x[str(a['id'])][k_id])**2 for a in alternatif)
        pembagi[k_id] = round(math.sqrt(total_kuadrat), 4)

    for a in alternatif:
        a_id = str(a['id'])
        matriks_r[a_id] = {}
        for k in kriteria:
            k_id = str(k['id'])
            nilai_x = float(matriks_x[a_id][k_id])
            # Cegah pembagian dengan nol
            if pembagi[k_id] != 0:
                matriks_r[a_id][k_id] = round(nilai_x / pembagi[k_id], 4)
            else:
                matriks_r[a_id][k_id] = 0

    # TAHAP 3: Matriks Ternormalisasi Terbobot (Y)
    matriks_y = {}
    
    for a in alternatif:
        a_id = str(a['id'])
        matriks_y[a_id] = {}
        for k in kriteria:
            k_id = str(k['id'])
            bobot = float(k['bobot'])
            matriks_y[a_id][k_id] = round(matriks_r[a_id][k_id] * bobot, 4)

    # TAHAP 4: Solusi Ideal Positif (A+) & Negatif (A-)
    ideal_positif = {}
    ideal_negatif = {}
    
    for k in kriteria:
        k_id = str(k['id'])
        jenis = str(k['jenis']).lower()
        
        # Kumpulkan semua nilai dari alternatif pada kriteria ini ke dalam satu list
        kolom_nilai = [matriks_y[str(a['id'])][k_id] for a in alternatif]
        
        if jenis == 'benefit':
            ideal_positif[k_id] = max(kolom_nilai)
            ideal_negatif[k_id] = min(kolom_nilai)
        else: # cost
            ideal_positif[k_id] = min(kolom_nilai)
            ideal_negatif[k_id] = max(kolom_nilai)

    # TAHAP 5: Jarak Terhadap Solusi Ideal (D+ dan D-)
    d_positif = {}
    d_negatif = {}
    
    for a in alternatif:
        a_id = str(a['id'])
        
        # Rumus jarak Euclidean
        sum_plus = sum((matriks_y[a_id][str(k['id'])] - ideal_positif[str(k['id'])])**2 for k in kriteria)
        sum_min = sum((matriks_y[a_id][str(k['id'])] - ideal_negatif[str(k['id'])])**2 for k in kriteria)
        
        d_positif[a_id] = round(math.sqrt(sum_plus), 4)
        d_negatif[a_id] = round(math.sqrt(sum_min), 4)

    # TAHAP 6: Nilai Preferensi (V) & Perangkingan
    hasil_akhir = []
    
    for a in alternatif:
        a_id = str(a['id'])
        total_d = d_negatif[a_id] + d_positif[a_id]
        
        if total_d != 0:
            v = round(d_negatif[a_id] / total_d, 4)
        else:
            v = 0
            
        hasil_akhir.append({
            "id": a['id'],
            "nama": a['nama_alternatif'],
            "nilai_v": v
        })

    # Urutkan array berdasarkan nilai_v secara descending (dari besar ke kecil)
    hasil_akhir = sorted(hasil_akhir, key=lambda x: x['nilai_v'], reverse=True)

    # 7. Kembalikan semua kamus data ini ke Laravel
    return {
        "pembagi": pembagi,
        "matriks_r": matriks_r,
        "matriks_y": matriks_y,
        "ideal_positif": ideal_positif,
        "ideal_negatif": ideal_negatif,
        "d_positif": d_positif,
        "d_negatif": d_negatif,
        "hasil_akhir": hasil_akhir
    }