@extends('layouts.admin')

@section('title', 'Quản Lý Tài Khoản Quản Trị – TechStore Admin')
@section('header_title', 'Tài Khoản Quản Trị Viên (Admins)')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <p class="text-xs text-slate-500">Danh sách nhân sự được cấp quyền đăng nhập và điều hành hệ thống TechStore</p>
        <a href="{{ route('admin.accounts.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs transition-colors flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Thêm Quản Trị Viên Mới
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-slate-700">
                <thead class="bg-slate-50 text-slate-500 uppercase text-[10px] tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3.5">Họ và tên</th>
                        <th class="px-5 py-3.5">Email quản trị</th>
                        <th class="px-5 py-3.5">Số điện thoại</th>
                        <th class="px-5 py-3.5">Vai trò</th>
                        <th class="px-5 py-3.5">Trạng thái</th>
                        <th class="px-5 py-3.5 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($admins as $adm)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 font-bold text-slate-900">
                                {{ $adm->name }}
                                @if($adm->id === Auth::id())
                                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[9px] bg-blue-100 text-blue-700 font-bold">Bạn</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 font-mono text-slate-600">{{ $adm->email }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $adm->phone ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-900 text-white uppercase tracking-wider">
                                    ADMIN
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($adm->is_active)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Hoạt động</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">Đã khóa</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-right">
                                @if($adm->id !== Auth::id())
                                    <form action="{{ route('admin.accounts.toggle-active', $adm->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Bạn có chắc chắn muốn thay đổi trạng thái tài khoản admin này?')" class="text-xs font-semibold {{ $adm->is_active ? 'text-rose-600 hover:text-rose-800' : 'text-emerald-600 hover:text-emerald-800' }}">
                                            {{ $adm->is_active ? 'Khóa quyền' : 'Kích hoạt' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 text-xs italic">Đang đăng nhập</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-8 text-center text-slate-400">Không có tài khoản quản trị nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $admins->links() }}
        </div>
    </div>

</div>
@endsection

