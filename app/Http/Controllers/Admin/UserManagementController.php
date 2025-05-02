<?php
// app/Http/Controllers/Admin/UserManagementController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        // show all operators & verifiers (but not admins)
        $users = User::where('is_admin', false)
                     ->orderBy('name')
                     ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', [
            'user' => new User(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8|confirmed',
            'is_operator' => '',
            'is_verifier' => '',
        ]);

        $data['is_operator'] = $request->has('is_operator') ? 1 : 0;
        $data['is_verifier'] = $request->has('is_verifier') ? 1 : 0;

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        $users = User::where('is_admin', false)
        ->orderBy('name')
        ->paginate(20);

        return view('admin.users.index', compact('users'))
                         ->with('success', 'کاربر با موفقیت ایجاد شد.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email,' . $user->id,
            'password'    => 'nullable|string|min:8|confirmed',
            'is_operator' => '',
            'is_verifier' => '',
        ]);

        // Convert checkboxes to 1/0
        $data['is_operator'] = $request->has('is_operator') ? 1 : 0;
        $data['is_verifier'] = $request->has('is_verifier') ? 1 : 0;

        if ($data['password'] ?? false) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $users = User::where('is_admin', false)
        ->orderBy('name')
        ->paginate(20);

        return view('admin.users.index', compact('users'))
                         ->with('success', 'اطلاعات کاربر با موفقیت به‌روز شد.');
    }

    public function destroy(User $user)
    {
        // prevent accidental admin deletion
        if ($user->is_admin) {
            return back()->withErrors(['error' => 'ادمن را نمی‌توان حذف کرد.']);
        }

        $user->delete();

        return back()->with('success', 'کاربر حذف شد.');
    }
}
