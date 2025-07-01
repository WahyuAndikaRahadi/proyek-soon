<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Jurnal Sekolah SMK Negeri 69 Jakarta')</title>

    <link rel="icon" href="{{ asset('logo.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Pastikan Anda menjalankan `npm install` dan `npm run dev` atau `npm run build` --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Untuk IE, Edge, dan Firefox */
        html {
            -ms-overflow-style: none;
            /* IE dan Edge */
            scrollbar-width: none;
            /* Firefox */
        }


        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            overflow: auto;
        }

        /* Memberi ruang di akhir konten agar tidak tertutup oleh navbar bawah pada mobile */
        @media (max-width: 767px) {
            main.container {
                padding-bottom: 100px;
                /* Ruang untuk navbar bawah */
            }
        }

        .gradient-bg-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            /* indigo-600 to purple-600 */
        }

        .gradient-text-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Styling for select inputs */
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

        /* Custom Dropdown Styles (Refined) */
        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #ffffff;
            min-width: 200px;
            box-shadow: 0px 8px 24px 0px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            border-radius: 0.5rem;
            top: calc(100% + 10px);
            /* Adjust spacing from button */
            opacity: 0;
            transition: opacity 0.2s ease-out, transform 0.2s ease-out;
            transform-origin: top center;
            padding: 0.5rem 0;
            border: 1px solid #e5e7eb;
        }

        .dropdown-button.active+.dropdown-menu {
            display: block;
            opacity: 1;
            /* transform: translateY(0); */
            /* Removed translate for simplicity unless needed for specific animation */
        }

        /* Specific adjustments for profile dropdown to align right */
        .profile-dropdown .dropdown-menu,
        .notification-dropdown .dropdown-menu {
            left: auto;
            right: 0;
            transform-origin: top right;
        }

        .dropdown-menu a {
            color: #4b5563;
            /* gray-700 */
            padding: 0.75rem 1rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: background-color 0.2s ease, color 0.2s ease;
            white-space: nowrap;
        }

        .dropdown-menu a:hover {
            background-color: #f3f4f6;
            /* gray-100 */
            color: #1f2937;
            /* gray-900 */
        }

        .dropdown-menu a.active {
            background-color: #e0e7ff;
            /* indigo-100 */
            color: #4f46e5;
            /* indigo-600 */
            font-weight: 600;
        }

        .dropdown-menu a .fa-solid,
        .dropdown-menu a .fa-regular {
            margin-right: 0.75rem;
            color: #9ca3af;
            /* gray-400 */
            transition: color 0.2s ease;
        }

        .dropdown-menu a:hover .fa-solid,
        .dropdown-menu a:hover .fa-regular,
        .dropdown-menu a.active .fa-solid,
        .dropdown-menu a.active .fa-regular {
            color: #4f46e5;
        }

        /* Custom Footer Wave Styling */
        .footer-wave {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            position: relative;
            padding-top: 100px;
            /* Increased padding to accommodate wave */
            color: white;
            overflow: hidden;
            /* Important to hide the overflowing wave part */
        }

        .footer-wave::before {
            content: '';
            position: absolute;
            top: -1px;
            /* Slightly overlap to avoid thin white lines */
            left: 0;
            width: 100%;
            height: 100px;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none"><path fill="%23f8fafc" fill-opacity="1" d="M0,192L48,170.7C96,149,192,107,288,112C384,117,480,171,576,192C672,213,768,203,864,192C960,181,1056,171,1152,149.3C1248,128,1344,96,1392,80L1440,64L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>') no-repeat center top;
            background-size: cover;
        }

        .footer-content {
            position: relative;
            z-index: 2;
        }

        /* Styles for NEW Mobile Bottom Navbar */
        .mobile-bottom-navbar {
            background-color: #ffffff;
            border-top-left-radius: 1.5rem;
            border-top-right-radius: 1.5rem;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.15);
            display: flex;
            justify-content: space-around;
            align-items: stretch;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.5rem 0.25rem;
            font-family: 'Inter', sans-serif;
        }

        .mobile-bottom-navbar .nav-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #4a5568;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            padding: 0.4rem 0.25rem;
            border-radius: 1rem;
            flex-grow: 1;
            text-align: center;
            position: relative;
            text-decoration: none;
            -webkit-tap-highlight-color: transparent;
            /* Remove tap highlight on mobile */
        }

        .mobile-bottom-navbar .nav-item .icon {
            font-size: 1.35rem;
            margin-bottom: 0.1rem;
        }

        .mobile-bottom-navbar .nav-item span {
            font-size: 0.7rem;
            font-weight: 500;
            display: block;
            line-height: 1.2;
        }

        .mobile-bottom-navbar .nav-item.active {
            background: linear-gradient(135deg, #8b5cf6, #6b46c1);
            /* Purple gradients */
            color: #ffffff;
        }

        .mobile-bottom-navbar .nav-item:active:not(.active) {
            transform: scale(0.95);
            background-color: rgba(0, 0, 0, 0.05);
        }

        @media (min-width: 768px) {
            .mobile-bottom-navbar {
                display: none;
            }
        }

        /* Notification Specific Styles */
        .notification-dropdown .dropdown-menu {
            width: 260px;
            /* Fixed width for notification dropdown */
            padding: 0;
        }

        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            text-decoration: none;
            /* Ensure links don't have underline */
            color: inherit;
            /* Inherit color to apply text-color utility */
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-item .icon-wrapper {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
        }

        .notification-item .text-wrapper p {
            font-size: 0.875rem;
            line-height: 1.4;
            color: #374151;
            /* Default text color, overridden by text-color utility */
        }

        .notification-item .text-wrapper .time {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .status-bar {
            display: flex;
            flex-wrap: wrap;
            /* Allow wrapping on smaller screens */
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            gap: 0.5rem;
            /* Space between items when wrapped */
        }

        .status-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            /* Ensure items don't break too early */
            min-width: 0;
            /* Allow flex item to shrink */
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            white-space: nowrap;
            /* Prevent text from wrapping inside a status item */
        }

        .battery-low {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .battery-medium {
            background-color: #fef3c7;
            color: #d97706;
        }

        .battery-high {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .connection-offline {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .connection-online {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .connection-slow {
            background-color: #fef3c7;
            color: #d97706;
        }

        /* Responsive adjustments for status bar */
        @media (max-width: 640px) {
            .status-bar {
                flex-direction: column;
                /* Stack items vertically on very small screens */
                align-items: flex-start;
                /* Align items to the start */
            }

            .status-info {
                width: 100%;
                /* Take full width */
                justify-content: space-between;
                /* Space out time/date and battery/network */
            }

            .status-item {
                font-size: 0.8rem;
                /* Slightly smaller font on mobile */
                padding: 0.2rem 0.4rem;
            }
        }

        /* --- [IMPROVEMENT & FIX] New Greeting Card Styles --- */
        .greeting-card {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            padding: 1.25rem;
            /* Very light purple gradient */
            border-radius: 0.75rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #ede9fe;
            margin-bottom: 1.5rem;
        }

        .greeting-header {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        #greeting-icon-wrapper {
            flex-shrink: 0;
            padding: 0.5rem;
        }

        #greeting-icon {
            width: 2.5rem;
            height: 2.5rem;
            object-fit: contain;
        }

        #greeting-text h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
        }

        #greeting-text p {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .system-info {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .time-date-info,
        .status-indicators {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            /* Allow wrapping of status items */
        }

        #current-time {
            font-weight: 600;
            font-size: 1rem;
            color: #374151;
        }

        #current-date {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.6rem;
            border-radius: 9999px;
            /* pill shape */
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .battery-low {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .battery-medium {
            background-color: #fef3c7;
            color: #d97706;
        }

        .battery-high {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .connection-offline {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .connection-online {
            background-color: #dcfce7;
            color: #16a34a;
        }

        .connection-slow {
            background-color: #fef3c7;
            color: #d97706;
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 5px rgba(124, 58, 237, 0.2), 0 0 10px rgba(124, 58, 237, 0.1);
            }

            50% {
                box-shadow: 0 0 15px rgba(124, 58, 237, 0.4), 0 0 25px rgba(124, 58, 237, 0.2);
            }
        }

        /* Scrollbar Styling */
        /* Untuk Chrome, Safari, dan Opera */
        ::-webkit-scrollbar {
            display: none;
        }

        /* Gradient Scroll Progress Bar */
        .progress-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: transparent;
            z-index: 1000;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #4f46e5, #7c3aed, #a78bfa);
            width: 0%;
            transition: width 0.1s ease-out;
            border-radius: 0 5px 5px 0;
        }
    </style>
    @yield('css') {{-- For page-specific CSS --}}
</head>

<body class="bg-gray-50 text-gray-800">
    <div class="progress-container">
        <div class="progress-bar" id="progressBar"></div>
    </div>
    <header class="gradient-bg-primary shadow-lg py-3">
        <nav class="container mx-auto px-4 flex justify-between items-center">

            {{-- Logo and Title --}}
            <a href="{{ route('teacher.dashboard') }}" class="flex items-center space-x-3 text-white flex-shrink-0">
                <img src="{{ asset('logo.png') }}"
                    alt="Logo SMK" class="h-10 w-10 object-contain" draggable="false" oncontextmenu="return false;">
                <div class="leading-tight">
                    <span class="block text-xl font-bold tracking-tight">Jurnal Sekolah</span>
                    <span class="block text-sm font-light">SMK Negeri 69 Jakarta</span>
                </div>
            </a>

            {{-- Right-side Navbar Items (Notifications & Profile) --}}
            <div class="flex items-center space-x-2 md:space-x-4">

                {{-- Notification Dropdown --}}
                <div class="relative dropdown notification-dropdown">
                    <button id="notification-button"
                        class="text-white p-2 rounded-full hover:bg-white/20 transition-colors relative dropdown-button"
                        aria-label="Notifikasi">
                        <i class="fas fa-bell text-lg"></i>
                        {{-- Notification Badge (appears if pendingNotifications count > 0) --}}
                        @if (isset($pendingNotifications) && count($pendingNotifications) > 0)
                            <span id="notification-badge"
                                class="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
                        @else
                            <span id="notification-badge"
                                class="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white hidden"></span>
                        @endif
                    </button>
<div id="notification-menu" class="dropdown-menu">
    <div class="flex justify-between items-center px-4 py-2 font-bold border-b border-gray-200">
        <span>Notifikasi</span>
        {{-- "Mark as Read" link, initially hidden if no notifications --}}
        <a href="#" id="mark-all-read"
            class="text-xs font-medium text-indigo-600 hover:underline
            @if (isset($pendingNotifications) && count($pendingNotifications) === 0) hidden @endif">
            Tandai sudah dibaca
        </a>
    </div>
    <div class="max-h-80 overflow-y-auto" id="notification-list">
        @forelse($pendingNotifications as $notification)
            {{-- Properti at_time, time_slot (yang berisi start_time dan end_time)
                 sudah ditambahkan langsung ke objek $notification di ViewComposerServiceProvider.
                 Kita akan mengaksesnya langsung dari $notification, bukan $notification->schedule. --}}
            <a href="{{ $notification->action_url }}"
                class="notification-item {{ $notification->text_color }}"
                data-schedule-id="{{ $notification->schedule_id }}"
                data-type="{{ $notification->type }}"
                data-date="{{ $notification->date }}"
                data-at-time="{{ $notification->at_time ?? '' }}" {{-- Mengakses at_time langsung --}}
                data-start-time="{{ $notification->time_slot ? \Carbon\Carbon::parse(explode(' - ', $notification->time_slot)[0])->format('H:i') : '' }}" {{-- Mengurai start_time dari time_slot --}}
                data-end-time="{{ $notification->time_slot ? \Carbon\Carbon::parse(explode(' - ', $notification->time_slot)[1])->format('H:i') : '' }}" {{-- Mengurai end_time dari time_slot --}}
                >
                <div class="icon-wrapper {{ $notification->bg_color }}"><i
                        class="{{ $notification->icon }}"></i></div>
                <div class="text-wrapper">
                    <p>{!! $notification->message !!}</p>
                    <div class="time text-xs text-gray-500 mt-1">
                        {{ \Carbon\Carbon::parse($notification->date)->isoFormat('dddd, D MMMM Y') }}
                        @if (isset($notification->at_time)) {{-- Mengakses at_time langsung --}}
                            <span class="font-medium">| Jam ke-{{ $notification->at_time }}</span>
                        @endif
                        @if (isset($notification->time_slot)) {{-- Menggunakan time_slot langsung --}}
                            <span class="font-medium">
                                ({{ $notification->time_slot }})
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="notification-item text-center text-gray-500 py-3"
                id="no-new-notifications-message">
                Tidak ada notifikasi baru.
            </div>
        @endforelse
    </div>
</div>
                </div>

                {{-- Profile Dropdown --}}
                <div class="relative dropdown profile-dropdown">
                    <button
                        class="flex items-center space-x-2 text-white hover:text-indigo-200 transition duration-300 ease-in-out py-2 px-2 rounded-md focus:outline-none dropdown-button"
                        type="button" aria-haspopup="true" aria-expanded="false">
                        @php
                            $userPhotoUrl = Auth::user()->photo_url ?? null;
                            $userNameInitial = 'G';
                            if (Auth::user() && Auth::user()->name) {
                                $nameParts = explode(' ', Auth::user()->name);
                                $initials = array_map(function ($part) {
                                    return strtoupper(substr($part, 0, 1));
                                }, $nameParts);
                                $userNameInitial = implode('', array_slice($initials, 0, 2));
                            }
                        @endphp

                        @if ($userPhotoUrl)
                            <img src="{{ $userPhotoUrl }}" alt="User Avatar"
                                class="h-8 w-8 rounded-full border-2 border-white/50 object-cover">
                        @else
                            <div
                                class="h-8 w-8 rounded-full border-2 border-white/50 bg-purple-800 flex items-center justify-center text-white text-sm font-bold">
                                {{ $userNameInitial }}
                            </div>
                        @endif
                        <span class="font-medium text-sm hidden md:block">{{ Auth::user()->name ?? 'Guru' }}</span>
                        <i class="fas fa-chevron-down text-xs ml-1 hidden md:block"></i>
                    </button>
                    <div class="dropdown-menu">
                        <div class="px-4 py-2 border-b border-gray-200 md:hidden">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Guru' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <a href="{{ route('teacher.password.change.form') }}"
                            class="{{ Request::routeIs('teacher.password.change.form') ? 'active' : '' }}">
                            <i class="fas fa-key mr-1"></i>Ubah Password
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit"
                                class="w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700 px-4 py-2 text-sm flex items-center">
                                <i class="fas fa-sign-out-alt mr-2 text-red-400"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main Navigation Links (Desktop Only) --}}
        <div id="main-nav-links" class="container mx-auto py-1 px-4 mt-2 hidden md:flex md:space-x-4">
            <a href="{{ route('teacher.dashboard') }}"
                class="px-3 py-2 text-white hover:bg-white/20 rounded-md transition duration-200 {{ Request::routeIs('teacher.dashboard') ? 'bg-white/20 font-semibold' : '' }}">
                <i class="fas fa-th-large mr-2"></i>Dashboard
            </a>
            <a href="{{ route('teacher.schedules.index') }}"
                class="px-3 py-2 text-white hover:bg-white/20 rounded-md transition duration-200 {{ Request::routeIs('teacher.schedules*') ? 'bg-white/20 font-semibold' : '' }}">
                <i class="fas fa-calendar-alt mr-2"></i>Jadwal Saya
            </a>

            {{-- Journal Dropdown --}}
            <div class="relative dropdown">
                <button
                    class="flex items-center w-full text-left px-3 py-2 text-white hover:bg-white/20 rounded-md transition duration-200 focus:outline-none dropdown-button {{ Request::is('teacher/journals*') ? 'bg-white/20 font-semibold' : '' }}"
                    type="button">
                    <i class="fas fa-book mr-2"></i>Jurnal Harian <i class="fas fa-chevron-down text-xs ml-2"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('teacher.journals.index') }}"
                        class="{{ Request::routeIs('teacher.journals.index') ? 'active' : '' }}">
                        <i class="fas fa-book-open mr-1"></i>Lihat Jurnal
                    </a>
                    <a href="{{ route('teacher.journals.create') }}"
                        class="{{ Request::routeIs('teacher.journals.create') ? 'active' : '' }}">
                        <i class="fas fa-plus-circle mr-1"></i>Tambah Jurnal
                    </a>
                </div>
            </div>

            {{-- Attendance Dropdown --}}
            <div class="relative dropdown">
                <button
                    class="flex items-center w-full text-left px-3 py-2 text-white hover:bg-white/20 rounded-md transition duration-200 focus:outline-none dropdown-button {{ Request::is('teacher/attendances*') ? 'bg-white/20 font-semibold' : '' }}"
                    type="button">
                    <i class="fas fa-user-check mr-2"></i>Absensi Siswa <i
                        class="fas fa-chevron-down text-xs ml-2"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('teacher.attendances.history') }}"
                        class="{{ Request::routeIs('teacher.attendances.history') ? 'active' : '' }}">
                        <i class="fas fa-history mr-1"></i>Riwayat Absensi
                    </a>
                    <a href="{{ route('teacher.attendances.create') }}"
                        class="{{ Request::routeIs('teacher.attendances.create') ? 'active' : '' }}">
                        <i class="fas fa-user-plus mr-1"></i>Tambah Absensi
                    </a>
                </div>
            </div>

            {{-- Assessment Dropdown --}}
            <div class="relative dropdown">
                <button
                    class="flex items-center w-full text-left px-3 py-2 text-white hover:bg-white/20 rounded-md transition duration-200 focus:outline-none dropdown-button {{ Request::is('teacher/assessments*') ? 'bg-white/20 font-semibold' : '' }}"
                    type="button">
                    <i class="fas fa-clipboard-list mr-2"></i>Penilaian Siswa <i
                        class="fas fa-chevron-down text-xs ml-2"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('teacher.assessments.index') }}"
                        class="{{ Request::routeIs('teacher.assessments.index') ? 'active' : '' }}">
                        <i class="fas fa-eye mr-1"></i>Lihat Penilaian
                    </a>
                    <a href="{{ route('teacher.assessments.create') }}"
                        class="{{ Request::routeIs('teacher.assessments.create') ? 'active' : '' }}">
                        <i class="fas fa-pencil-alt mr-1"></i>Form Penilaian
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <!-- [IMPROVEMENT & FIX] New Greeting Card -->
        <div class="greeting-card bg-white">
            <div class="greeting-header">
                <div id="greeting-icon-wrapper">
                    <img id="greeting-icon" src="" alt="Greeting Icon" draggable="false"
                        oncontextmenu="return false;">
                </div>
                <div id="greeting-text">
                    <h1 id="greeting-message"></h1>
                    <p>Selamat datang kembali, {{ Auth::user()->name ?? 'Guru' }}!</p>
                </div>
            </div>
            <div class="system-info">
                <div class="time-date-info">
                    <span id="current-time"></span>
                    <span id="current-date"></span>
                </div>
                <div class="status-indicators">
                    <div id="battery-status" class="status-item hidden">
                        <span id="battery-icon"><i class="fas fa-battery-full"></i></span>
                        <span id="battery-level">100%</span>
                    </div>
                    <div id="network-status" class="status-item">
                        <span id="network-icon"><i class="fas fa-wifi"></i></span>
                        <span id="network-text">Online</span>
                    </div>
                </div>
            </div>
        </div>
        {{-- Session Success Message --}}
        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-md mb-6"
                role="alert">
                <div class="flex">
                    <div class="py-1"><i class="fas fa-check-circle fa-2x text-green-500 mr-4"></i></div>
                    <div>
                        <p class="font-bold">Sukses!</p>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Session Error Message --}}
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md shadow-md mb-6"
                role="alert">
                <div class="flex">
                    <div class="py-1"><i class="fas fa-exclamation-triangle fa-2x text-red-500 mr-4"></i></div>
                    <div>
                        <p class="font-bold">Error!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif


        @yield('content') {{-- Page-specific content will be injected here --}}
    </main>

    <footer class="footer-wave text-white">
        <div
            class="container mx-auto px-4 py-20 text-center footer-content md:flex md:justify-between md:items-start md:text-left">
            <div class="mb-6 md:mb-0 md:w-1/4">
                <div class="flex flex-col items-center md:flex-row md:items-start space-y-2 md:space-y-0 md:space-x-3">
                    <img src="{{ asset('logo.png') }}"
                        alt="Logo SMK Negeri 69 Jakarta"
                        class="h-14 w-14 md:h-10 md:w-10 object-contain mb-2 md:mb-0"
                        draggable="false" oncontextmenu="return false;">
                    <div class="text-center md:text-left">
                        <h3 class="text-xl font-bold mb-1">Jurnal Sekolah</h3>
                        <p class="text-sm">SMK Negeri 69 Jakarta</p>
                    </div>
                </div>
                <p class="text-sm mt-4">&copy; {{ date('Y') }} Hak Cipta Dilindungi.</p>
            </div>

            <div class="mb-6 md:mb-0 md:w-1/3">
                <h3 class="text-xl font-bold mb-2">Kontak Sekolah</h3>
                <ul class="text-sm space-y-2">
                    <li><i class="fas fa-map-marker-alt mt-1 mr-2"></i><span>Jl. KRT. Radjiman Widyodiningrat,
                            Jatinegara, Cakung, Jakarta Timur, 13930</span></li>
                    <li><i class="fas fa-phone mr-2"></i><span>(021) 22131229</span></li>
                    <li><i class="fas fa-envelope mr-2"></i><span>smknegeri69jkt@gmail.com</span></li>
                </ul>
            </div>

            <div class="mb-6 md:mb-0 md:w-1/6">
                <h3 class="text-xl font-bold mb-2">Tautan Cepat</h3>
                <ul class="text-sm space-y-1">
                    <li><a href="{{ route('teacher.dashboard') }}" class="hover:underline">Dashboard</a></li>
                    <li><a href="{{ route('teacher.schedules.index') }}" class="hover:underline">Jadwal</a></li>
                    <li><a href="{{ route('teacher.journals.index') }}" class="hover:underline">Jurnal</a></li>
                    <li><a href="{{ route('teacher.attendances.history') }}" class="hover:underline">Absensi</a></li>
                </ul>
            </div>

            <div class="md:w-1/6">
                <h3 class="text-xl font-bold mb-2">Ikuti Kami</h3>
                <div class="flex justify-center md:justify-start space-x-4">
                    <a target="_blank" href="https://www.instagram.com/smknegeri69jakarta"
                        class="text-indigo-200 hover:text-white transition duration-300" aria-label="Instagram"><i
                            class="fab fa-instagram text-lg"></i></a>
                    <a target="_blank" href="https://www.youtube.com/@smkn69jakarta25"
                        class="text-indigo-200 hover:text-white transition duration-300" aria-label="YouTube"><i
                            class="fab fa-youtube text-lg"></i></a>
                    <a target="_blank" href="https://www.smkn69jkt.sch.id/"
                        class="text-indigo-200 hover:text-white transition duration-300" aria-label="Website"><i
                            class="fa-solid fa-globe"></i></a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Mobile Bottom Navigation Bar (Hidden on Desktop) --}}
    <nav class="mobile-bottom-navbar md:hidden">
        <a href="{{ route('teacher.dashboard') }}"
            class="nav-item {{ Request::routeIs('teacher.dashboard') ? 'active' : '' }}" data-page-name="dashboard">
            <i class="fas fa-th-large icon"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('teacher.schedules.index') }}"
            class="nav-item {{ Request::routeIs('teacher.schedules*') ? 'active' : '' }}" data-page-name="schedules">
            <i class="fas fa-calendar-alt icon"></i>
            <span>Jadwal</span>
        </a>
        <a href="{{ route('teacher.journals.index') }}"
            class="nav-item {{ Request::routeIs('teacher.journals*') ? 'active' : '' }}" data-page-name="journals">
            <i class="fas fa-book icon"></i>
            <span>Jurnal</span>
        </a>
        <a href="{{ route('teacher.attendances.history') }}"
            class="nav-item {{ Request::routeIs('teacher.attendances*') ? 'active' : '' }}"
            data-page-name="attendances">
            <i class="fas fa-user-check icon"></i>
            <span>Absensi</span>
        </a>
        <a href="{{ route('teacher.assessments.index') }}"
            class="nav-item {{ Request::routeIs('teacher.assessments*') ? 'active' : '' }}"
            data-page-name="assessments">
            <i class="fas fa-clipboard-list icon"></i>
            <span>Nilai</span>
        </a>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.onscroll = function() {
                const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = (winScroll / height) * 100;
                document.getElementById("progressBar").style.width = scrolled + "%";
            };


            // --- Dropdown Logic ---
            const dropdownButtons = document.querySelectorAll('.dropdown-button');

            dropdownButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.stopPropagation(); // Prevent click from bubbling to window
                    const dropdownMenu = this.nextElementSibling;
                    const isActive = this.classList.contains('active');

                    // Close all other active dropdowns
                    document.querySelectorAll('.dropdown-button.active').forEach(btn => {
                        if (btn !== this) { // Only close others
                            btn.classList.remove('active');
                            btn.nextElementSibling.style.display = 'none';
                        }
                    });

                    // Toggle the clicked dropdown
                    if (isActive) {
                        this.classList.remove('active');
                        dropdownMenu.style.display = 'none';
                    } else {
                        this.classList.add('active');
                        dropdownMenu.style.display = 'block';
                    }
                });
            });

            // Close dropdowns when clicking outside
            window.addEventListener('click', function() {
                document.querySelectorAll('.dropdown-button.active').forEach(button => {
                    button.classList.remove('active');
                    button.nextElementSibling.style.display = 'none';
                });
            });

            // --- Notification Dismissal Logic ---
            const markAllReadBtn = document.getElementById('mark-all-read');
            const notificationBadge = document.getElementById('notification-badge');
            const notificationList = document.getElementById('notification-list');
            let noNewNotificationsMessage = document.getElementById(
                'no-new-notifications-message'); // Use let to reassign

            // Initial state check for notification badge and mark as read button
            function updateNotificationDisplay() {
                const currentNotificationsCount = notificationList.querySelectorAll(
                    '.notification-item:not(#no-new-notifications-message)').length;
                if (currentNotificationsCount > 0) {
                    if (notificationBadge) notificationBadge.classList.remove('hidden');
                    if (markAllReadBtn) markAllReadBtn.classList.remove('hidden');
                } else {
                    if (notificationBadge) notificationBadge.classList.add('hidden');
                    if (markAllReadBtn) markAllReadBtn.classList.add('hidden'); // Corrected variable name here
                    // Ensure "Tidak ada notifikasi baru" message is visible if list is empty
                    if (!noNewNotificationsMessage) {
                        noNewNotificationsMessage = document.createElement('div');
                        noNewNotificationsMessage.id = 'no-new-notifications-message';
                        noNewNotificationsMessage.className = 'notification-item text-center text-gray-500 py-3';
                        noNewNotificationsMessage.textContent = 'Tidak ada notifikasi baru.';
                        notificationList.appendChild(noNewNotificationsMessage);
                    } else {
                        noNewNotificationsMessage.classList.remove('hidden');
                    }
                }
            }

            updateNotificationDisplay(); // Call on DOM load

            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const notificationItems = document.querySelectorAll(
                        '#notification-list .notification-item');
                    const notificationsToDismiss = [];

                    notificationItems.forEach(item => {
                        // Ensure it's an actual notification item, not the "no new" message
                        if (item.id !== 'no-new-notifications-message' && item.dataset.scheduleId &&
                            item.dataset.type && item.dataset.date) {
                            notificationsToDismiss.push({
                                schedule_id: item.dataset.scheduleId,
                                type: item.dataset.type,
                                date: item.dataset.date // Correctly getting the date
                            });
                        }
                    });

                    if (notificationsToDismiss.length === 0) {
                        console.log('No valid notifications to dismiss.');
                        updateNotificationDisplay(); // Update display to hide badge/button
                        return;
                    }

                    // Send dismissal request to the server
                    fetch('{{ route('teacher.notifications.dismiss') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                notifications: notificationsToDismiss
                            })
                        })
                        .then(response => {
                            // Check if the response is JSON, otherwise, handle potential HTML (e.g., login redirect)
                            const contentType = response.headers.get("content-type");
                            if (contentType && contentType.indexOf("application/json") !== -1) {
                                return response.json();
                            } else {
                                // If it's not JSON, it might be a redirect or an error page
                                return response.text().then(text => {
                                    console.error(
                                        'Server returned non-JSON response (likely HTML):',
                                        text);
                                    if (text.includes('<form action="{{ route('login') }}"')) {
                                        alert(
                                            'Sesi Anda telah berakhir. Silakan login kembali.'
                                            );
                                        window.location.href = '/login';
                                    }
                                    throw new Error(
                                        'Server returned HTML, not JSON. Check Network Tab for details.'
                                    );
                                });
                            }
                        })
                        .then(data => {
                            if (data.success) {
                                console.log('Notifications successfully marked as dismissed.');
                                // Clear current notifications and show "no new notifications" message
                                notificationList.innerHTML = '';
                                if (!
                                    noNewNotificationsMessage
                                    ) { // Re-check if message element exists after clearing innerHTML
                                    noNewNotificationsMessage = document.createElement('div');
                                    noNewNotificationsMessage.id = 'no-new-notifications-message';
                                    noNewNotificationsMessage.className =
                                        'notification-item text-center text-gray-500 py-3';
                                    noNewNotificationsMessage.textContent =
                                        'Tidak ada notifikasi baru.';
                                    notificationList.appendChild(noNewNotificationsMessage);
                                } else {
                                    noNewNotificationsMessage.classList.remove('hidden');
                                }
                                updateNotificationDisplay(); // Update display (badge, button)

                                // Close notification dropdown
                                const notificationDropdownButton = document.querySelector(
                                    '.notification-dropdown .dropdown-button');
                                if (notificationDropdownButton && notificationDropdownButton.classList
                                    .contains('active')) {
                                    notificationDropdownButton.classList.remove('active');
                                    notificationDropdownButton.nextElementSibling.style.display =
                                        'none';
                                }

                            } else {
                                console.error('Failed to mark notifications as dismissed:', data
                                    .message);
                                alert('Terjadi kesalahan saat menandai notifikasi: ' + (data.message ||
                                    'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('Error during fetch operation:', error);
                            alert('Terjadi kesalahan jaringan atau server: ' + error.message);
                        });
                });
            }
        });

        // --- [IMPROVEMENT & FIX] Greeting, Time, Status Logic ---
        document.addEventListener('DOMContentLoaded', function() {
            const greetings = {
                morning: {
                    text: 'Selamat Pagi!',
                    icon: 'https://res.cloudinary.com/dxbkwpm3i/image/upload/v1747107337/Color3_a37nzs.png'
                },
                afternoon: {
                    text: 'Selamat Siang!',
                    icon: 'https://res.cloudinary.com/dxbkwpm3i/image/upload/v1747107956/Color3_zze7jd.png'
                },
                evening: {
                    text: 'Selamat Sore!',
                    icon: 'https://res.cloudinary.com/dxbkwpm3i/image/upload/v1747107881/Color-2_vmcdkp.png'
                },
                night: {
                    text: 'Selamat Malam!',
                    icon: 'https://res.cloudinary.com/dxbkwpm3i/image/upload/v1747108167/Color2_r3z3jx.png'
                }
            };

            function updateGreeting() {
                const hour = new Date().getHours();
                let period = greetings.night;
                if (hour >= 5 && hour < 12) period = greetings.morning;
                else if (hour >= 12 && hour < 17) period = greetings.afternoon;
                else if (hour >= 17 && hour < 20) period = greetings.evening;

                document.getElementById('greeting-message').textContent = period.text;
                document.getElementById('greeting-icon').src = period.icon;
            }

            function updateTime() {
                const now = new Date();
                const timeOptions = {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                };
                const dateOptions = {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long',
                    year: 'numeric'
                };
                document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID', timeOptions);
                document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', dateOptions);
            }

            function updateBatteryStatus() {
                if ('getBattery' in navigator) {
                    navigator.getBattery().then(battery => {
                        const batteryStatusEl = document.getElementById('battery-status');
                        const update = () => {
                            batteryStatusEl.classList.remove('hidden');
                            const level = Math.round(battery.level * 100);
                            let icon = 'fa-battery-full',
                                statusClass = 'battery-high';
                            if (level <= 20) {
                                icon = 'fa-battery-quarter';
                                statusClass = 'battery-low';
                            } else if (level <= 60) {
                                icon = 'fa-battery-half';
                                statusClass = 'battery-medium';
                            }
                            if (battery.charging) {
                                icon = 'fa-bolt';
                                statusClass = 'battery-high';
                            }

                            batteryStatusEl.className = `status-item ${statusClass}`;
                            document.getElementById('battery-icon').innerHTML =
                                `<i class="fas ${icon}"></i>`;
                            document.getElementById('battery-level').textContent = `${level}%`;
                        };
                        update();
                        battery.addEventListener('levelchange', update);
                        battery.addEventListener('chargingchange', update);
                    });
                }
            }

            function updateNetworkStatus() {
                const networkStatusEl = document.getElementById('network-status');
                const update = () => {
                    let icon = 'fa-wifi',
                        text = 'Online',
                        statusClass = 'connection-online';
                    if (!navigator.onLine) {
                        icon = 'fa-plug';
                        text = 'Offline';
                        statusClass = 'connection-offline';
                    }
                    networkStatusEl.className = `status-item ${statusClass}`;
                    document.getElementById('network-icon').innerHTML = `<i class="fas ${icon}"></i>`;
                    document.getElementById('network-text').textContent = text;
                };
                update();
                window.addEventListener('online', update);
                window.addEventListener('offline', update);
            }

            // Initial calls
            updateGreeting();
            updateTime();
            updateBatteryStatus();
            updateNetworkStatus();

            // Update time every second, and greeting every minute
            setInterval(updateTime, 1000);
            setInterval(updateGreeting, 60000);
        });
    </script>
    @yield('js') {{-- For page-specific JavaScript --}}
</body>

</html>