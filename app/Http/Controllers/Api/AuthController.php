<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SetupRequest;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     * Initial setup for Restaurant and Owner.
     */
    public function setup(SetupRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $slug = Str::slug($request->restaurant_name);
            if (Restaurant::where('slug', $slug)->exists()) {
                $slug .= '-'.Str::lower(Str::random(5));
            }

            // Create Restaurant
            $restaurant = Restaurant::create([
                'name' => $request->restaurant_name,
                'slug' => $slug,
                'is_active' => true,
            ]);

            // Ensure OWNER role exists
            Role::firstOrCreate(['name' => 'OWNER', 'guard_name' => 'web']);

            // Create Owner User
            $owner = User::create([
                'restaurant_id' => $restaurant->id,
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'password' => Hash::make($request->password),
            ]);

            // Assign OWNER role
            $owner->assignRole('OWNER');

            // Generate Sanctum access token
            $token = $owner->createToken('owner-token')->plainTextToken;

            return response()->json([
                'message' => 'Restaurant and owner setup successfully completed.',
                'token' => $token,
                'restaurant' => $restaurant,
                'user' => $owner,
                'roles' => $owner->getRoleNames(),
            ], 201);
        });
    }

    /**
     * Authenticate user and issue Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'restaurant' => $user->restaurant,
            'roles' => $user->getRoleNames(),
        ], 200);
    }

    /**
     * Get authenticated user details with restaurant and roles.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => $user,
            'restaurant' => $user->restaurant,
            'roles' => $user->getRoleNames(),
        ], 200);
    }

    /**
     * Revoke current Sanctum access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully.',
        ], 200);
    }
}
