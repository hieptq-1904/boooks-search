<?php

namespace App\Http\Controllers;

use App\Enums\User;
use App\Enums\UserRole;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ConfirmRegisterRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(LoginRequest $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return $this->responseError([], 'Invalid email or password.', Response::HTTP_UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userService->login($request->only(['email', 'password']));

        return $user !== false ?
            $this->responseSuccess(new UserResource($user), 'Login successfully!') :
            $this->responseError([], 'Login failed!', Response::HTTP_UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->responseSuccess([], 'Logout successfully!');
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->userService->forgotPassword($request->email, UserRole::USER);

        return $this->responseSuccess([], 'A confirmation link has been sent to your email address.');
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = $this->userService->resetPassword($request->email, $request->password, $request->token);

        if (empty($user['data'])) {
            return $this->responseError(
                [],
                $user['msg'],
                Response::HTTP_BAD_REQUEST,
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->responseSuccess(new UserResource($user['data']), $user['msg']);
    }

    public function verifyTokenResetPassword(Request $request)
    {
        $isValid = $this->userService->isValidTokenResetPassword($request->email, $request->token);
        if ($isValid) {
            return $this->responseSuccess(
                [],
                'Valid password reset information.'
            );
        }

        return $this->responseError(
            [],
            'Invalid password reset information.',
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_BAD_REQUEST
        );
    }

    public function profile()
    {
        return $this->responseSuccess(new UserResource(auth()->user()), 'Fetched user information successfully!');
    }

    public function register(UserRequest $request)
    {

        $user = $this->userService->register($request->only([
            'email',
            'password',
            'name',
        ]));

        return $this->responseSuccess(new UserResource($user), 'Please check your email address and complete the registration within 24 hours.');
    }

    public function confirmRegister(ConfirmRegisterRequest $request)
    {
        $user = $this->userService->confirmRegister($request->only([
            'email',
            'token',
        ]));

        if (isset($user['invalid']) && $user['invalid']) {
            return $this->responseError([], 'Invalid token or email.', Response::HTTP_UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);
        }

        if (isset($user['expired']) && $user['expired']) {
            return $this->responseError([], 'The confirmation code has been expired.', Response::HTTP_UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);
        }

        return $this->responseSuccess(new UserResource($user), 'Your account has been confirmed successfully!');
    }

    public function resendRegisterEmail(ResendRegisterEmailRequest $request)
    {
        $response = $this->userService->resendRegisterEmail($request->email);

        if ($response) {
            return $this->responseSuccess([], 'The email has been sent back successfully!');
        }

        return $this->responseError(
            [],
            'The email has been sent back failed. Please resend it in 3 minutes.',
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_BAD_REQUEST
        );
    }
}
