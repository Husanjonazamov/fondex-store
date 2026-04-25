<?php

namespace App\Http\Controllers;

use App\Mail\SetEmailData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Redirect;

class SendEmailController extends Controller
{
    public function __construct()
    {
    }


    function sendMail(Request $request)
    {
        $data       = $request->all();
        $subject    = $data['subject'] ?? '';
        $message    = base64_decode($data['message'] ?? '');
        $recipients = $data['recipients'];

        $fromAddress = config('mail.from.address');
        if (empty($fromAddress)) {
            return response()->json(['success' => true, 'message' => 'Mail not configured, skipped.']);
        }

        try {
            Mail::to($recipients)->send(new SetEmailData($subject, $message));
            return response()->json(['success' => true, 'message' => 'email sent successfully!']);
        } catch (\Exception $e) {
            \Log::warning('SendEmailController: mail failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => true, 'message' => 'Mail skipped: ' . $e->getMessage()]);
        }
    }
}

?>