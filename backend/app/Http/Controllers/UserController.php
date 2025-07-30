<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users (Admin only).
     */
    public function index(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $users = User::orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }

    /**
     * Store a newly created user (Admin only).
     */
    public function store(Request $request)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'balance' => 'required|numeric|min:0',
            'bot_balance' => 'required|numeric|min:0',
            'role' => 'required|in:user,admin',
            'status' => 'required|in:active,suspended,pending',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'balance' => $request->balance,
            'bot_balance' => $request->bot_balance,
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return response()->json($user, 201);
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, User $user)
    {
        // Users can only see their own profile, admins can see any
        if (!$request->user()->isAdmin() && $request->user()->id !== $user->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        return response()->json($user);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        // Users can only update their own profile, admins can update any
        if (!$request->user()->isAdmin() && $request->user()->id !== $user->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $rules = [
            'name' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'balance' => 'sometimes|numeric|min:0',
            'bot_balance' => 'sometimes|numeric|min:0',
        ];

        // Only admins can update role and status
        if ($request->user()->isAdmin()) {
            $rules['role'] = 'sometimes|in:user,admin';
            $rules['status'] = 'sometimes|in:active,suspended,pending';
        }

        $request->validate($rules);

        $updateData = $request->only(['name', 'email', 'balance', 'bot_balance']);

        // Only admins can update role and status
        if ($request->user()->isAdmin()) {
            $updateData = array_merge($updateData, $request->only(['role', 'status']));
        }

        $user->update($updateData);

        return response()->json($user);
    }

    /**
     * Remove the specified user (Admin only).
     */
    public function destroy(Request $request, User $user)
    {
        if (!$request->user()->isAdmin()) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        // Prevent admin from deleting themselves
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'Você não pode excluir sua própria conta'], 400);
        }

        $user->delete();

        return response()->json(['message' => 'Usuário excluído com sucesso']);
    }

    /**
     * Update user balance.
     */
    public function updateBalance(Request $request, User $user)
    {
        // Users can only update their own balance, admins can update any
        if (!$request->user()->isAdmin() && $request->user()->id !== $user->id) {
            return response()->json(['message' => 'Acesso negado'], 403);
        }

        $request->validate([
            'balance' => 'sometimes|numeric|min:0',
            'bot_balance' => 'sometimes|numeric|min:0',
        ]);

        $user->update($request->only(['balance', 'bot_balance']));

        return response()->json($user);
    }
}