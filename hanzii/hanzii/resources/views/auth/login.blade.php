@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white/90 rounded-2xl shadow-2xl p-8 backdrop-blur-md">
        <div class="flex flex-col items-center">
            <img src="https://img.icons8.com/color/96/000000/china.png" alt="Logo" class="mb-4 w-20 h-20 rounded-full shadow-lg border-4 border-indigo-200">
            <h2 class="text-3xl font-extrabold text-indigo-700 drop-shadow text-center">Đăng nhập vào hệ thống</h2>
            <p class="mt-2 text-center text-sm text-gray-500">
                Hoặc
                <a href="{{ route('register') }}" class="font-medium text-pink-600 hover:text-pink-500 transition">đăng ký tài khoản mới</a>
            </p>
        </div>
        <form class="mt-8 space-y-6" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="rounded-lg shadow-inner bg-indigo-50/50 p-6 space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-indigo-700 mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12H8m8 0a4 4 0 11-8 0 4 4 0 018 0zm0 0v1a4 4 0 01-8 0v-1"></path></svg>
                        </span>
                        <input id="email" name="email" type="email" autocomplete="email" required
                            class="appearance-none rounded-md block w-full pl-10 pr-3 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition sm:text-sm bg-white shadow-sm"
                            placeholder="Nhập email..." value="{{ old('email') }}">
                    </div>
                    @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="relative">
                    <label for="password" class="block text-sm font-medium text-indigo-700 mb-1">Mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 0v1a4 4 0 01-8 0v-1"></path></svg>
                        </span>
                        <input id="password" name="password" type="password" autocomplete="current-password" required
                            class="appearance-none rounded-md block w-full pl-10 pr-10 py-2 border border-gray-300 placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition sm:text-sm bg-white shadow-sm"
                            placeholder="Nhập mật khẩu...">
                        <button type="button" onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm text-gray-400 hover:text-pink-500 focus:outline-none">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eyeSlashIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between mt-2">
                <div class="flex items-center">
                    <input id="remember_me" name="remember" type="checkbox"
                        class="h-4 w-4 text-pink-500 focus:ring-pink-400 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                        Ghi nhớ đăng nhập
                    </label>
                </div>
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-pink-500 transition">
                        Quên mật khẩu?
                    </a>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit"
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-lg font-extrabold rounded-full text-white bg-gradient-to-r from-pink-500 via-indigo-500 to-purple-500 hover:from-indigo-600 hover:to-pink-600 shadow-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-400 active:scale-95">
                    Đăng nhập
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    const eyeSlashIcon = document.getElementById('eyeSlashIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.add('hidden');
        eyeSlashIcon.classList.remove('hidden');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('hidden');
        eyeSlashIcon.classList.add('hidden');
    }
}
</script>
@endsection