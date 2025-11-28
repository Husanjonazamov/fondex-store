<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorUsers;

class AdvertisementsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUsers::where('user_id',$id)->first();
        $id=$exist->uuid;

        return view("advertisements.index")->with('id',$id);
    }

    public function create()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUsers::where('user_id',$id)->first();
        $id=$exist->uuid;
        return view('advertisements.create')->with('id',$id);
    }
    public function edit($id)
    {
        return view('advertisements.edit')->with('id', $id);
    }
    public function view($id)
    {
        return view('advertisements.view')->with('id', $id);
    }
    public function chat($id)
    {
        return view('advertisements.chat')->with('id', $id);
    }

}