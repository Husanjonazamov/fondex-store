<?php
/**
 * File name: RestaurantController.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\VendorUsers;
class DocumentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function DocumentList()
    {
        $user = Auth::user();
        $id = Auth::id();
        $exist = VendorUsers::where('user_id', $id)->first();
        $id = $exist->uuid;
        return view("documents.index")->with('id', $id);
    }
    public function DocumentUpload($id)
    {
        $user = Auth::user();
        $userId = Auth::id();
        $exist = VendorUsers::where('user_id', $userId)->first();
        $vendorId = $exist->uuid;
        return view("documents.document_upload", compact('vendorId', 'id'));
    }
}
