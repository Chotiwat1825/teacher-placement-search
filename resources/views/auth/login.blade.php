<x-guest-layout>
    <div
        class="min-h-screen flex flex-col sm:justify-center items-center">
        {{-- ส่วน Logo/Title --}}
        <div>
            <a href="/">
                {{-- เปลี่ยนเป็น Logo ของคุณ หรือใช้ Text --}}
                {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
                <h1 class="text-4xl font-bold text-center text-indigo-700 dark:text-indigo-400">
                    ระบบค้นหาข้อมูล<br class="sm:hidden">การบรรจุครู
                </h1>
                <p class="text-center text-gray-600 dark:text-gray-400 mt-2">
                    เข้าสู่ระบบสำหรับผู้ดูแลระบบ
                </p>
            </a>
        </div>

        <!-- Session Status (เช่น "Password reset link sent.") -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-lg">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div>
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
                            type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                            placeholder="กรอกอีเมลของคุณ" />
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
                            type="password" name="password" required autocomplete="current-password"
                            placeholder="กรอกรหัสผ่าน" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-6">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox"
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                            name="remember">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('จดจำฉันไว้ในระบบ') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-8">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            href="{{ route('password.request') }}">
                            {{ __('ลืมรหัสผ่าน?') }}
                        </a>
                    @endif

                    <x-primary-button class="ml-4 px-6 py-3 text-base">
                        {{ __('เข้าสู่ระบบ') }}
                    </x-primary-button>
                </div>

                @if (Route::has('register'))
                    <div class="text-center mt-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            ยังไม่มีบัญชีผู้ใช้?
                            <a class="underline font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200"
                                href="{{ route('register') }}">
                                ลงทะเบียนที่นี่
                            </a>
                        </p>
                    </div>
                @endif
            </form>
        </div>
        <footer class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
            © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </footer>
    </div>
</x-guest-layout>
