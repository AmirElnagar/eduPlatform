<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'المنصة التعليمية') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <span class="text-2xl font-bold text-blue-600">📚 المنصة التعليمية</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center space-x-4 space-x-reverse">
                    @auth
                    <!-- User is logged in -->
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 space-x-reverse text-gray-700 hover:text-blue-600 focus:outline-none">
                                <img src="{{ auth()->user()->profile_image ? asset('storage/' . auth()->user()->profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=3B82F6&color=fff' }}"
                                    alt="{{ auth()->user()->name }}"
                                    class="w-8 h-8 rounded-full border-2 border-gray-300">
                                <span class="font-medium">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-cloak
                                class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-200 z-50">

                                @if(auth()->user()->role->value !== 'user')
                                <a href="{{ route('dashboard') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    لوحة التحكم
                                </a>
                                @endif

                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    الملف الشخصي
                                </a>

                                <hr class="my-1">

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-right px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- User is NOT logged in -->
                    <a href="{{ route('login') }}"
                        class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                        تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}"
                        class="bg-blue-600 text-white hover:bg-blue-700 px-4 py-2 rounded-md text-sm font-medium">
                        إنشاء حساب
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-br from-blue-600 to-blue-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    منصة تعليمية متكاملة
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-blue-100">
                    للمدرسين والطلاب وأولياء الأمور
                </p>
                <div class="flex justify-center gap-4">
                    @guest
                    <a href="{{ route('register') }}"
                        class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold transition">
                        ابدأ الآن مجاناً
                    </a>
                    @else
                    @if(auth()->user()->role->value !== 'user')
                    <a href="{{ route('dashboard') }}"
                        class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold transition">
                        اذهب إلى لوحة التحكم
                    </a>
                    @endif
                    @endguest

                    <a href="#features"
                        class="bg-blue-500 hover:bg-blue-400 text-white px-8 py-3 rounded-lg text-lg font-semibold transition">
                        تعرف أكثر
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-900">
                مميزات المنصة
            </h2>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- للمدرسين -->
                <div class="text-center p-6 bg-blue-50 rounded-lg hover:shadow-lg transition">
                    <div class="text-5xl mb-4">👨‍🏫</div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">للمدرسين</h3>
                    <ul class="text-gray-600 space-y-2 mb-4">
                        <li>✓ إدارة المجموعات بسهولة</li>
                        <li>✓ رفع الدروس والمحتوى</li>
                        <li>✓ إنشاء الامتحانات</li>
                        <li>✓ متابعة الحضور والغياب</li>
                    </ul>
                    {{-- هنحط لينك انضم كمدرس بعدين --}}
                </div>

                <!-- للطلاب -->
                <div class="text-center p-6 bg-green-50 rounded-lg hover:shadow-lg transition">
                    <div class="text-5xl mb-4">👨‍🎓</div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">للطلاب</h3>
                    <ul class="text-gray-600 space-y-2 mb-4">
                        <li>✓ الوصول للدروس والمحتوى</li>
                        <li>✓ حل الامتحانات أونلاين</li>
                        <li>✓ متابعة النتائج</li>
                        <li>✓ التفاعل مع المدرسين</li>
                    </ul>
                </div>

                <!-- لأولياء الأمور -->
                <div class="text-center p-6 bg-purple-50 rounded-lg hover:shadow-lg transition">
                    <div class="text-5xl mb-4">👨‍👩‍👦</div>
                    <h3 class="text-xl font-semibold mb-3 text-gray-900">لأولياء الأمور</h3>
                    <ul class="text-gray-600 space-y-2 mb-4">
                        <li>✓ متابعة الأبناء</li>
                        <li>✓ الاطلاع على النتائج</li>
                        <li>✓ تلقي الإشعارات</li>
                        <li>✓ التواصل مع المدرسين</li>
                    </ul>
                    {{-- هنحط لينك انضم كولي أمر بعدين --}}
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-blue-600 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">جاهز للبدء؟</h2>
            <p class="text-xl mb-8 text-blue-100">انضم الآن وابدأ رحلتك التعليمية</p>
            @guest
            <a href="{{ route('register') }}"
                class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold inline-block transition">
                إنشاء حساب مجاني
            </a>
            @else
            @if(auth()->user()->role->value !== 'user')
            <a href="{{ route('dashboard') }}"
                class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-3 rounded-lg text-lg font-semibold inline-block transition">
                اذهب إلى لوحة التحكم
            </a>
            @endif
            @endguest
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} المنصة التعليمية. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <style>
        [x-cloak] {
            display: none !important;
        }

        html {
            scroll-behavior: smooth;
        }
    </style>
</body>

</html>