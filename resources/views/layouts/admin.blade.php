<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SPK TOPSIS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f8f9fc; overflow-x: hidden; }
        
        /* Tema Sidebar Biru Mirip Referensi */
        .sidebar { 
            height: 100vh; 
            background-color: #4e73df; /* Biru khas dashboard */
            color: white; 
            position: fixed; 
            width: 250px; 
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar-brand {
            padding: 20px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
        }
        .sidebar a { 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            padding: 15px 20px; 
            display: flex; 
            align-items: center;
            gap: 10px;
            font-size: 15px;
            border-left: 4px solid transparent;
        }
        .sidebar a:hover, .sidebar a.active { 
            color: white;
            background-color: rgba(255,255,255,0.1); 
            border-left: 4px solid #fff;
            font-weight: 600;
        }
        
        /* Main Content & Topbar */
        .main-wrapper { margin-left: 250px; width: calc(100% - 250px); }
        .topbar {
            background-color: white;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-bottom: 1px solid #e3e6f0;
        }
        .content { padding: 30px; }
    </style>
</head>
<body>
    <div class="d-flex">
        
        <!-- Sidebar Admin -->
        <div class="sidebar">
            <div class="sidebar-brand">
                SPK TOPSIS
            </div>
            <a href="{{ url('/admin') }}" class="{{ request()->is('admin') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard / Master Data
            </a>
            <a href="{{ url('/admin/perhitungan') }}" class="{{ request()->is('admin/perhitungan') ? 'active' : '' }}">
                <i class="bi bi-calculator"></i> Perhitungan TOPSIS
            </a>
            <a href="{{ url('/') }}" target="_blank">
                <i class="bi bi-box-arrow-up-right"></i> Lihat Form Kuesioner
            </a>
        </div>

        <!-- Main Content Wrapper -->
        <div class="main-wrapper">
            
            <!-- Top Navbar -->
            <div class="topbar">
                <h5 class="m-0 fw-normal text-muted">Dashboard Overview</h5>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-secondary">Administrator</span>
                    <i class="bi bi-person-circle fs-3 text-secondary"></i>
                    
                    <form action="{{ route('logout') }}" method="POST" class="m-0 ms-3 border-start ps-3">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
                    </form>
                </div>
            </div>

            <!-- Konten Dinamis -->
            <div class="content">
                @yield('content')
            </div>

        </div>
    </div>
</body>
</html>