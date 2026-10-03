<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OperatorController extends Controller
{
    public function index(): View
    {
        $items = User::operator()->withCount('laporan')->orderBy('name')->get()->map(fn (User $u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'phone' => $u->phone,
            'active' => $u->is_active,
            'laporan_count' => $u->laporan_count,
        ])->all();

        return view('operator', ['items' => $items]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password' => Hash::make($request->validated('password')),
            'role' => Role::OPERATOR,
            'is_active' => true,
        ]);

        return redirect()->route('operator.index')->with('status', 'Akun operator dibuat');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        if ($request->filled('name')) {
            $user->name = $request->validated('name');
        }
        if ($request->filled('email')) {
            $user->email = $request->validated('email');
        }
        if ($request->has('phone')) {
            $user->phone = $request->validated('phone');
        }
        if ($request->has('active')) {
            $user->is_active = $request->boolean('active');
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->validated('password'));
        }
        $user->save();

        return redirect()->route('operator.index')->with('status', 'Data operator diperbarui');
    }
}
