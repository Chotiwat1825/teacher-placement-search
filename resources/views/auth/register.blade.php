<x-guest-layout>
    {{-- ถ้าไม่ได้ย้าย background และ flex ไปที่ guest.blade.php ให้คงส่วนนี้ไว้ --}}
    {{-- <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100 dark:bg-gray-900"> --}}

    <div>
        <a href="/">
            {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
            <h1 class="text-3xl sm:text-4xl font-bold text-center text-indigo-700 dark:text-indigo-400">
                ลงทะเบียนผู้ใช้งานใหม่
            </h1>
            <p class="text-center text-gray-600 dark:text-gray-400 mt-2">
                สร้างบัญชีเพื่อเข้าใช้งานระบบ
            </p>
        </a>
    </div>

    <div class="w-full sm:max-w-lg mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-lg">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('ชื่อ-นามสกุล')" class="dark:text-gray-300" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input id="name"
                        class="block mt-1 w-full pl-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                        type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                        placeholder="กรอกชื่อและนามสกุลของคุณ" />
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-6">
                <x-input-label for="email" :value="__('อีเมล')" class="dark:text-gray-300" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                    <x-text-input id="email"
                        class="block mt-1 w-full pl-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                        type="email" name="email" :value="old('email')" required autocomplete="username"
                        placeholder="กรอกอีเมลที่ใช้งานได้" />
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-6">
                <x-input-label for="password" :value="__('รหัสผ่าน')" class="dark:text-gray-300" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input id="password"
                        class="block mt-1 w-full pl-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                        type="password" name="password" required autocomplete="new-password"
                        placeholder="อย่างน้อย 8 ตัวอักษร" />
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-6">
                <x-input-label for="password_confirmation" :value="__('ยืนยันรหัสผ่าน')" class="dark:text-gray-300" />
                <div class="relative mt-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-text-input id="password_confirmation"
                        class="block mt-1 w-full pl-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                        type="password" name="password_confirmation" required autocomplete="new-password"
                        placeholder="กรอกรหัสผ่านอีกครั้ง" />
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            {{-- (Optional) เงื่อนไขการยอมรับ หรือ reCAPTCHA อาจจะเพิ่มตรงนี้ --}}

            <div class="flex items-center justify-end mt-8">
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('login') }}">
                    {{ __('มีบัญชีผู้ใช้อยู่แล้ว?') }}
                </a>

                <x-primary-button class="ml-4 px-6 py-3 text-base">
                    {{ __('ลงทะเบียน') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
        © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
    </footer>
    {{-- ปิด div ของ min-h-screen ถ้าไม่ได้ย้ายไป guest.blade.php --}}
    {{-- </div> --}}
</x-guest-layout>
