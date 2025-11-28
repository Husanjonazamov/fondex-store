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

