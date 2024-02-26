<?php

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

function api_response($data, $responseCode,  $message, $statusCode)
{
    return response()->json([
        'code' => $responseCode,
        'message' => $message,
        'data' => $data,
    ], $statusCode);
}

function lowercaseStatus($status)
{
    return strtolower(str_replace('STATUS_', '', $status));
}

if (!function_exists('getEnglishDatetime')) {
    function getEnglishDatetime()
    {
        $jpNow = Carbon::now('Asia/Tokyo');

        return formatDateTime($jpNow, 'M d, Y - H:i:s');
    }
}

function formatDateTime($datetime, $format = 'Y/m/d H:i:s')
{
    if (!$datetime) {
        return null;
    }

    $date = str_replace('/', '-', $datetime);

    return date($format, strtotime($date));
}
