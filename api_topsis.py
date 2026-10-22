
from fastapi import FastAPI, HTTPException
from pydantic import BaseModel
import math

app = FastAPI()


# Struktur data yang diterima dari Laravel
class TopsisData(BaseModel):
    kriteria: list
    alternatif: list
    matriks_x: dict


@app.post("/hitung-topsis")
def hitung(data: TopsisData):

    # TAHAP 1: Persiapan Perhitungan
    kriteria = data.kriteria
    alternatif = data.alternatif
    matriks_x = data.matriks_x

    if not kriteria:
        raise HTTPException(status_code=400, detail="Data kriteria kosong")

    if not alternatif:
        raise HTTPException(status_code=400, detail="Data alternatif kosong")

    # Validasi bobot dan jenis kriteria
    for k in kriteria:
        if "id" not in k or "bobot" not in k or "jenis" not in k:
            raise HTTPException(
                status_code=400,
                detail="Setiap kriteria harus memiliki id, bobot, dan jenis"
            )

        if k["jenis"].lower() not in ["benefit", "cost"]:
            raise HTTPException(
                status_code=400,
                detail=f"Jenis kriteria {k['id']} harus benefit atau cost"
            )

        if float(k["bobot"]) < 0:
            raise HTTPException(
                status_code=400,
                detail="Bobot kriteria tidak boleh negatif"
            )

    # TAHAP 2: Matriks Normalisasi (R)
    pembagi = {}
    matriks_r = {}

    for k in kriteria:
        kid = str(k["id"])

        jumlah_kuadrat = 0.0

        for a in alternatif:
            aid = str(a["id"])

            try:
                nilai = float(matriks_x[aid][kid])
            except (KeyError, TypeError, ValueError):
                raise HTTPException(
                    status_code=400,
                    detail=f"Nilai matriks_x untuk alternatif {aid}, kriteria {kid} tidak valid"
                )

            if not math.isfinite(nilai):
                raise HTTPException(
                    status_code=400,
                    detail=f"Nilai matriks harus berupa angka finite: {aid}, {kid}"
                )

            jumlah_kuadrat += nilai ** 2

        pembagi[kid] = math.sqrt(jumlah_kuadrat)

    for a in alternatif:
        aid = str(a["id"])
        matriks_r[aid] = {}

        for k in kriteria:
            kid = str(k["id"])
            penyebut = pembagi[kid]
            nilai = float(matriks_x[aid][kid])

            matriks_r[aid][kid] = (
                nilai / penyebut if penyebut != 0 else 0.0
            )

    # TAHAP 3: Matriks Normalisasi Terbobot (Y)
    matriks_y = {}

    for a in alternatif:
        aid = str(a["id"])
        matriks_y[aid] = {}

        for k in kriteria:
            kid = str(k["id"])
            bobot = float(k["bobot"])

            matriks_y[aid][kid] = matriks_r[aid][kid] * bobot

    # TAHAP 4: Solusi Ideal Positif (A+) dan Negatif (A-)
    ideal_positif = {}
    ideal_negatif = {}

    for k in kriteria:
        kid = str(k["id"])
        jenis = k["jenis"].lower()

        nilai_kriteria = [
            matriks_y[str(a["id"])][kid]
            for a in alternatif
        ]

        if jenis == "benefit":
            ideal_positif[kid] = max(nilai_kriteria)
            ideal_negatif[kid] = min(nilai_kriteria)
        else:  # cost
            ideal_positif[kid] = min(nilai_kriteria)
            ideal_negatif[kid] = max(nilai_kriteria)

    # TAHAP 5: Jarak terhadap Solusi Ideal
    d_positif = {}
    d_negatif = {}
<<<<<<< HEAD
    
    for a in alternatif:
        a_id = str(a['id'])

        # Rumus jarak Euclidean
        sum_plus = sum((matriks_y[a_id][str(k['id'])] - ideal_positif[str(k['id'])])**2 for k in kriteria)
        sum_min = sum((matriks_y[a_id][str(k['id'])] - ideal_negatif[str(k['id'])])**2 for k in kriteria)

        d_positif[a_id] = round(math.sqrt(sum_plus), 4)
        d_negatif[a_id] = round(math.sqrt(sum_min), 4)
=======
>>>>>>> b9209eb18350870bcbc3fc915926e78ea88d0f0a

    for a in alternatif:
        aid = str(a["id"])

        jumlah_positif = 0.0
        jumlah_negatif = 0.0

        for k in kriteria:
            kid = str(k["id"])
            nilai_y = matriks_y[aid][kid]

            jumlah_positif += (
                nilai_y - ideal_positif[kid]
            ) ** 2

            jumlah_negatif += (
                nilai_y - ideal_negatif[kid]
            ) ** 2

        d_positif[aid] = math.sqrt(jumlah_positif)
        d_negatif[aid] = math.sqrt(jumlah_negatif)

    # TAHAP 6: Nilai Preferensi
    hasil_akhir = []

    for a in alternatif:
        aid = str(a["id"])

        dp = d_positif[aid]
        dn = d_negatif[aid]
        penyebut = dp + dn

        nilai_v = dn / penyebut if penyebut != 0 else 0.0

        hasil_akhir.append({
            "id": a["id"],
            "nama": a.get("nama", f"Alternatif {aid}"),
            "d_positif": dp,
            "d_negatif": dn,
            "nilai_v": nilai_v
        })

    # TAHAP 7: Perangkingan
    hasil_akhir.sort(
        key=lambda item: item["nilai_v"],
        reverse=True
    )

    for peringkat, hasil in enumerate(hasil_akhir, start=1):
        hasil["peringkat"] = peringkat

    # Kirim hasil perhitungan ke Laravel
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
