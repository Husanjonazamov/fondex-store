<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\SmsServices;
use App\Models\User;
use App\Models\VendorUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AjaxController extends Controller
{
    public function setToken(Request $request)
    {
        $uuid = $request->id;
        $email = trim((string) $request->email);
        $password = (string) $request->password;
        $passwordToStore = $password !== '' ? $password : Str::random(32);

        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $email,
                'email' => $email,
                'password' => Hash::make($passwordToStore),
                'isSubscribed' => $request->isSubscribed,
            ]);
        } else {
            $user->update([
                'isSubscribed' => ($request->isSubscribed == null) ? '' : $request->isSubscribed,
            ]);
        }

        $vendorUser = VendorUsers::where('email', $email)->first();
        if (!$vendorUser) {
            DB::table('vendor_users')->insert([
                'user_id' => $user->id,
                'uuid' => $uuid,
                'email' => $email,
            ]);
        } else {
            DB::table('vendor_users')
                ->where('id', $vendorUser->id)
                ->update([
                    'user_id' => $user->id,
                    'uuid' => $uuid,
                    'email' => $email,
                ]);
        }

        Auth::login($user, true);

        $data = [];
        if (Auth::check()) {
            $data['access'] = true;
        }

        return $data;
    }

    public function sendOtp(Request $request)
    {
        $phone = $request->phone;
        $otp = rand(100000, 999999);

        session(["otp_$phone" => $otp, "otp_time_$phone" => now()->timestamp]);

        try {
            (new SmsServices())->sendOtpSms($phone, $otp);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function verifyOtp(Request $request)
    {
        $phone = $request->phone;
        $otp = $request->otp;

        $savedOtp = session("otp_$phone");
        $savedTime = session("otp_time_$phone");

        if (!$savedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP topilmadi. Qayta yuboring.'], 422);
        }

        if (now()->timestamp - $savedTime > 300) {
            return response()->json(['success' => false, 'message' => 'OTP muddati tugagan (5 daqiqa).'], 422);
        }

        if ((string) $savedOtp !== (string) $otp) {
            return response()->json(['success' => false, 'message' => 'OTP noto\'g\'ri.'], 422);
        }

        session()->forget(["otp_$phone", "otp_time_$phone"]);

        return response()->json(['success' => true]);
    }

    public function saveVendorFirestoreId(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        DB::table('vendor_users')
            ->where('user_id', $user->id)
            ->update(['firestore_vendor_id' => $request->firestore_vendor_id]);

        return response()->json(['success' => true]);
    }

    public function setSubcriptionFlag(Request $request)
    {
        session_write_close();

        User::where('email', $request->email)->update([
            'isSubscribed' => $request->isSubscribed,
        ]);

        $data = [];
        if (Auth::check()) {
            $data['access'] = true;
        }

        return $data;
    }

    public function logout(Request $request)
    {
        $user_id = Auth::user()->user_id;
        $user = VendorUsers::where('user_id', $user_id)->first();

        try {
            Auth::logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }

        $data1 = [];
        if (!Auth::check()) {
            $data1['logoutuser'] = true;
        }

        return $data1;
    }
}
