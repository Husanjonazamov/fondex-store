<?php

namespace App\Http\Services;

use App\Http\Services\Sms\SendService;
use Illuminate\Support\Facades\Log;

class SmsServices
{
    public function sendOtpSms($to, $code)
    {
        $sms     = "$code - Fondex tasdiqlash kodi. Hech kimga bermang!";
        $service = new SendService();

        try {
            $service->sendSms($to, $sms);
            Log::info('OTP sent successfully', ['phone' => $to, 'otp' => $code]);
        } catch (\Exception $e) {
            Log::error('OTP sending failed', [
                'phone' => $to,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
