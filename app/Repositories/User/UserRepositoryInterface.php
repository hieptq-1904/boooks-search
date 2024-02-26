<?php

namespace App\Repositories\User;

use App\Repositories\BaseRepositoryInterface;

/**
 * Interface UserRepositoryInterface.
 */
interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function getProfile($user);

    public function forgotPassword($email, $token);

    public function setVerifyCode($data);

    public function resetPassword($email, $password, $token);

    public function isValidTokenResetPassword($email, $token);

    public function confirmRegister($data);
}
