<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class User extends Enum
{
    const TOKEN_REMEMBER_WEEK = 1;
    const WAITING_VERIFICATION = 1;
    const WAITING_PERSONAL_INFOR = 2;
    const ACTIVE = 2;
    const INACTIVE = 1;
    const EXPIRE_AFTER_HOURS = 24;
    const SORT_DEFAULT = 0;
    const TIME_EXPRIRE_VERIFY = 1; //day
    const PAYMENT_CREDIT_CARD = 2;
    const TAX_INDIVIDUAL = 1;
    const TAX_COMPANY = 2;
    const TYPE_BANNER_DEFAULT = 1;

    const ASC = 'asc';
    const DESC = 'desc';

    const ONE_STOP = 1;
    const NO_ONE_STOP = 0;

    public static function status($statusId = null)
    {
        switch ($statusId) {
            case self::WAITING_VERIFICATION:
                return 'Waiting verification';
            case self::WAITING_PERSONAL_INFOR:
                return 'Waiting personal information';
            case self::INACTIVE:
                return 'Inactive';
            case self::ACTIVE:
                return 'Active';
            default:
                return [
                    self::INACTIVE => 'Inactive',
                    self::ACTIVE => 'Active',
                    self::WAITING_VERIFICATION => 'Waiting verification',
                    self::WAITING_PERSONAL_INFOR => 'Waiting personal information',
                ];
        }
    }

    public static function sortDirection()
    {
        return [
            self::ASC,
            self::DESC,
        ];
    }

    public static function sortBy()
    {
        return [
            'id',
            'email',
            'first_name',
            'last_name',
            'status',
        ];
    }

    public static function statusValidate()
    {
        return [
            self::WAITING_VERIFICATION,
            self::WAITING_PERSONAL_INFOR,
            self::ACTIVE,
            self::INACTIVE,
        ];
    }
}
