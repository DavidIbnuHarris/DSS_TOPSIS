SPK TOPSIS (Sistem Pendukung Keputusan - Metode TOPSIS)

Aplikasi Sistem Pendukung Keputusan (SPK) menggunakan metode TOPSIS yang dibangun dengan Laravel 10 untuk frontend dan manajemen backend, serta terintegrasi dengan FastAPI (Python) sebagai engine perhitungan matematis.

---

🛠️ Persyaratan Sistem

Sebelum menjalankan aplikasi ini di komputer lokal, pastikan Anda telah menginstal:

- PHP (versi 8.2 atau lebih disarankan)
- Composer
- Python (versi 3.8 atau lebih baru) beserta pustaka FastAPI dan Uvicorn
- MySQL/MariaDB (XAMPP atau Laragon)
- Git

---

🚀 Panduan Instalasi dan Konfigurasi

Ikuti langkah-langkah berikut secara berurutan agar aplikasi berjalan dengan normal di komputer atau laptop baru.

1. Clone Repository

Unduh repository dari GitHub menggunakan perintah berikut:

git clone https://github.com/USERNAME_ANDA/NAMA_REPOSITORY.git
cd NAMA_REPOSITORY

Catatan: Ganti "USERNAME_ANDA" dan "NAMA_REPOSITORY" dengan username GitHub dan nama repository yang sebenarnya.

2. Konfigurasi File Environment (".env")

Salin file konfigurasi contoh ".env.example" menjadi ".env":

cp .env.example .env

Buka file ".env" menggunakan editor teks seperti VS Code, kemudian sesuaikan konfigurasi database MySQL:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_lokal_anda
DB_USERNAME=root
DB_PASSWORD=

Penting: Buat database dengan nama yang sesuai pada phpMyAdmin atau melalui Laragon sebelum menjalankan migrasi. Sesuaikan username dan password database dengan konfigurasi MySQL di komputer Anda.

3. Instalasi Dependensi PHP (Laravel)

Jalankan perintah berikut untuk menginstal seluruh dependensi Laravel:

composer install

4. Generate Application Key

Buat kunci unik untuk aplikasi Laravel:

php artisan key:generate

5. Migrasi dan Seeder Database

Agar tabel database dan akun default otomatis dibuat, jalankan:

php artisan migrate --seed

Perintah ini akan menjalankan seluruh migrasi database dan seeder yang tersedia dalam proyek.

Jika ingin mengatur ulang database dari awal, gunakan:

php artisan migrate:fresh --seed

Peringatan: Perintah "migrate:fresh --seed" akan menghapus seluruh tabel yang ada pada database yang dikonfigurasi, kemudian membuat ulang tabel dan menjalankan seeder. Gunakan hanya jika Anda memang ingin menghapus data lama.

6. Menjalankan FastAPI Python Engine

Aplikasi membutuhkan engine Python yang aktif untuk melakukan perhitungan metode TOPSIS.

Buka terminal baru, lalu masuk ke folder proyek:

cd NAMA_REPOSITORY

Jika belum menginstal dependensi Python, instal terlebih dahulu sesuai kebutuhan proyek. Contoh:

pip install fastapi uvicorn

Kemudian jalankan FastAPI menggunakan Uvicorn:

uvicorn api_topsis:app --reload --port 8001

Pastikan file "api_topsis.py" berada pada lokasi yang sesuai dengan perintah tersebut. Jika proyek menggunakan virtual environment Python, aktifkan terlebih dahulu sebelum menjalankan perintah.

7. Menjalankan Server Laravel

Buka terminal lain pada folder proyek Laravel, kemudian jalankan:

php artisan serve --port=8000

Aplikasi dapat diakses melalui browser pada alamat:

http://127.0.0.1:8000

Pastikan server Laravel dan FastAPI berjalan bersamaan agar seluruh fitur aplikasi dapat digunakan.

---

👥 Hak Akses Akun Default

Setelah perintah "php artisan migrate --seed" berhasil dijalankan, akun berikut dapat digunakan untuk login, dengan catatan seeder proyek memang membuat akun tersebut.

Administrator

- Email: "admin@topsis.com"
- Password: "password"
- Hak akses:
  - Mengelola data alternatif.
  - Mengelola data kriteria.
  - Mengelola skala Likert.
  - Melihat detail perhitungan matematis TOPSIS.

User / Responden

- Email: "user@topsis.com"
- Password: "password"
- Hak akses:
  - Mengisi formulir kuesioner penilaian.
  - Melihat hasil akhir perangkingan.

Catatan: Jika akun tidak dapat digunakan untuk login, periksa file seeder pada proyek, pastikan seeder akun sudah dipanggil, dan pastikan password disimpan menggunakan mekanisme hashing yang sesuai dengan Laravel.

---

🔧 Troubleshooting

1. Database Connection Error

Jika muncul kesalahan koneksi database:

- Pastikan MySQL/MariaDB sedang berjalan.
- Periksa nama database pada file ".env".
- Pastikan "DB_USERNAME" dan "DB_PASSWORD" sudah benar.
- Pastikan database sudah dibuat.
- Jika konfigurasi ".env" baru diubah, bersihkan cache konfigurasi:

php artisan config:clear

2. Tabel Database Tidak Ditemukan

Jalankan migrasi database:

php artisan migrate --seed

Pastikan seluruh migrasi dan seeder berhasil dijalankan tanpa kesalahan.

3. Tidak Bisa Login dengan Akun Default

- Pastikan proses seeding sudah berhasil.
- Periksa seeder yang membuat akun administrator dan responden.
- Pastikan email sesuai dengan data yang tersimpan di database.
- Jika password tidak sesuai, periksa proses pembuatan dan hashing password pada seeder.

4. FastAPI Tidak Bisa Dihubungi

- Pastikan FastAPI berjalan pada port "8001".
- Pastikan file "api_topsis.py" tersedia dan tidak mengalami kesalahan.
- Periksa konfigurasi URL FastAPI pada aplikasi Laravel.
- Pastikan alamat dan port yang digunakan Laravel sesuai dengan alamat server FastAPI.

5. Dependensi Laravel Bermasalah

Jika dependensi belum terpasang atau folder "vendor" tidak tersedia, jalankan:

composer install

---

📌 Catatan Penting

- Jangan membagikan file ".env" yang berisi kredensial database atau konfigurasi rahasia.
- Pastikan konfigurasi Laravel dan FastAPI sesuai dengan lingkungan komputer yang digunakan.
- Gunakan "php artisan migrate:fresh --seed" dengan hati-hati karena perintah tersebut menghapus seluruh tabel pada database yang dikonfigurasi.
- Untuk penggunaan di lingkungan produksi, ganti password default dan gunakan konfigurasi keamanan yang sesuai.

---

Dengan panduan ini, pengguna yang mengunduh repository diharapkan dapat memahami proses instalasi, konfigurasi database, migrasi, seeder, login, serta cara menjalankan Laravel dan FastAPI