<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * @param Request $request
     * @param UserRepositoryInterface $userRepository
     * @return JsonResponse
     * @throws ValidationException
     */
    public function register(Request $request, UserRepositoryInterface $userRepository): JsonResponse
    {
        $this->validate($request, $this->rules());

        try {
            $user = $userRepository->create([
                ... $request->only('first_name', 'last_name', 'email', 'phone' ),
                ... ['password' => Hash::make($request->get('password'))]
            ]);
            return response()->json(['user' => $user, 'message' => 'User Registration Succeed.'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }
    }

    /**
     * @return string[]
     */
    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:55',
            'last_name' => 'required|string|max:55',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|max:30|min:6',
            'phone'=>'required|string|unique:users|digits_between:6,25'
        ];
    }
}
