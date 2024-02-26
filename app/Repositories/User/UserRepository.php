<?php

namespace App\Repositories\User;

use App\Enums\User as EnumsUser;
use App\Exceptions\AuthException;
use App\Models\User;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $model;

    /**
     * @var DatabaseManager
     */
    private $db;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     * @param DatabaseManager $db
     */
    public function __construct(User $model, DatabaseManager $db)
    {
        parent::__construct($model);

        $this->db = $db;
    }

    public function getProfile($user)
    {
        return $user;
    }

    public function forgotPassword($email, $token)
    {
        $table = $this->db->table('password_resets');
        $table->where(['email' => $email])->delete();
        $table->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * @throws AuthException
     */
    public function resetPassword($email, $password, $token)
    {
        $updatePassword = $this->db->table('password_resets')->where([
            'email' => $email,
            'token' => $token,
        ]);
        $effectiveTime = Carbon::now()->subHours(EnumsUser::EXPIRE_AFTER_HOURS);
        if ($updatePassword->latest()->first() != null) {
            if (!$updatePassword->where('created_at', '>=', $effectiveTime)->latest()->first()) {
                $this->db->table('password_resets')->where(['email' => $email])->delete();
                return ['msg' => 'The confirmation code has been expired.', 'data' => []];
            }

            DB::beginTransaction();
            try {
                $queryUserByEmail = $this->model->where('email', $email);
                $queryUserByEmail->update(['password' => bcrypt($password)]);

                $this->db->table('password_resets')->where(['email' => $email])->delete();
                DB::commit();

                return ['msg' => 'The password has been reset.', 'data' => $queryUserByEmail->first()];
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Reset password failed! Error detail: ' . $e->getMessage());
                throw new AuthException('Reset password failed! Please try again.');
            }
        } else {
            return ['msg' => 'Invalid email or token.', 'data' => []];
        }
    }

    public function isValidTokenResetPassword($email, $token)
    {
        return (bool) $this->db->table('password_resets')
            ->where(['email' => Str::lower($email), 'token' => $token])->first();
    }

    /**
     * @throws AuthException
     */
    public function setVerifyCode($data)
    {
        $table = DB::table('verification_codes');
        DB::beginTransaction();
        try {
            $table->where(['email' => $data['email']])->delete();
            $table->insert(['email' => $data['email'], 'token' => $data['token'], 'created_at' => Carbon::now()]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Set verify code failed! Error detail: ' . $e->getMessage());
            throw new AuthException('Set verify code failed! Please try again.');
        }
    }

        /**
     * @throws AuthException
     */
    public function confirmRegister($data)
    {
        $expired = true;
        $invalid = true;

        $effectiveTime = Carbon::now()->subHours(EnumsUser::EXPIRE_AFTER_HOURS);
        $emailConfirmQuery = $this->db->table('verification_codes')->where(['email' => $data['email'], 'token' => $data['token']]);

        if (!$emailConfirmQuery->latest()->first()) {
            $invalid = false;

            return [
                'invalid' => $invalid,
                'expired' => $expired,
            ];
        }

        if (!$emailConfirmQuery->where('created_at', '>=', $effectiveTime)->latest()->first()) {
            $expired = false;

            return [
                'invalid' => $invalid,
                'expired' => $expired,
            ];
        }

        $queryByEmail = $this->model->where('email', $data['email']);
        DB::beginTransaction();
        try {
            $queryByEmail->update(['email_verified_at' => now()]);
            $this->db->table('verification_codes')->where(['email' => $data['email']])->delete();
            DB::commit();

            return $queryByEmail->first();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Confirm register failed. Detail error: ' . $e->getMessage());
            throw new AuthException('Confirm register failed! Please try again.');
        }
    }
}
