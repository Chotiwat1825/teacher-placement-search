<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Sarabun:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Sarabun', 'Figtree', sans-serif;
            /* เพิ่ม Sarabun เป็น fallback */
        }
    </style>
</head>

<body class="font-sans text-gray-900 antialiased">
    {{-- ย้ายส่วนจัด layout หลักมาที่นี่ ถ้าต้องการให้ทุกหน้า guest เหมือนกัน --}}
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 dark:bg-gray-900">
        {{-- ส่วน Logo/Title อาจจะยังอยู่ใน login.blade.php หรือจะย้ายมาบางส่วนก็ได้ --}}
        {{-- <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div> --}}

        {{-- กล่องฟอร์มจะถูก render โดย slot --}}
        {{-- <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"> --}}
        {{ $slot }}
        {{-- </div> --}}
    </div>
</body>

</html>
