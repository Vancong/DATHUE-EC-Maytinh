<!DOCTYPE html>
<html lang="vi" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản Trị – TechStore Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 antialiased text-slate-800">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl p-8 border border-slate-800/10">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white font-black text-xl flex items-center justify-center mx-auto mb-3 shadow-lg shadow-blue-500/25">
                TS
            </div>
            <h1 class="text-xl font-black tracking-tight text-slate-900">TechStore Admin Portal</h1>
            <p class="text-xs text-slate-500 mt-1">Đăng nhập tài khoản quản trị hệ thống</p>
        </div>

        <!-- Quick Demo Admin Fill -->
        <div class="mb-6 p-3.5 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-bold text-slate-900">Tài khoản Admin mặc định:</p>
                    <p class="text-[11px] text-slate-600 mt-0.5">admin@techstore.vn / password123</p>
                </div>
                <button type="button" onclick="fillAdminDemo()" class="px-2.5 py-1 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg text-[11px] transition-colors">
                    Điền nhanh
                </button>
            </div>
        </div>

        @if(session('error'))
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                @foreach($errors->all() as $err)
                    <p>{{ $err }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="admin-email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Email Quản Trị</label>
                <input 
                    type="email" 
                    id="admin-email" 
                    name="email" 
                    value="{{ old('email', 'admin@techstore.vn') }}" 
                    required 
                    autocomplete="email"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                >
            </div>

            <div>
                <label for="admin-password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Mật khẩu</label>
                <input 
                    type="password" 
                    id="admin-password" 
                    name="password" 
                    value="password123"
                    required 
                    autocomplete="current-password"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100"
                >
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span>Duy trì đăng nhập</span>
                </label>
                <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-700 font-medium">Về trang chủ</a>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl shadow-md transition-colors text-sm">
                Đăng Nhập Quản Trị
            </button>
        </form>
    </div>

    <script>
        function fillAdminDemo() {
            document.getElementById('admin-email').value = 'admin@techstore.vn';
            document.getElementById('admin-password').value = 'password123';
        }
    </script>
</body>
</html>

