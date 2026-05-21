<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function data(): JsonResponse
    {
        $users = User::orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'username'   => $u->username,
                'email'      => $u->email,
                'role'       => $u->role,
                'active'     => $u->active,
                'created_at' => $u->created_at?->toISOString(),
            ]);

        return response()->json(['success' => true, 'data' => $users]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user',
            'active'   => 'nullable|boolean',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'active'   => $request->boolean('active', true),
        ]);

        return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan.', 'data' => $user->only(['id', 'name', 'username', 'role', 'active'])]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'role'   => 'required|in:admin,user',
            'active' => 'nullable|boolean',
        ]);

        $updateData = [
            'name'   => $request->name,
            'email'  => $request->email,
            'role'   => $request->role,
            'active' => $request->boolean('active', true),
        ];

        if ($request->password) {
            $request->validate(['password' => 'string|min:6']);
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json(['success' => true, 'message' => 'User berhasil diupdate.']);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Tidak bisa menghapus akun sendiri.'], 422);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'User berhasil dihapus.']);
    }
}
