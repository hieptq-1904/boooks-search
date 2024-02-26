<?php

namespace App\Services;

use App\Enums\User;
use App\Enums\UserRole;
use App\Exceptions\AuthException;
use App\Jobs\SendEmailForgotPasswordJob;
use App\Jobs\SendEmailRegisterJob;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        UserRepositoryInterface $userRepository
    ) {
        $this->userRepository = $userRepository;
    }


    public function login($data)
    {
        $user = Auth::user();
        $token = $user->createToken('apiToken')->plainTextToken;
        $user->access_token = $token;
        return $user;
    }

    public function getProfile($user)
    {
        return $this->userRepository->getProfile($user);
    }

    public function findByEmail($email)
    {
        return $this->userRepository->findByField('email', $email);
    }

    public function confirmChangeEmail($data)
    {
        return $this->userRepository->confirmChangeEmail($data);
    }

    /**
     * @throws AuthException
     */
    public function forgotPassword($email, $role)
    {
        $token = Str::random(6);
        $user = $this->userRepository->findByField('email', $email);
        $name = $user->first_name . ' ' . $user->last_name;
        $time = Carbon::now()->format('Y/m/d - H:i:s');
        DB::beginTransaction();
        try {
            $this->userRepository->forgotPassword($email, $token);
            if ($user->hasRole($role) || $user->hasRole(UserRole::ADMIN)) {
                if ($role == UserRole::ADMIN) {
                    // dispatch(new SendEmailAdminForgotPasswordJob($email, $token, $name, $time, getLanguageId()));
                } else {
                    dispatch(new SendEmailForgotPasswordJob($email, $token, $name));
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Forgot password failed! Error detail: ' . $e->getMessage());
            throw new AuthException('Forgot password failed! Please try again.');
        }
    }

    public function resetPassword($email, $password, $token)
    {
        return $this->userRepository->resetPassword($email, $password, $token);
    }

    public function isValidTokenResetPassword($email, $token)
    {
        return $this->userRepository->isValidTokenResetPassword($email, $token);
    }

    public function register($values)
    {

        $values['password'] = bcrypt($values['password']);

        return $user;
        try {
            DB::beginTransaction();
            $user = $this->userRepository->create($values);
            $user->assignRole(UserRole::USER);
            $this->sendEmailRegister($values);
            Log::info('Register account user successfully!');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Register account user failed. Error detail: ' . $e->getMessage());
            throw new AuthException('Register account user failed! Please try again.');
        }
    }

    public function resendRegisterEmail($email)
    {
        $data = [
            'email' => $email,
        ];

        $user = $this->userRepository->findByField('email', $email);

        if ($user->status != User::WAITING_VERIFICATION) {
            throw new AuthException('Resent email failed! The account has already been verified.');
        }

        if (is_null($user->updated_at) || Carbon::now()->subMinutes(3) > Carbon::parse($user->updated_at)) {
            $this->sendEmailRegister($data);
            $user->touch();

            return true;
        }

        return false;
    }

    public function sendEmailRegister($data)
    {
        $data['time_now'] = Carbon::now()->format('Y/m/d - H:i:s');
        $user = $this->userRepository->findByField('email', $data['email']);
        $data['name'] = $user->first_name . ' ' . $user->last_name;
        $data['token'] = Str::random(6);
        $this->userRepository->setVerifyCode($data);
        dispatch(new SendEmailRegisterJob($data));
    }

    public function confirmRegister($data)
    {
        return $this->userRepository->confirmRegister($data);
    }
}
