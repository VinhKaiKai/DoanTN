<!-- Header -->
<header class="bg-white shadow-md">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center">
            <h1 class="text-2xl font-bold text-red-600">
                <i class="fas fa-book-open mr-2"></i>Trung Tâm tiếng Trung Lý Thú
            </h1>
        </div>
        <nav class="hidden md:flex space-x-8">
            <a href="{{ route('welcome') }}" class="text-gray-800 hover:text-red-600">Trang chủ</a>
            <a href="{{ route('login') }}" class="text-gray-800 hover:text-red-600">Đăng nhập</a>
            <a href="{{ route('register') }}"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition duration-300">Đăng ký</a>
        </nav>
    </div>
</header>