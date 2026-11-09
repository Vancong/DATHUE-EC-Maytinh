<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminAccountController extends Controller
{
    public function index(): View
    {
        $admins = User::where('role', 'admin')->latest()->paginate(15);

        return view('admin.accounts.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admin.accounts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'name.required' => 'Họ tên không được để trống.',
            'email.required' => 'Email không được để trống.',
            'email.unique' => 'Email này đã tồn tại trong hệ thống.',
            'password.required' => 'Mật khẩu không được để trống.',
            'password.min' => 'Mật khẩu tối thiểu 6 ký tự.',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'role' => 'admin',
            'is_active' => true,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.accounts.index')->with('success', 'Thêm tài khoản quản trị mới thành công!');
    }

    public function toggleActive(User $admin): RedirectResponse
    {
        abort_if($admin->role !== 'admin', 404);

        if ($admin->id === Auth::id()) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        $admin->is_active = ! $admin->is_active;
        $admin->save();

        $statusStr = $admin->is_active ? 'mở khóa' : 'khóa';

        return back()->with('success', "Đã {$statusStr} tài khoản {$admin->name}.");
    }
}
