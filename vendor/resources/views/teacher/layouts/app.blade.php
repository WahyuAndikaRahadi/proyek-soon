<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Jurnal Sekolah SMK Negeri 69 Jakarta')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc; /* bg-gray-50 */
        }
        .gradient-bg-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); /* indigo-600 to purple-600 */
        }
        .gradient-text-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Styling for select inputs, if any */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366f1'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }
        select:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234338ca'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 15l7-7 7 7'%3E%3C/path%3E%3C/svg%3E");
        }
        .btn {
            position: relative;
            overflow: hidden;
        }
        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }
        .btn:active::after {
            width: 200px;
            height: 200px;
        }

        /* --- Custom Dropdown Styles (Refined) --- */
        .dropdown {
            position: relative;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #ffffff;
            min-width: 200px; /* Lebar minimum lebih bagus */
            box-shadow: 0px 8px 24px 0px rgba(0,0,0,0.1); /* Shadow lebih halus */
            z-index: 1000;
            border-radius: 0.5rem; /* Sudut lebih melengkung */
            top: calc(100% + 10px); /* Jarak dari tombol */
            left: 50%; /* Posisikan di tengah relatif terhadap tombol */
            transform: translateX(-50%); /* Geser ke kiri setengah lebar dropdown */
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.2s ease-out, transform 0.2s ease-out; /* Transisi untuk muncul */
            transform-origin: top center; /* Untuk efek scaling */
            padding: 0.5rem 0; /* Padding vertikal di dalam menu */
            border: 1px solid #e5e7eb; /* Border halus */
        }
        .dropdown:hover .dropdown-menu,
        .dropdown-button.active + .dropdown-menu { /* Using JS to add 'active' on click */
            display: block;
            opacity: 1;
            transform: translateX(-50%) scaleY(1); /* Kembali ke skala normal saat muncul */
        }
        .dropdown-menu a {
            color: #4b5563; /* gray-700 */
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: flex; /* Untuk ikon dan teks sejajar */
            align-items: center;
            transition: background-color 0.2s ease, color 0.2s ease;
            white-space: nowrap;
        }
        .dropdown-menu a:hover {
            background-color: #f3f4f6; /* gray-100 */
            color: #1f2937; /* gray-900 */
        }
        .dropdown-menu a.active {
            background-color: #e0e7ff; /* indigo-100 */
            color: #4f46e5; /* indigo-600 */
            font-weight: 600; /* Lebih tebal */
        }
        .dropdown-menu a .fa-solid, .dropdown-menu a .fa-regular {
            margin-right: 0.75rem; /* Jarak ikon dari teks */
            color: #9ca3af; /* gray-400 */
            transition: color 0.2s ease;
        }
        .dropdown-menu a:hover .fa-solid, .dropdown-menu a:hover .fa-regular,
        .dropdown-menu a.active .fa-solid, .dropdown-menu a.active .fa-regular {
            color: #4f46e5; /* Ikon ikut berubah warna */
        }

        /* Specific adjustments for profile dropdown to align right */
        .profile-dropdown .dropdown-menu {
            left: auto;
            right: 0;
            transform: translateX(0); /* Reset transform, align right */
            transform-origin: top right;
        }
        .profile-dropdown:hover .dropdown-menu,
        .profile-dropdown .dropdown-button.active + .dropdown-menu {
            transform: translateX(0) scaleY(1);
        }

        /* Search input styling */
        .search-input-wrapper {
            position: relative;
            flex-grow: 1; /* Allow it to grow */
            margin: 0 1rem; /* Adjust margin as needed */
        }
        .search-input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border-radius: 0.375rem;
            border: 1px solid rgba(255, 255, 255, 0.3); /* Border putih transparan */
            background-color: rgba(255, 255, 255, 0.15); /* Background transparan */
            color: white; /* Teks putih */
            outline: none;
            transition: border-color 0.2s ease, background-color 0.2s ease;
        }
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.7); /* Placeholder putih transparan */
        }
        .search-input:focus {
            border-color: rgba(255, 255, 255, 0.6);
            background-color: rgba(255, 255, 255, 0.25);
        }
        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7); /* Ikon putih transparan */
        }

        /* Custom Footer Wave Styling - Adjusted to match image */
        .footer-wave {
            background-color: #2e71f3; /* Warna dasar biru pada gambar */
            position: relative;
            padding-top: 5rem; /* Increased padding to accommodate wave */
            padding-bottom: 2rem;
            color: white;
            text-align: center;
            overflow: hidden; /* Penting untuk menyembunyikan bagian wave yang di luar */
        }

        .footer-wave::before {
            content: '';
            position: absolute;
            top: 0; /* Mulai dari paling atas container footer */
            left: 0;
            width: 100%;
            height: 100px; /* Tinggi gelombang */
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none"><path fill="%23FFFFFF" fill-opacity="1" d="M0,192L48,170.7C96,149,192,107,288,112C384,117,480,171,576,192C672,213,768,203,864,192C960,181,1056,171,1152,149.3C1248,128,1344,96,1392,80L1440,64L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>') no-repeat center top;
            background-size: cover;
            transform: translateY(-100%); /* Geser ke atas agar gelombang di atas padding-top */
            z-index: 1;
        }

        .footer-content {
            position: relative;
            z-index: 2; /* Pastikan konten footer di atas gelombang */
            padding-top: 2rem; /* Memberi ruang di atas konten footer */
        }
        
        /* Mobile specific adjustments */
        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                align-items: flex-start;
                padding-bottom: 0;
            }
            .search-input-wrapper {
                width: 100%;
                margin: 0.5rem 0 1rem 0; /* Add margin for spacing in mobile */
            }
            .navbar-bottom {
                flex-direction: column;
                align-items: flex-start;
                padding-bottom: 0; /* Remove padding from mobile menu items */
                width: 100%;
            }
            .navbar-bottom a, .navbar-bottom .dropdown {
                width: 100%;
                margin: 0.25rem 0; /* Adjust spacing */
            }
            .navbar-bottom .dropdown-button {
                justify-content: flex-start; /* Align dropdown button text to left */
            }
            .dropdown-menu {
                position: static; /* Stack dropdown menu items in mobile */
                width: 100%;
                box-shadow: none;
                border-radius: 0;
                border: none;
                padding: 0;
                transform: none;
                opacity: 1; /* Always visible when parent is shown */
                margin-top: 0;
                transition: none; /* Disable transition for static dropdowns */
            }
            .dropdown-menu a {
                padding-left: 2.5rem; /* Indent mobile dropdown items */
                background-color: rgba(255, 255, 255, 0.1); /* Slightly different background for sub-items */
                color: white;
            }
            .dropdown-menu a:hover {
                background-color: rgba(255, 255, 255, 0.2);
            }
            .dropdown-menu a.active {
                background-color: rgba(255, 255, 255, 0.25);
                font-weight: 600;
            }
            .dropdown-menu a .fa-solid, .dropdown-menu a .fa-regular {
                color: rgba(255, 255, 255, 0.8);
            }
            .dropdown-menu a:hover .fa-solid, .dropdown-menu a:hover .fa-regular,
            .dropdown-menu a.active .fa-solid, .dropdown-menu a.active .fa-regular {
                color: white;
            }
            .mobile-only-block {
                display: block !important;
            }
            .desktop-only-block {
                display: none !important;
            }

            /* Ensure user profile dropdown works in mobile */
            .profile-dropdown .dropdown-menu {
                left: auto;
                right: 0;
                transform: none; /* Reset transform for static mobile dropdown */
                transform-origin: top right;
            }
            .profile-dropdown .dropdown-button.active + .dropdown-menu {
                display: block;
            }
        }
        
        /* Utility for mobile-only headings */
        .mobile-section-heading {
            display: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem; /* text-sm */
            font-weight: 600;
            margin-top: 1rem;
            margin-bottom: 0.5rem;
            padding-left: 1rem;
        }
        @media (max-width: 768px) {
            .mobile-section-heading {
                display: block;
            }
        }

        /* Scrollbar hide for horizontal menu in mobile */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;  /* Chrome, Safari, Opera */
        }
    </style>
    @yield('css')
</head>
<body class="bg-gray-50 text-gray-800">
    <header class="gradient-bg-primary shadow-lg py-6"> {{-- Increased padding-top/bottom --}}
        <nav class="container mx-auto px-4 navbar-container flex justify-between items-center relative md:flex-nowrap"> {{-- Changed md:flex-wrap to md:flex-nowrap --}}
            {{-- Left Section: Logo & App Name --}}
            <a href="{{ route('teacher.dashboard') }}" class="flex items-center space-x-3 text-white flex-shrink-0">
                {{-- Logo Sekolah (bentuk asli) --}}
                <img src="https://res.cloudinary.com/dxbkwpm3i/image/upload/v1748678502/59619828logo69-600x750_-_Edited_raug9b.png" alt="Logo SMK" class="h-10 w-10 object-contain"> {{-- Removed rounded-full --}}
                <div class="leading-tight">
                    <span class="block text-xl font-bold tracking-tight">Jurnal Sekolah</span>
                    <span class="block text-sm font-light">SMK Negeri 69 Jakarta</span>
                </div>
            </a>

            {{-- Search Bar (Visible on all screens, but centered on mobile) --}}
            <div class="search-input-wrapper w-full md:w-auto md:order-none order-last mt-4 md:mt-0 md:flex-grow"> {{-- Added md:flex-grow --}}
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Cari di sini..." class="search-input">
            </div>

            {{-- Right Section: User Profile Dropdown & Mobile Toggler --}}
            <div class="flex items-center space-x-4 ml-auto flex-shrink-0 md:ml-4"> {{-- Adjusted margin for desktop --}}
                {{-- User Profile Dropdown --}}
                <div class="relative dropdown profile-dropdown">
                    <button class="flex items-center space-x-2 text-white hover:text-indigo-200 transition duration-300 ease-in-out py-2 px-3 rounded-md focus:outline-none dropdown-button" type="button" aria-haspopup="true" aria-expanded="false">
                        {{-- User Avatar (tetap bulat untuk profil) --}}
                        <img src="https://via.placeholder.com/32x32/eee/333?text=AB" alt="User Avatar" class="h-8 w-8 rounded-full border border-white">
                        <span class="font-medium text-sm hidden md:block">{{ Auth::user()->name ?? 'Guru' }}</span> {{-- Dynamic name --}}
                        <i class="fas fa-chevron-down text-xs ml-1"></i>
                    </button>
                    <div class="dropdown-menu">
                        <a href="{{ route('password.change.form') }}" class="block text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm {{ Request::is('change-password') ? 'active' : '' }}">
                            <i class="fas fa-key mr-2"></i>Ubah Password
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="block w-full text-left text-red-600 hover:bg-red-100 px-4 py-2 text-sm btn">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Mobile Navbar Toggler (Moved to top right for mobile) --}}
                <button class="md:hidden text-white focus:outline-none" id="navbar-toggler-mobile">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </nav>

        {{-- Mobile/Desktop Nav Links (Horizontal on desktop, vertical on mobile) --}}
        <div id="main-nav-links" class="container mx-auto px-4 mt-4 md:mt-0 md:flex md:space-x-8 hidden md:block">
            <a href="{{ route('teacher.dashboard') }}" class="block md:inline-block px-3 py-2 text-white hover:bg-indigo-700 rounded-md transition duration-200 {{ Request::is('teacher/dashboard') ? 'bg-indigo-700 font-semibold' : '' }}">
                <i class="fas fa-th-large mr-2 desktop-only-block"></i>Dashboard
            </a>
            <a href="{{ route('teacher.schedules.index') }}" class="block md:inline-block px-3 py-2 text-white hover:bg-indigo-700 rounded-md transition duration-200 {{ Request::is('teacher/schedules*') ? 'bg-indigo-700 font-semibold' : '' }}">
                <i class="fas fa-calendar-alt mr-2 desktop-only-block"></i>Jadwal Saya
            </a>
            
            <div class="relative dropdown">
                <button class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-md transition duration-200 focus:outline-none dropdown-button {{ Request::is('teacher/journals*') ? 'bg-indigo-700 font-semibold' : '' }}" type="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-book mr-2 desktop-only-block"></i>Jurnal Harian <i class="fas fa-chevron-down text-xs ml-1"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('teacher.journals.index') }}" class="block text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm {{ Request::is('teacher/journals') || (Request::is('teacher/journals/*') && !Request::is('teacher/journals/create')) ? 'active' : '' }}">
                        <i class="fas fa-book-open mr-2"></i>Lihat Jurnal
                    </a>
                    <a href="{{ route('teacher.journals.create') }}" class="block text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm {{ Request::is('teacher/journals/create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Jurnal
                    </a>
                </div>
            </div>

            <div class="relative dropdown">
                <button class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-md transition duration-200 focus:outline-none dropdown-button {{ Request::is('teacher/attendances*') ? 'bg-indigo-700 font-semibold' : '' }}" type="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user-check mr-2 desktop-only-block"></i>Absensi Siswa <i class="fas fa-chevron-down text-xs ml-1"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('teacher.attendances.history') }}" class="block text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm {{ Request::is('teacher/attendances/history') ? 'active' : '' }}">
                        <i class="fas fa-history mr-2"></i>Lihat Riwayat Absensi
                    </a>
                    <a href="{{ route('teacher.attendances.create') }}" class="block text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm {{ Request::is('teacher/attendances/create') ? 'active' : '' }}">
                        <i class="fas fa-user-plus mr-2"></i>Tambah Absensi
                    </a>
                </div>
            </div>

            <div class="relative dropdown">
                <button class="flex items-center px-3 py-2 text-white hover:bg-indigo-700 rounded-md transition duration-200 focus:outline-none dropdown-button {{ Request::is('teacher/assessments*') ? 'bg-indigo-700 font-semibold' : '' }}" type="button" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-clipboard-list mr-2 desktop-only-block"></i>Penilaian Siswa <i class="fas fa-chevron-down text-xs ml-1"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('teacher.assessments.index') }}" class="block text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm {{ Request::is('teacher/assessments') || (Request::is('teacher/assessments/*') && !Request::is('teacher/assessments/create')) ? 'active' : '' }}">
                        <i class="fas fa-eye mr-2"></i>Lihat Penilaian
                    </a>
                    <a href="{{ route('teacher.assessments.create') }}" class="block text-gray-700 hover:bg-gray-100 px-4 py-2 text-sm {{ Request::is('teacher/assessments/create') ? 'active' : '' }}">
                        <i class="fas fa-pencil-alt mr-2"></i>Catat Penilaian Baru
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 min-h-screen">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.style.display='none';">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg class="fill-current h-6 w-6 text-red-500 cursor="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" onclick="this.parentElement.style.display='none';">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            </div>
        @endif

        <h1 class="text-3xl font-extrabold text-gray-900 mb-6 border-b-2 border-indigo-500 pb-2">@yield('page_title', 'Halaman Aplikasi')</h1>

        @yield('content')
    </main>

    <footer class="footer-wave text-white relative">
        {{-- SVG Wave is generated by CSS ::before --}}
        <div class="container mx-auto px-4 py-8 text-center footer-content md:flex md:justify-between md:items-start md:text-left">
            <div class="mb-6 md:mb-0">
                <h3 class="text-xl font-bold mb-2">Jurnal Sekolah</h3>
                <p class="text-sm">SMK Negeri 69 Jakarta</p>
                <p class="text-sm mt-2">&copy; {{ date('Y') }} Hak Cipta Dilindungi.</p>
            </div>

            <div class="mb-6 md:mb-0">
                <h3 class="text-xl font-bold mb-2">Kontak Sekolah</h3>
                <ul class="text-sm space-y-1">
                    <li><i class="fas fa-map-marker-alt mr-2"></i>Jl. Contoh Alamat No. 123, Jakarta</li>
                    <li><i class="fas fa-phone mr-2"></i>(021) 1234 5678</li>
                    <li><i class="fas fa-envelope mr-2"></i>info@smkn69jkt.sch.id</li>
                </ul>
            </div>

            <div class="mb-6 md:mb-0">
                <h3 class="text-xl font-bold mb-2">Tautan Cepat</h3>
                <ul class="text-sm space-y-1">
                    <li><a href="{{ route('teacher.dashboard') }}" class="hover:underline">Dashboard</a></li>
                    <li><a href="{{ route('teacher.schedules.index') }}" class="hover:underline">Jadwal Saya</a></li>
                    <li><a href="{{ route('teacher.journals.index') }}" class="hover:underline">Jurnal Harian</a></li>
                    <li><a href="{{ route('teacher.attendances.history') }}" class="hover:underline">Absensi Siswa</a></li>
                    <li><a href="{{ route('teacher.assessments.index') }}" class="hover:underline">Penilaian Siswa</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-xl font-bold mb-2">Ikuti Kami</h3>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a href="#" class="text-indigo-200 hover:text-white transition duration-300"><i class="fab fa-facebook-f text-lg"></i></a>
                    <a href="#" class="text-indigo-200 hover:text-white transition duration-300"><i class="fab fa-twitter text-lg"></i></a>
                    <a href="#" class="text-indigo-200 hover:text-white transition duration-300"><i class="fab fa-instagram text-lg"></i></a>
                    <a href="#" class="text-indigo-200 hover:text-white transition duration-300"><i class="fab fa-linkedin-in text-lg"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/motion@latest/dist/motion.js"></script>
    <script>
        // Toggle mobile menu for the main nav links
        const navbarTogglerMobile = document.getElementById('navbar-toggler-mobile');
        const mainNavLinks = document.getElementById('main-nav-links');
        if (navbarTogglerMobile && mainNavLinks) {
            navbarTogglerMobile.addEventListener('click', () => {
                mainNavLinks.classList.toggle('hidden');
                mainNavLinks.classList.toggle('flex-col'); // Add flex-col for mobile stacking
                mainNavLinks.classList.toggle('space-y-1'); // Add spacing
                mainNavLinks.classList.toggle('bg-indigo-700'); // Add darker background for mobile menu
                mainNavLinks.classList.toggle('p-4'); // Add padding
                mainNavLinks.classList.toggle('rounded-b-md'); // Rounded bottom corners
            });
        }
        
        // Handle dropdowns for desktop (General logic for all dropdowns)
        document.querySelectorAll('.dropdown-button').forEach(button => {
            button.addEventListener('click', function(event) {
                event.stopPropagation(); // Prevent document click from closing immediately

                const dropdownMenu = this.nextElementSibling;
                const wasActive = this.classList.contains('active');

                // Close all dropdowns
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.style.display = 'none';
                    menu.previousElementSibling.classList.remove('active');
                });

                // If it was not active, open the current one
                if (!wasActive) {
                    dropdownMenu.style.display = 'block';
                    this.classList.add('active');
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.style.display = 'none';
                if (menu.previousElementSibling) { // Check if previousElementSibling exists
                    menu.previousElementSibling.classList.remove('active');
                }
            });
        });

        // Add active class based on current route for both desktop and mobile menu
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('#main-nav-links a, .profile-dropdown .dropdown-menu a');

            navLinks.forEach(link => {
                const linkHref = link.getAttribute('href');
                if (linkHref) { 
                    let isActive = currentPath.startsWith(linkHref);

                    // Special handling for resource routes where index is not /resource/create
                    // Jurnal Harian
                    if (linkHref === '{{ route('teacher.journals.index') }}' && currentPath.startsWith('{{ route('teacher.journals.index') }}')) {
                        if (currentPath === '{{ route('teacher.journals.create') }}') {
                            isActive = false; 
                        } else {
                            isActive = true;
                        }
                    }
                    // Absensi Siswa
                    if (linkHref === '{{ route('teacher.attendances.history') }}' && currentPath.startsWith('{{ route('teacher.attendances.history') }}')) {
                           isActive = true;
                    }
                    if (linkHref === '{{ route('teacher.attendances.create') }}' && currentPath.startsWith('{{ route('teacher.attendances.create') }}')) {
                           isActive = true;
                    }
                    // Penilaian Siswa
                    if (linkHref === '{{ route('teacher.assessments.index') }}' && currentPath.startsWith('{{ route('teacher.assessments.index') }}')) {
                        if (currentPath === '{{ route('teacher.assessments.create') }}') {
                            isActive = false; 
                        } else {
                            isActive = true;
                        }
                    }
                    // Password Change
                    if (linkHref === '{{ route('password.change.form') }}' && currentPath.startsWith('{{ route('password.change.form') }}')) {
                        isActive = true;
                    }
                    
                    if (isActive) {
                        // Remove previous active classes
                        document.querySelectorAll('#main-nav-links a.bg-indigo-700, #main-nav-links .dropdown-button.bg-indigo-700').forEach(activeElem => {
                            activeElem.classList.remove('bg-indigo-700', 'font-semibold');
                        });
                        document.querySelectorAll('.dropdown-menu a.active').forEach(activeElem => {
                            activeElem.classList.remove('active');
                        });

                        // Add active class
                        if (link.closest('.dropdown-menu')) {
                            link.classList.add('active');
                            // Also activate the parent dropdown button
                            const parentDropdownButton = link.closest('.dropdown-menu').previousElementSibling;
                            if (parentDropdownButton) {
                                parentDropdownButton.classList.add('bg-indigo-700', 'font-semibold');
                            }
                        } else {
                            link.classList.add('bg-indigo-700', 'font-semibold');
                        }
                    }
                }
            });
        });

        // Search Bar Functionality (client-side simulation)
        document.getElementById('searchInput').addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                const query = this.value.trim();
                if (query) {
                    alert('Melakukan pencarian untuk: ' + query);
                    // Di sini Anda akan mengarahkan pengguna ke halaman pencarian atau memuat hasil secara dinamis
                    // Contoh: window.location.href = '/search?q=' + encodeURIComponent(query);
                }
            }
        });
    </script>
    @yield('js')
</body>
</html>