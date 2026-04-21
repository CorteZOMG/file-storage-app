<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OAT;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

#[OAT\Tag(name: "Auth", description: "Authentication endpoints")]
class AuthController extends Controller
{
    #[OAT\Post(path: "/register", summary: "Register a new user", tags: ["Auth"])]
    #[OAT\RequestBody(required: true, content: new OAT\JsonContent(
        required: ["name", "email", "password"],
        properties: [
            new OAT\Property(property: "name", type: "string"),
            new OAT\Property(property: "email", type: "string", format: "email"),
            new OAT\Property(property: "password", type: "string", format: "password", minLength: 8)
        ]
    ))]
    #[OAT\Response(response: 201, description: "User successfully registered")]
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    #[OAT\Post(path: "/login", summary: "Log in a user", tags: ["Auth"])]
    #[OAT\RequestBody(required: true, content: new OAT\JsonContent(
        required: ["email", "password"],
        properties: [
            new OAT\Property(property: "email", type: "string", format: "email"),
            new OAT\Property(property: "password", type: "string", format: "password")
        ]
    ))]
    #[OAT\Response(response: 200, description: "Successful login")]
    #[OAT\Response(response: 422, description: "Validation error")]
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    #[OAT\Post(path: "/logout", summary: "Log out user", tags: ["Auth"], security: [["bearerAuth" => []]])]
    #[OAT\Response(response: 200, description: "Successful logout")]
    #[OAT\Response(response: 401, description: "Unauthenticated")]
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    #[OAT\Get(path: "/user", summary: "Get current user", tags: ["Auth"], security: [["bearerAuth" => []]])]
    #[OAT\Response(response: 200, description: "Current user details")]
    public function user(Request $request)
    {
        return $request->user();
    }
}
