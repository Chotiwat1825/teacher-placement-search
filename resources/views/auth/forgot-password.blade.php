<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('ลืมรหัสผ่านใช่ไหม? ไม่เป็นไร เพียงแจ้งที่อยู่อีเมลของคุณ แล้วเราจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านให้คุณเลือกตั้งรหัสผ่านใหม่ได้ทางอีเมล') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('อีเมล')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('ส่งลิงก์สําหรับรีเซ็ตรหัสผ่าน') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
