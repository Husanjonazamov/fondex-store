<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\VendorUsers;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUsers::where('user_id',$id)->first();
        $id=$exist->uuid;
        return view('home')->with('id',$id);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        return view('welcome');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    public function users()
    {
        return view('users');
    }

    public function storeServiceFile(Request $request){
		if(!empty($request->serviceJson) && !Storage::disk('local')->has('firebase/credentials.json')){
			Storage::disk('local')->put('firebase/credentials.json',file_get_contents(base64_decode($request->serviceJson)));
		}
	}

}
