<?php

namespace App\Traits;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ResetsPasswords
{
    /**
     * @var array|string[]
     */
    protected array $messages = [
        PasswordBroker::RESET_LINK_SENT => 'Password reset token was sent. Please check your email.',
        PasswordBroker::PASSWORD_RESET => 'Password was successfully updated',
        PasswordBroker::INVALID_TOKEN => 'Password reset token was expired.',
        PasswordBroker::INVALID_USER => 'Invalid user email.',
        PasswordBroker::RESET_THROTTLED => 'Please try later. Throttle limit exceeded.'
    ];

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $this->validateEmail($request);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResponse($request, $response)
            : $this->sendFailedResponse($request, $response);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function reset(Request $request): JsonResponse
    {
        $this->validate($request, $this->resetRules());

        $response = Password::broker()->reset(
            $request->only( 'email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) { $this->resetPassword($user, $password); }
        );

        return $response == Password::PASSWORD_RESET
            ? $this->sendResponse($request, $response)
            : $this->sendFailedResponse($request, $response);
    }

    /**
     * Reset the given user's password.
     *
     * @param CanResetPassword $user
     * @param string $password
     * @return void
     */
    protected function resetPassword(CanResetPassword $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        event(new PasswordReset($user));
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function resetRules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    /**
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function validateEmail(Request $request): void
    {
        $this->validate($request, ['email' => 'required|email']);
    }

    /**
     * @param Request $request
     * @param $response
     * @return JsonResponse
     */
    protected function sendResponse(Request $request, $response): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => $this->getResponseMessage($response)
        ]);
    }

    /**
     * @param Request $request
     * @param $response
     * @return JsonResponse
     */
    protected function sendFailedResponse(Request $request, $response): JsonResponse
    {
        return response()->json([
            'status' => 'failed',
            'errors' => $this->getResponseMessage($response)
        ]);
    }

    /**
     * @param string $response
     * @return string
     */
    protected function getResponseMessage(string $response):string
    {
        return $this->messages[$response] ?? $response;
    }
}