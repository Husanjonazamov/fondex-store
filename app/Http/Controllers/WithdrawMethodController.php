<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;

class WithdrawMethodController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUsers::where('user_id', $id)->first();
        $id = $exist->uuid;
        return view("withdraw_method.index")->with('id', $id);

    }
    public function create()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUsers::where('user_id', $id)->first();
        $id = $exist->uuid;
        return view("withdraw_method.create")->with('id', $id);

    }


}