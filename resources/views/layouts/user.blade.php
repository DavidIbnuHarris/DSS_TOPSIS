<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPK TOPSIS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card-alternatif { border-top: 4px solid #1abc9c; }
    </style>
</head>
<body>
    <!-- Navbar untuk Logout -->
    <nav class="navbar navbar-expand bg-white shadow-sm mb-4 py-2">
        <div class="container">
            <span class="navbar-brand mb-0 h1 fw-bold" style="color: #1abc9c;">SPK TOPSIS</span>
            <div class="ms-auto">
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger px-3">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Konten Kuesioner -->
    @yield('content')

</body>
</html>