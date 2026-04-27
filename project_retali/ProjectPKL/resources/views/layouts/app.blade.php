<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=yes, viewport-fit=cover">
    <title>{{ config('app.name', 'Retail System') }}</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        :root {
            --sidebar-width: 280px;
            --primary-color: #1A365D;
            --secondary-color: #2C5282;
            --accent-color: #D4AF37;
            --light-bg: #F7FAFC;
            --dark-bg: #1E1E2D;
            --text-light: #FFFFFF;
            --text-dark: #2D3748;
            --border-color: #E2E8F0;
            --success-color: #38A169;
            --warning-color: #D69E2E;
            --danger-color: #E53E3E;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            background: var(--light-bg);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary-color) 0%, #2A4365 100%);
            color: var(--text-light);
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            box-shadow: var(--card-shadow);
            z-index: 1000;
            position: fixed;
            height: 100vh;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            transform: translateX(0);
        }

        /* Sidebar menu scroll area */
        .sidebar-menu-scroll {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 20px;
        }

        .sidebar-menu-scroll::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-menu-scroll::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 3px;
        }

        .sidebar-menu-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-header {
            padding: 28px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--text-light);
            font-weight: 700;
            font-size: 1.4rem;
        }

        .sidebar-brand img {
            height: 42px;
            width: auto;
            border-radius: 8px;
        }

        .nav-pills {
            padding: 24px 16px;
            flex-grow: 1;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 16px;
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            border: none;
            background: transparent;
            width: 100%;
            text-align: left;
        }

        .sidebar .nav-link i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            transition: var(--transition);
            color: var(--accent-color);
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: var(--text-light);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: var(--text-light);
            font-weight: 600;
            box-shadow: 0 6px 20px rgba(42, 67, 101, 0.4);
            border-left: 4px solid var(--accent-color);
        }

        .sidebar .nav-link.active i {
            color: var(--accent-color);
        }

        /* Submenu Styles */
        .submenu {
            padding-left: 30px;
            margin-top: 4px;
            overflow: hidden;
            transition: var(--transition);
        }

        .submenu-item {
            padding: 12px 20px;
            margin-bottom: 4px;
            border-radius: 10px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .submenu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            transform: translateX(3px);
        }

        .submenu-item.active {
            background: rgba(255, 255, 255, 0.2);
            color: var(--text-light);
            font-weight: 500;
            border-left: 3px solid var(--accent-color);
        }

        .submenu-item i {
            font-size: 1rem;
            width: 20px;
            color: var(--accent-color);
        }

        .sidebar-footer {
            padding: 24px 20px;
            margin-top: auto;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .user-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-color) 0%, #B8860B 100%);
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .user-details {
            flex-grow: 1;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2px;
        }

        .user-role {
            font-size: 0.8rem;
            opacity: 0.85;
            color: var(--accent-color);
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.12);
            color: var(--text-light);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            width: 100%;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-logout:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
            color: var(--accent-color);
        }

        /* ===== CONTENT ===== */
        .content {
            flex-grow: 1;
            padding: 30px 40px;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            min-height: 100vh;
            width: 100%;
        }

        /* Mobile Menu Button */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: var(--primary-color);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            font-size: 1.3rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: var(--transition);
        }

        .mobile-menu-btn:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }

        /* Mobile Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        .sidebar-overlay.active {
            display: block;
        }

        .content-header {
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .breadcrumb {
            margin-bottom: 0;
            font-size: 0.9rem;
            color: #718096;
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 500;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            margin-bottom: 24px;
            transition: var(--transition);
            background: var(--text-light);
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: var(--text-light);
            border-bottom: 1px solid var(--border-color);
            font-weight: 600;
            padding: 20px 24px;
            border-radius: 16px 16px 0 0 !important;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .card-body {
            padding: 24px;
        }

        /* ===== STATS CARDS ===== */
        .stats-card {
            border-radius: 16px;
            padding: 24px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--text-light);
            height: 100%;
            transition: var(--transition);
            border-bottom: 3px solid var(--accent-color);
        }

        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(26, 54, 93, 0.25);
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 16px;
            opacity: 0.9;
            color: var(--accent-color);
        }

        .stats-number {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
            font-weight: 500;
        }

        /* ===== BUTTONS ===== */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(26, 54, 93, 0.3);
        }

        /* ===== TABLES ===== */
        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }

        .table thead th {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: var(--text-light);
            border: none;
            padding: 16px;
            font-weight: 600;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-color: var(--border-color);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(212, 175, 55, 0.1);
        }

        /* ===== RESPONSIVE IMPROVEMENTS ===== */
        @media (max-width: 992px) {
            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 1000;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .content {
                margin-left: 0 !important;
                padding: 20px 16px;
                padding-top: 70px;
            }

            .page-title {
                font-size: 1.6rem;
            }
        }

        @media (max-width: 768px) {
            .content {
                padding: 15px 12px;
                padding-top: 70px;
            }

            .card-body {
                padding: 18px;
            }

            .page-title {
                font-size: 1.4rem;
            }

            .stats-number {
                font-size: 1.8rem;
            }

            .stats-card {
                padding: 18px;
            }

            .stats-icon {
                font-size: 2rem;
            }

            /* Perbaikan tabel di HP */
            .table-responsive {
                border-radius: 12px;
            }

            .table thead th {
                padding: 12px 8px;
                font-size: 0.85rem;
            }

            .table tbody td {
                padding: 12px 8px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .content {
                padding: 12px 10px;
                padding-top: 65px;
            }

            .page-title {
                font-size: 1.2rem;
            }

            .breadcrumb {
                font-size: 0.8rem;
            }

            .stats-number {
                font-size: 1.5rem;
            }

            .stats-label {
                font-size: 0.75rem;
            }

            .stats-card {
                padding: 14px;
            }

            .stats-icon {
                font-size: 1.6rem;
                margin-bottom: 10px;
            }

            .card-header {
                padding: 15px 18px;
                font-size: 1rem;
            }

            .card-body {
                padding: 15px;
            }

            .table thead th {
                padding: 10px 6px;
                font-size: 0.75rem;
            }

            .table tbody td {
                padding: 10px 6px;
                font-size: 0.75rem;
            }

            /* Perbaikan submenu di HP jika sidebar terbuka */
            .submenu-item {
                padding: 10px 16px;
                font-size: 0.85rem;
            }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        /* ===== UTILITIES ===== */
        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background: var(--primary-color) !important;
        }

        .border-primary {
            border-color: var(--primary-color) !important;
        }

        .text-accent {
            color: var(--accent-color) !important;
        }

        .bg-accent {
            background: var(--accent-color) !important;
        }
    </style>
</head>

<body>
    <!-- Mobile Menu Button -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('dashboard') }}" class="sidebar-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="sidebar-logo">
                <span>Retali Operation</span>
            </a>
        </div>

        <!-- Area menu yang bisa discroll -->
        <div class="sidebar-menu-scroll">
            <ul class="nav nav-pills flex-column mb-auto">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <!-- Pengguna -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('pengguna*', 'tourleaders*') ? 'active' : '' }}"
                        href="#penggunaSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->is('pengguna*', 'tourleaders*') ? 'true' : 'false' }}">
                        <i class="fas fa-users"></i>
                        <span>Pengguna</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->is('pengguna*', 'tourleaders*') ? 'show' : '' }}"
                        id="penggunaSubmenu">
                        <div class="submenu">
                            <a href="{{ route('tourleaders.index') }}"
                                class="submenu-item {{ request()->is('tourleaders*') ? 'active' : '' }}">
                                <i class="fas fa-user-tie"></i>
                                Tour Leader
                            </a>
                            <a href="{{ route('muthawif.index') }}"
                                class="submenu-item {{ request()->is('pengguna/mutowif*') ? 'active' : '' }}">
                                <i class="fas fa-user-check"></i>
                                Muthawif
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Tugas -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('tugas*') ? 'active' : '' }}" href="#tugasSubmenu"
                        data-bs-toggle="collapse" aria-expanded="{{ request()->is('tugas*') ? 'true' : 'false' }}">
                        <i class="fas fa-tasks"></i>
                        <span>Tugas</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>
                    <div class="collapse {{ request()->is('tugas*') ? 'show' : '' }}" id="tugasSubmenu">
                        <div class="submenu">
                            <a href="{{ route('admin.tasks.index') }}"
                                class="submenu-item {{ request()->is('admin/tugas*') ? 'active' : '' }}">
                                <i class="fas fa-user-cog"></i>
                                Tugas Tourleader
                            </a>
                            <a href="{{ route('admin.ceklis.index') }}"
                                class="submenu-item {{ request()->is('tugas/ceklis*') ? 'active' : '' }}">
                                <i class="fas fa-clipboard-check"></i>
                                Tugas Ceklis
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Itinerary -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.itineraries.*') ? 'active' : '' }}"
                        href="#itinerarySubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->routeIs('admin.itineraries.*') ? 'true' : 'false' }}">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>Itinerary</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>

                    <div class="collapse {{ request()->routeIs('admin.itinerary.*') ? 'show' : '' }}"
                        id="itinerarySubmenu">
                        <div class="submenu">
                            <a href="{{ route('admin.itinerary.index') }}"
                                class="submenu-item {{ request()->routeIs('admin.itinerary.index') ? 'active' : '' }}">
                                <i class="fas fa-map"></i> Halaman Itinerary
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Riwayat Absensi -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('admin/attendances*', 'admin/absensi*') ? 'active' : '' }}"
                        href="#absensiSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->is('admin/attendances*', 'admin/absensi*') ? 'true' : 'false' }}">
                        <i class="fas fa-user-clock"></i>
                        <span>Riwayat Absensi</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>

                    <div class="collapse {{ request()->is('admin/attendances*', 'admin/absensi*') ? 'show' : '' }}"
                        id="absensiSubmenu">
                        <div class="submenu">
                            <!-- Absensi Tour Leader -->
                            <a href="{{ route('admin.attendances.index') }}"
                                class="submenu-item {{ request()->is('admin/attendances*') ? 'active' : '' }}">
                                <i class="fas fa-user-tie"></i>
                                Absensi Tour Leader
                            </a>

                            <!-- Absensi Jamaah -->
                            <a href="{{ route('jamaah.index') }}"
                                class="submenu-item {{ request()->is('admin/jamaah*') ? 'active' : '' }}">
                                <i class="fas fa-users"></i>
                                Absensi Jamaah
                            </a>

                            <!-- Absensi Muthawif -->
                            <a href="{{ route('admin.absensi.muthawif.index') }}"
                                class="submenu-item {{ request()->is('admin/absensi-muthawif*') ? 'active' : '' }}">
                                <i class="fas fa-user-check"></i>
                                Absensi Muthawif
                            </a>
                        </div>
                    </div>
                </li>

                <!-- Persiapan Umroh -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('persiapan*') ? 'active' : '' }}" href="#persiapanSubmenu"
                        data-bs-toggle="collapse"
                        aria-expanded="{{ request()->is('persiapan*') ? 'true' : 'false' }}">

                        <i class="fas fa-kaaba"></i>
                        <span>Persiapan Umroh</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>

                    <div class="collapse {{ request()->is('persiapan*') ? 'show' : '' }}" id="persiapanSubmenu">
                        <div class="submenu">

                            <!-- Persiapan Diniyah -->
                            <a href=""
                                class="submenu-item {{ request()->is('') ? 'active' : '' }}">
                                <i class="fas fa-book-quran"></i>
                                Persiapan Diniyah
                            </a>

                            <!-- Persiapan Teknis -->
                            <a href=""
                                class="submenu-item {{ request()->is('') ? 'active' : '' }}">
                                <i class="fas fa-cogs"></i>
                                Persiapan Teknis
                            </a>

                        </div>
                    </div>
                </li>

                <!-- Riwayat Scan (Ubah dari link tunggal menjadi submenu) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('scans*') || request()->is('admin/scans*') || request()->is('scan/koper*') || request()->is('scan/paspor*') ? 'active' : '' }}"
                        href="#scanSubmenu" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->is('scans*') || request()->is('admin/scans*') || request()->is('scan/koper*') || request()->is('scan/paspor*') ? 'true' : 'false' }}">
                        <i class="fas fa-history"></i>
                        <span>Riwayat Scan</span>
                        <i class="fas fa-chevron-down ms-auto"></i>
                    </a>

                    <div class="collapse {{ request()->is('scans*') || request()->is('admin/scans*') || request()->is('scan/koper*') || request()->is('scan/paspor*') ? 'show' : '' }}"
                        id="scanSubmenu">
                        <div class="submenu">
                            <!-- Scan Koper -->
                            <a href="{{ route('scans.index') }}"
                                class="submenu-item {{ request()->is('scan/koper*') || request()->is('scans/koper*') ? 'active' : '' }}">
                                <i class="fas fa-suitcase-rolling"></i>
                                Scan Koper
                            </a>

                            <!-- Scan Paspor -->
                            <a href="{{ route('scan-paspor.index') }}"
                                class="submenu-item {{ request()->is('scan-paspor*') ? 'active' : '' }}">
                                <i class="fas fa-passport"></i>
                                Scan Paspor
                            </a>

                        </div>
                    </div>
                </li>

                <!-- Notifikasi -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('notifications*', 'admin/notifications*') ? 'active' : '' }}"
                        href="{{ route('admin.notifications.index') }}">
                        <i class="fas fa-bell"></i>
                        <span>Notifikasi</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="sidebar-footer">
            @php
                $user = auth('web')->user();
            @endphp

            @if ($user)
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-details">
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-logout" style="text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="content fade-in-up">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Toggle
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            function openSidebar() {
                sidebar.classList.add('open');
                overlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (sidebar.classList.contains('open')) {
                        closeSidebar();
                    } else {
                        openSidebar();
                    }
                });
            }
            
            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }
            
            // Tutup sidebar saat link di klik di mobile
            const allLinks = document.querySelectorAll('.sidebar .nav-link, .sidebar .submenu-item');
            allLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 992) {
                        // Jangan langsung close jika ini adalah toggle submenu
                        const isToggle = this.getAttribute('data-bs-toggle') === 'collapse';
                        if (!isToggle) {
                            setTimeout(() => {
                                closeSidebar();
                            }, 150);
                        }
                    }
                });
            });
            
            // Smooth animations
            const elements = document.querySelectorAll('.card, .nav-link, .stats-card');
            elements.forEach((element, index) => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'all 0.5s ease';

                setTimeout(() => {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }, 100 + (index * 100));
            });

            // Active state management
            const currentPath = window.location.pathname;
            document.querySelectorAll('.nav-link').forEach(link => {
                if (link.getAttribute('href') === currentPath) {
                    link.classList.add('active');
                }
            });

            // Submenu toggle animation
            const submenuToggles = document.querySelectorAll('[data-bs-toggle="collapse"]');
            submenuToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const icon = this.querySelector('.fa-chevron-down');
                    if (icon) {
                        icon.classList.toggle('fa-rotate-180');
                    }
                });
            });
            
            // Handle resize: jika window lebih dari 992 dan sidebar terbuka, tutup
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992 && sidebar.classList.contains('open')) {
                    closeSidebar();
                }
            });
        });
    </script>
</body>

</html>