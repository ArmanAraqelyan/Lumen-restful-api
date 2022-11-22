<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SignInController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function signIn(Request $request): JsonResponse
    {
        $this->validate($request, $this->rules());

        if (! $token = Auth::attempt($request->only(['email', 'password']), true)) {
            // Login has failed
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'user' => auth()->user(),
            'expires_in' => auth()->factory()->getTTL() * 60 * 24
        ]);
    }

    /**
     * @return string[]
     */
    protected function rules(): array
    {
        return [
            'email' => 'required|email|exists:users',
            'password' => 'required|string|max:30',
        ];
    }
}