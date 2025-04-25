<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Récupérer tous les utilisateurs.
     */
    public function getAllUsers(Request $request)
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Inscription d'un utilisateur.
     */
    public function register(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'secret_code' => 'nullable|string',
        ]);

        // Vérification du code secret pour attribuer le rôle
        $role = (!empty($validated['secret_code']) && $validated['secret_code'] === config('app.admin_secret_code')) ? 'admin' : 'user';

        // Création de l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ]);

        // Génération du token d'authentification
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'message' => 'Inscription réussie',
            'role' => $role
        ], 201);
    }

    /**
     * Connexion de l'utilisateur.
     */
    public function login(Request $request)
    {
        // Validation des informations de connexion
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Vérifier les informations d'identification
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Identifiants incorrects'
            ], 401);
        }

        // Récupération de l'utilisateur
        $user = User::where('email', $request->email)->firstOrFail();

        // Génération du token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'role' => $user->role, // Retourner le rôle de l'utilisateur
        ]);
    }

    /**
     * Déconnexion de l'utilisateur.
     */
    public function logout(Request $request)
    {
        // Suppression des tokens de l'utilisateur
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }
}
