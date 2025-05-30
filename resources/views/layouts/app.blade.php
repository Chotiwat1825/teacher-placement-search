<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - ระบบค้นหาข้อมูลการบรรจุครู</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    {{-- ฟอนต์ Sarabun จาก Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css"
        integrity="sha512-ZKX+BvQihRJPA8CROKBhDNvoc2aDMOdAlcm7TUQY+35XYtrd3yh95QOOhsPDQY99L4WOYRNflqrOkR1ebL4VRg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts and Styles (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- ถ้าไม่ได้ใช้ Vite ให้ใช้ asset helper แทน (ตัวอย่าง)
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/app.js') }}" defer></script>
    --}}

    {{-- กำหนดฟอนต์หลักสำหรับ body --}}
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
        }

        /* เพิ่มสไตล์สำหรับ scrollbar ให้ดูดีขึ้น (optional) */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    @stack('styles') {{-- สำหรับ CSS เพิ่มเติมเฉพาะหน้า --}}
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900 flex flex-col min-h-screen">

    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('search.index') }}" class="flex-shrink-0 flex items-center">
                        {{-- สามารถใส่ Logo ตรงนี้ได้ --}}
                        {{-- <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo"> --}}
                        <span
                            class="ml-2 text-xl font-semibold text-indigo-600 hover:text-indigo-800 transition-colors">
                            {{ config('app.name', 'ระบบค้นหาฯ') }}
                        </span>
                    </a>
                </div>

                <!-- Navigation Links (ถ้ามี) -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('search.index') }}"
                            class="text-gray-700 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors {{ request()->routeIs('search.index') || request()->routeIs('search.show') ? 'bg-indigo-500 text-white' : '' }}">
                            หน้าหลักค้นหา
                        </a>
                        {{-- เพิ่ม Link อื่นๆ ที่นี่ ถ้าต้องการ --}}
                        {{-- <a href="#" class="text-gray-700 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">เกี่ยวกับระบบ</a> --}}

                        @auth
                            @if (Auth::user()->is_admin)
                                <a href="{{ route('admin.dashboard') }}"
                                    class="text-gray-700 hover:bg-green-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    ระบบหลังบ้าน
                                </a>
                            @else
                                {{-- <a href="{{ route('dashboard') }}" class="text-gray-700 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">แดชบอร์ดผู้ใช้</a> --}}
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-gray-700 hover:bg-red-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    ออกจากระบบ
                                </a>
                            </form>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-gray-700 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                เข้าสู่ระบบ
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="text-gray-700 hover:bg-indigo-500 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    ลงทะเบียน
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>

                <!-- Mobile menu button (ถ้าต้องการ) -->
                <div class="-mr-2 flex md:hidden">
                    <button type="button" @click="mobileMenuOpen = !mobileMenuOpen"
                        class="bg-white inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg x-show="!mobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mobileMenuOpen" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"
                            style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. (ถ้าต้องการ) -->
        <div x-show="mobileMenuOpen" class="md:hidden" id="mobile-menu" style="display: none;"
            x-transition:enter="transition ease-out duration-100 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75 transform"
            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('search.index') }}"
                    class="text-gray-700 hover:bg-indigo-500 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors {{ request()->routeIs('search.index') || request()->routeIs('search.show') ? 'bg-indigo-500 text-white' : '' }}">หน้าหลักค้นหา</a>
                {{-- เพิ่ม Link อื่นๆ ที่นี่ --}}
                @auth
                    @if (Auth::user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}"
                            class="text-gray-700 hover:bg-green-500 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors">ระบบหลังบ้าน</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-gray-700 hover:bg-red-500 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors">
                            ออกจากระบบ
                        </a>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="text-gray-700 hover:bg-indigo-500 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors">เข้าสู่ระบบ</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="text-gray-700 hover:bg-indigo-500 hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors">ลงทะเบียน</a>
                    @endif
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Heading (Optional) -->
    @if (isset($header))
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $header }}
                </h2>
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="flex-grow">
        {{-- Content ของแต่ละหน้าจะถูกแทรกที่นี่ --}}
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white text-center p-6 mt-auto">
        <div class="max-w-7xl mx-auto">
            <p>© {{ date('Y') }} {{ config('app.name', 'Laravel') }}. สงวนลิขสิทธิ์</p>
            {{-- เพิ่มเติมข้อมูล Footer ถ้าต้องการ --}}
            {{-- <p class="text-sm text-gray-400 mt-1">พัฒนาโดย: [ชื่อของคุณ/ทีมงาน]</p> --}}
        </div>
    </footer>
    {{-- ใน layouts/app.blade.php หรือ details.blade.php @push('scripts') (ต้องโหลด jQuery ก่อน) --}}
    {{-- ตรวจสอบว่า jQuery ถูกโหลดแล้ว --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"
        integrity="sha512-Ixzuzfxv1EqafeQlTCufWfaC6ful6WF szeptember/XOQAUPgKTvixhgThDem2CkYaGUaLqMsMMlstroLuQfigureL9A=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- SweetAlert2 CDN (ถ้าไม่ได้ bundle ผ่าน npm/vite) --}}
    {{-- <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> --}}
    {{-- เนื่องจากเราติดตั้งผ่าน npm และ import ใน bootstrap.js แล้ว จึงไม่จำเป็นต้องมี CDN อีก --}}

    {{-- Script สำหรับแสดง SweetAlert2 จาก Session Flash Messages --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#3085d6' // สีปุ่ม
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: '{{ session('error') }}',
                    icon: 'error',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#d33' // สีปุ่ม
                });
            });
        </script>
    @endif

    @if (session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'แจ้งเพื่อทราบ',
                    text: '{{ session('info') }}',
                    icon: 'info',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#3085d6'
                });
            });
        </script>
    @endif

    @if (session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'คำเตือน',
                    text: '{{ session('warning') }}',
                    icon: 'warning',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#f8bb86'
                });
            });
        </script>
    @endif

    @stack('scripts') {{-- สำหรับ JavaScript เพิ่มเติมเฉพาะหน้า --}}

    {{-- Alpine.js (ถ้าไม่ได้ใช้ใน app.js และต้องการใช้สำหรับ Mobile Menu) --}}
    {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
    <script>
        // Alpine.js data for mobile menu (ถ้าไม่ได้ใช้ Alpine.js ใน app.js)
        // ถ้าคุณใช้ Breeze, Alpine.js น่าจะถูกติดตั้งและใช้งานแล้ว
        // แต่ถ้าไม่ได้ใช้ ให้เพิ่ม data นี้เพื่อให้ mobile menu ทำงาน
        document.addEventListener('alpine:init', () => {
            Alpine.data('mobileMenu', () => ({
                mobileMenuOpen: false,
            }));
        });

        // หรือถ้าจะใช้ JavaScript ธรรมดาสำหรับ Mobile Menu (ตัวอย่างง่ายๆ)
        // const mobileMenuButton = document.querySelector('button[aria-controls="mobile-menu"]');
        // const mobileMenu = document.getElementById('mobile-menu');
        // if (mobileMenuButton && mobileMenu) {
        //     mobileMenuButton.addEventListener('click', () => {
        //         const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true' || false;
        //         mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
        //         mobileMenu.style.display = isExpanded ? 'none' : 'block';
        //         // Toggle SVG icons (simplified, full implementation would swap them)
        //     });
        // }
    </script>
</body>

</html>
