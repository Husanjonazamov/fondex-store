<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;

class MySubscriptionsController extends Controller
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
        return view("my_subscriptions.index")->with('id', $id);
    }

    public function show($id)
    {
        return view('my_subscriptions.show')->with('id', $id);
    }

   
}


