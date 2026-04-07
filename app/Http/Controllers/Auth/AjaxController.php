<?php

/**

 * File name: AjaxController.php

 * Last modified: 2022.06.11 at 16:10:52

 * Author:Siddhi

 * Copyright (c) 2022

 */



namespace App\Http\Controllers\Auth;



use App\Models\VendorUsers;

use App\Models\User;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Http\Services\SmsServices;

use Laravel\Socialite\Facades\Socialite;

use Prettus\Validator\Exceptions\ValidatorException;



class AjaxController extends Controller

{





    public function setToken(Request $request){

        $isSubscribed = $request->isSubscribed;

        $userId = $request->userId;

        $uuid = $request->id;

        $password=$request->password;

        $exist = VendorUsers::where('email',$request->email )->get();

        $data = $exist->isEmpty();

       

        if($exist->isEmpty()){

           

            $user=User::create([

                'name' => $request->email,

                'email' => $request->email,

                'password' => Hash::make($password),

                'isSubscribed' => $request->isSubscribed

            ]);



             DB::table('vendor_users')->insert([

                'user_id' => $user->id,

                'uuid' => $uuid,

                'email' => $request->email,

            ]);

           



        }else {
            $user = DB::table('vendor_users')->select('id')->where('email', $request->email)->first();
                    DB::table('vendor_users')->where('id', $user->id)
                    ->update([
                    'uuid' => $uuid,
                    'email' => $request->email
                    ]);

        }

        User::where('email', $request->email)->update([

            'isSubscribed' => ($request->isSubscribed == null) ? '' : $request->isSubscribed

        ]);

        $user = User::where('email',$request->email)->first();

    

       Auth::login($user,true);

       $data = array();

       if(Auth::check()){



            $data['access'] = true;

       }



       

        return $data;

    }

    public function sendOtp(Request $request)
    {
        $phone = $request->phone; // e.g. "+998901234567"

        $otp = rand(100000, 999999);

        // OTP ni session ga saqlaymiz (5 daqiqa)
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
        $otp   = $request->otp;

        $savedOtp  = session("otp_$phone");
        $savedTime = session("otp_time_$phone");

        if (!$savedOtp) {
            return response()->json(['success' => false, 'message' => 'OTP topilmadi. Qayta yuboring.'], 422);
        }

        if (now()->timestamp - $savedTime > 300) {
            return response()->json(['success' => false, 'message' => 'OTP muddati tugagan (5 daqiqa).'], 422);
        }

        if ((string)$savedOtp !== (string)$otp) {
            return response()->json(['success' => false, 'message' => 'OTP noto\'g\'ri.'], 422);
        }

        // OTP to'g'ri - sessiondan o'chiramiz
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

        User::where('email', $request->email)->update([

            'isSubscribed' => $request->isSubscribed

        ]);



        $data = array();

        if (Auth::check()) {

            $data['access'] = true;

        }





        return $data;

    }



    public function logout(Request $request){



        $user_id = Auth::user()->user_id;

        $user = VendorUsers::where('user_id',$user_id)->first();



        try {

            Auth::logout();

        } catch (\Exception $e) {

              $this->sendError($e->getMessage(), 401);

        }

        

        $data1 = array();

        if(!Auth::check()){

          $data1['logoutuser'] = true;

        }

        return $data1;

    }





}

